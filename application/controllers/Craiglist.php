<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Craiglist extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('diagnostic_model');
		$this->scraped_date=date('Y-m-d h:i:s');
    }

    public function index($cronSID='',$scheduleID='') {

		
		//return false;
		$this->load->library('curl');
        
        if(empty($cronSID)){
		    $sid=$this->input->get('sid');   
		}else{
		    $sid=$cronSID;
		}
		$this->bot_id=$sid;

		if(!empty($sid)){
		    
		    $condition=array(
				'keyword_id' => $sid
			);
			$searchData=$this->common_model->get_all_searches($condition);
			$this->website_id=$searchData[0]['website_id'];
			$manualScheduleId=0;
		    if(!empty($scheduleID)){
		        $dataToUpdate=Array();
		        $dataToUpdate['status']='S';
		        $dataToUpdate['start_time']=date('Y-m-d H:i:s');	
		        $updateCondition=Array();
		        $updateCondition['schedule_id']=$scheduleID;
				$this->schedule_id=$scheduleID;
		        $this->common_model->update($dataToUpdate, 'schedules',$updateCondition);   
		    }else{
				$dataToInsert=Array();
				$dataToInsert['website_id']=$searchData[0]['website_id'];	
				$dataToInsert['sr_id']=0;	
				$dataToInsert['keyword_id']=$sid;	
				$dataToInsert['status']='S';	
				$dataToInsert['user_id']=$this->session->userdata('id');	
				$dataToInsert['scheduled_time']=date('Y-m-d H:i:s');	
				$dataToInsert['start_time']=$dataToInsert['scheduled_time'];	
				$dataToInsert = $this->security->xss_clean($dataToInsert);
				$manualScheduleId=$this->common_model->insert($dataToInsert, 'schedules');
				$this->schedule_id=$manualScheduleId;
			}
			/* 
				Create log start 
			*/
			$this->insertLogData=Array();
			$this->insertLogData['bot_id']=$this->bot_id;
			$this->insertLogData['schedule_id']=$this->schedule_id;
			$this->insertLogData['message']='Started the process.';
			$this->insertLogData['status']='1';
			$this->insertLogData['url']='';
			$this->insertLogData['log_time']=date('Y-m-d H:i:s');
			$this->diagnostic_model->insertLog($this->insertLogData,'diagnostic_log');

			/* 
				Create log end 
			*/

			
		   	$condition = array(
				'website_id' => $searchData[0]['website_id'],
				'status' => 'Y'
			);
			/* Check for assigned locations  */
			$locationIDs= explode(',', $searchData[0]['locations']);
			$websiteLists = $this->common_model->get_all_suburls($condition,$locationIDs);

			$condition = array(
				'website_id' => $searchData[0]['website_id'], 
				'status' => 'Y'
			);
			
			$keywords = $this->db->get_where('keywords',array('keyword_id'=>$sid))->row()->keywords;
			$keywords=str_replace(',','|',$keywords);

			$conditionIn= explode(',',$searchData[0]['subcategories']);
			$categoryLists = $this->common_model->get_all_subcategories($condition,$conditionIn);
			$posted_date_start=$searchData[0]['posted_date_start'];
			if($posted_date_start=='0000-00-00'){
				$posted_date_start='';
			}
			$posted_date_end=$searchData[0]['posted_date_end'];
			if($posted_date_end=='0000-00-00'){
				$posted_date_end='';
			}
			
			$counter=1;
			#$this->writeToFile('log.txt',"");
			foreach($websiteLists as $websiteList){
				$total=sizeof($websiteLists);
				
				$data = array(
					'progress' => "Scraping directory# $counter out of $total"
				);
				$condition = array(
					'keyword_id' => $sid
				);
				
				$data = $this->security->xss_clean($data);
				$keywordId = $this->common_model->update($data, 'keywords',$condition);
				
				#$websiteList['sub_url']='https://newyork.craigslist.org';
				foreach($categoryLists as $categoryList){
					$url=$websiteList['sub_url'].'/search/'.$categoryList['dir_name'].'?query='.urlencode($keywords);
					$resultFile=$this->fetchPage($url);
					$this->fetchListings($resultFile,$categoryList,$keywords,$websiteList,$posted_date_start,$posted_date_end,$sid);	
					#exit;
				}		
				
				$counter++;
				/*if($counter>50){
				    if(!empty($this->schedule_id)){
        		        $dataToUpdate=Array();
        		        $dataToUpdate['status']='C';
						$dataToUpdate['completion_time']=date('Y-m-d H:i:s');
        		        $updateCondition=Array();
        		        $updateCondition['schedule_id']=$this->schedule_id;						
        		        $this->common_model->update($dataToUpdate, 'schedules',$updateCondition);   
        		    }
					exit;
				}*/
			}
			$data = array(
					'progress' => "Scraping Completed..."
			);
			$condition = array(
				'keyword_id' => $sid
			);
			
			$data = $this->security->xss_clean($data);
			$keywordId = $this->common_model->update($data, 'keywords',$condition);
			if(!empty($this->schedule_id)){
		        $dataToUpdate=Array();
		        $dataToUpdate['status']='C';
		        $dataToUpdate['completion_time']=date('Y-m-d H:i:s');
		        $updateCondition=Array();
		        $updateCondition['schedule_id']=$this->schedule_id;
		        $this->common_model->update($dataToUpdate, 'schedules',$updateCondition);  
		        $this->pushNotification($this->schedule_id);
		    }
			/*else{
				//print $manualScheduleId;
				if(!empty($manualScheduleId)){					
					$dataToUpdate=Array();
					$dataToUpdate['status']='C';
					$dataToUpdate['completion_time']=date('Y-m-d H:i:s');
					$updateCondition=Array();
					$updateCondition['schedule_id']=$manualScheduleId;
					$this->common_model->update($dataToUpdate, 'schedules',$updateCondition);
					$this->pushNotification($manualScheduleId);
				}
			}*/
			$this->sendEmail($searchData[0]);
			echo "Scraping Completed...";		
		}else{
			echo "Some error Occured!!...";		
		}
		
	}
	function fetchListings($resultFile,$categoryList,$keywords,$websiteList,$posted_date_start,$posted_date_end,$sid){

		if(preg_match('/Here are some from nearby areas\./is',$resultFile,$matcher)){
			$resultFile=$this->before($matcher[0],$resultFile);
		}
		while(preg_match('/<li\s*class="result-row"\s*data-pid="\d+".*?>\s*<a\s*href="(.*?)"/is',$resultFile,$matcher)){
			$resultFile=$this->after($matcher[0],$resultFile);
			$url=$matcher[1];
			$postDateWebsite='';
			if(preg_match('/<time\s*class="result-date"\s*datetime="(\d\d\d\d-\d\d-\d\d)\s+/is',$resultFile,$matcher)){
				$postDateWebsite=$matcher[1];
			}else{
				$this->insertLogData['message']="Failed to scraped posted date, Change in website.";
				$this->insertLogData['status']='0';		
				$this->insertLogData['url']='';
				$this->insertLogData['proxy']='';
				$this->insertLogData['log_time']=date('Y-m-d H:i:s');
				$this->diagnostic_model->insertLog($this->insertLogData,'diagnostic_log');
		
				$dataToUpdate=Array();
				$dataToUpdate['last_updated']=date('Y-m-d H:i:s');	
				$dataToUpdate['status']='F';
				$dataToUpdate['completion_time']=$dataToUpdate['last_updated'];
				$updateCondition=Array();
				$updateCondition['schedule_id']=$this->schedule_id;				
				$this->common_model->update($dataToUpdate, 'schedules',$updateCondition);
				exit;
			}
			if(!empty($posted_date_start)){
				if(!empty($posted_date_end)){
					if(!$this->checkDateBetween($postDateWebsite,$posted_date_start,$posted_date_end)){
						continue;
					}
				}elseif($postDateWebsite != $posted_date){
					continue;
				}
			}
			
			#Skip post if older than 60 days
			$records_to_keep=$this->db->get_where('websites',array('id'=>'1'))->row()->records_to_keep;
			$dateDiff=svi_datediff($postDateWebsite,date('Y-m-d'));

			if($dateDiff>$records_to_keep){
				continue;
			}
			
			$resultId = $this->db->get_where('results',array('job_url'=>$url))->row()->result_id;
			if(empty($resultId)){
				$this->scrapeData($url,$categoryList,$keywords,$websiteList,$sid);
			}
		}
	}

	function scrapeData($url,$categoryList,$keywords,$websiteList,$sid){
		$insertResult=0;
		$resultFile=$this->fetchPage($url);
		
		$resultData=Array();
		$resultData['job_url']=$url;
		$resultData['website_id']=$categoryList['website_id'];
		$resultData['suburl_id']=$websiteList['suburl_id'];
		$resultData['sub_category_id']=$categoryList['sub_cat_id'];
		$resultData['keywords']=$keywords;
		if(preg_match('/<span id="titletextonly">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['title']=$this->removeSpaces($matcher[1]);
			$resultData['title']=html_entity_decode($resultData['title']);
			
		}
		if(preg_match('/<p\s*class="postinginfo reveal">posted:\s*<time\s*class="date\s*timeago"\s*datetime="(\d\d\d\d-\d\d-\d\d)/is',$resultFile,$matcher)){
			$resultData['posted_date']=$matcher[1];
		}
		if(preg_match('/<div\s*class="print-qrcode".*?>(.*?)<div\s*class="postinginfos">/is',$resultFile,$matcher)){
			$resultData['description']=$matcher[1];
			$resultData['description']=strip_tags($resultData['description'],'');
			$resultData['description']=html_entity_decode($resultData['description']);
			$resultData['description']=$this->removeSpaces($resultData['description']);
		}
		if(preg_match('/<span>compensation:\s*(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['compensation']=$matcher[1];
			$resultData['compensation']=strip_tags($resultData['compensation'],'');
		}
		
		if(!empty($this->input->get('mak')) && $this->input->get('mak')=='Y'){
			$resultData['exact_match']='Y';
		}else{
			$resultData['exact_match']='N';
		}
		$resultFile=strip_tags($resultFile,'');
		
		#Skip post if older than 60 days
		$records_to_keep=$this->db->get_where('websites',array('id'=>'1'))->row()->records_to_keep;
		$dateDiff=svi_datediff($resultData['posted_date'],date('Y-m-d'));
		if($dateDiff>$records_to_keep){
			return;
		}

		if($this->checkKeywords($resultFile,$keywords)){
			$resultData['keywords']=str_replace('|',',',$keywords);	
			$resultData['scraped_date']=$this->scraped_date;	
			$resultData['search_id']=$sid;	
			$resultData = $this->security->xss_clean($resultData);
			$this->common_model->insert($resultData, 'results');
		}
	}
	function checkKeywords($resultFile,$keywords){
		if(empty($this->input->get('mak')) || $this->input->get('mak')=='N'){
			if(preg_match("/($keywords)/is",$resultFile,$match2)){
				return true;
			}
		}elseif($this->input->get('mak')=='Y'){

			$keywordList=explode('|',$keywords);
			foreach($keywordList as $key =>$value){
				if(!preg_match("/$value/is",$resultFile,$match2)){
					return false;
				}
			}
			return true;
		}
	}

	function fetchPage($url){
        
        $proxyDetail=$this->common_model->getProxy($this->website_id);
        
		$this->curl->create($url);

		//  To Temporarily Store Data Received From Server
		#$this->curl->option('buffersize', 10);
		//  To support Different Browsers
		$this->curl->option('useragent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2');
		//  To Receive Data Returned From Server
		$this->curl->option('returntransfer', 1);
		//  To follow The URL Provided For Website
		$this->curl->option('followlocation', 1);
		//  To Retrieve Server Related Data
		$this->curl->option('HEADER', true);
		//  To Set Time For Process Timeout
		$this->curl->option('connecttimeout', 600);
		
		if($proxyDetail){
			$this->curl->option('PROXYPORT', $proxyDetail->port);
			$this->curl->option('PROXYTYPE', 'HTTPS');
			$this->curl->option('PROXY', $proxyDetail->ip);
			$this->curl->option('PROXYUSERPWD', "$proxyDetail->uname:$proxyDetail->password");
			$data = $this->curl->execute();
		}
		
		//  To Execute 'option' Array Into cURL Library & Store Returned Data Into $data
		

		if(empty($data)){
			$this->insertLogData['message']="Could not get response.";
			$this->insertLogData['status']='0';
		}else{
			$this->insertLogData['message']="Response Success.";
			$this->insertLogData['status']='1';
		}		
		$this->insertLogData['url']=$url;
		$this->insertLogData['proxy']=$proxyDetail->ip;
		$this->insertLogData['log_time']=date('Y-m-d H:i:s');
		$this->diagnostic_model->insertLog($this->insertLogData,'diagnostic_log');

		$dataToUpdate=Array();
		$dataToUpdate['last_updated']=date('Y-m-d H:i:s');	
		$updateCondition=Array();
		$updateCondition['schedule_id']=$this->schedule_id;
		$this->common_model->update($dataToUpdate, 'schedules',$updateCondition); 

		
		return $data;

	}
	function before ($inthis, $inthat)
	{
		return substr($inthat, 0, strpos($inthat, $inthis));
	} 	

	function between ($inthis, $that, $inthat)
	{
		return $this->before($that, $this->after($inthis, $inthat));
	}

	function after ($inthis, $inthat)
	{
		if (!is_bool(strpos($inthat, $inthis)))
		return substr($inthat, strpos($inthat,$inthis)+strlen($inthis));
	}
	function removeSpaces($str)
	{
		$str=preg_replace('/<.*?>/is','',$str);
 		$str=preg_replace('/^\s+/is',"",$str);
 		$str=preg_replace('/\s+/is'," ",$str);
 		$str=preg_replace('/\s+$/is',"",$str);
 		$str=preg_replace("/\n/"," ",$str);
 		$str=preg_replace("/\r/"," ",$str);
 		$str=preg_replace("/\t/"," ",$str);
 		#$str=preg_replace("/'$/","",$str);
 		return $str;
	}
	function removeCur($str)
	{
		$str=preg_replace('/^\$/is','',$str);
 		return $str;
	}
	function writeToFile($fileName,$data){
		$fh = fopen($fileName, 'w');
		fwrite($fh, $data);
		fclose($fh);

	}
	function appendFile($fileName,$message)
	{
		$myFile = $fileName;
		$fh = fopen($myFile, 'a');
		fwrite($fh, $message);
		fclose($fh);
	}
	function checkDateBetween($postedDate,$startDate,$endDate)
	{
		$myDate = new DateTime($postedDate); // Today
		$contractDateBegin = new DateTime($startDate);
		$contractDateEnd  = new DateTime($endDate);
		if( $myDate->getTimestamp() >= $contractDateBegin->getTimestamp() &&  $myDate->getTimestamp() <= $contractDateEnd->getTimestamp()){
		  return 1;
		}else{
		   return 0;
		}
	}
	public function sendEmail($searchData) {
		$adminEmail=$this->config->item('adminEmail');
		$emails=$this->common_model->getEmailsForNotification($searchData);
		$emailArray=Array();
		foreach($emails as $email){
			if($email['email'] != $this->config->item('adminEmail')){
				$emailArray[$email['email']]=$email['email'];
			}
		}
		$data['searchName']=$searchData['search_name'];
		$emailData['subject']="Scraping completed for the search -- ".$searchData['search_name'];
		$emailData['message']=$this->load->view('admin/email-templates/scraper_start_notification', $data,TRUE);
		#print_r($emailData);
		svi_send_notification($adminEmail,$emailArray,$emailData);
	}
    public function pushNotification($searchID) {
		if(!empty($searchID)){
			$condition=Array();
			$condition['schedule_id']=$searchID;
			$scheduleData=$this->common_model->getScheduleById($condition);
			$activeUsers=$this->common_model->get_users_for_push_notification($scheduleData->keyword_id);
			$dataToInsert=Array();
			foreach($activeUsers as $activeUser){				
				$dataToInsert['search_id']=$scheduleData->keyword_id;	
				$dataToInsert['start_time']=$scheduleData->start_time;	
				$dataToInsert['end_time']=$scheduleData->completion_time;
				$dataToInsert['user_id']=$activeUser['user_id'];	
				$dataToInsert = $this->security->xss_clean($dataToInsert);				
				$this->common_model->insert($dataToInsert, 'notification');
			}
		}
	}
}