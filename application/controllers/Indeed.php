<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Indeed extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('diagnostic_model');
		$this->scraped_date=date('Y-m-d h:i:s');
    }

    public function index($cronSID='',$scheduleID='') {
		
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
            
            $this->captchaFailed=0;

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
			$keywords=str_replace(',',' OR ',$keywords);

			if(!empty($searchData[0]['subcategories'])){
				$conditionIn= explode(',',$searchData[0]['subcategories']);
				$categoryLists = $this->common_model->get_all_subcategories($condition,$conditionIn);
			}
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
				#$this->appendFile('log.txt',"going for Indeed ".$websiteList['name']."\n");
				
				$maxJobType=1;
				if($maxJobType<sizeof($categoryLists)){
					$maxJobType=sizeof($categoryLists);
				}
				
				$resultCount=0;
				for($jobType=0;$jobType<$maxJobType;$jobType++){

					$jt='';
					if(!empty($categoryLists[$jobType]['sub_cat_url'])){
						$jt="&jt=".$categoryLists[$jobType]['sub_cat_url'];
					}
					for($i=0;$i<10;$i++){
						$start=$i*10;
						$searchUrl="https://www.indeed.com/jobs?q=".urlencode($keywords)."&l=".urlencode($websiteList['name'])."&sort=date&fromage=29&radius=25&start=$start$jt";
						#$searchUrl="https://www.indeed.com/jobs?q=php+developer%2C+angular&l=Atlanta%2C+Georgia%2C+United+States&radius=100&start=$start$jt";
						
						#$this->appendFile('log.txt',$searchUrl."\n");
						$resultFile=$this->fetchPage($searchUrl);
						$this->writeToFile('indeed.html',$resultFile);
						
						if(preg_match('/<div\s*id="searchCountPages">\s*Page\s*\d+\s+of\s+(.*?)\s+jobs<\/div>/is',$resultFile,$matcher)){
							$resultCount=$matcher[1];
							#$this->appendFile('log.txt',$resultCount."\n");
							$resultCount=preg_replace("/[^0-9]/", "", $resultCount);
							$resultCount= intdiv($resultCount, 15);
						}
						#print $searchUrl."\n";
						#print "Arun";
						$this->fetchListings($resultFile,$searchData[0],$keywords,$websiteList,$posted_date_start,$posted_date_end,$sid);	
						if($i >= $resultCount){
							break;
						}
					}
				}
				$counter++;
				/*if($counter>4){
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
			/*if(!empty($scheduleID)){
		        $dataToUpdate=Array();
		        $dataToUpdate['status']='C';
		        $dataToUpdate['completion_time']=date('Y-m-d H:i:s');
		        $updateCondition=Array();
		        $updateCondition['schedule_id']=$scheduleID;
		        $this->common_model->update($dataToUpdate, 'schedules',$updateCondition);   
		        $this->pushNotification($scheduleID);
		    }else{
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
		
		#jobmap[0]= {jk:'848b7f0efc9ecbe8'
	
		#$this->writeToFile('htmlfile.html',$resultFile);
		#exit;
		while(preg_match("/jobmap\[\d+\]=\s*\{jk:'(.*?)'/is",$resultFile,$matcher)){
			
			$resultFileTmp=$this->before($matcher[0],$resultFile);
			$resultFile=$this->after($matcher[0],$resultFile);	
			$uniqueId=$matcher[1];
			#https://www.indeed.com/viewjob?jk=f078e2a726eaec31&tk=1fai9eaug31i6000&from=serp&vjs=3
			$url="https://www.indeed.com/viewjob?jk=".$uniqueId;
			
			$jobListDate='';

			if(preg_match("/data-jk=\"$uniqueId/is",$resultFile,$matcher1)){
				$resultFile=$this->after($matcher1[0],$resultFile);
				if(preg_match("/data-jk=/is",$resultFile,$matcher1)){
					$resultFile=$this->before($matcher1[0],$resultFile);					
				}
				#<span class="date">
				$this->writeToFile('test.html',$resultFile);
				if(preg_match('/<span class="date">Today<\/span>/is',$resultFile,$matcher)){
					$jobListDate=date('Y-m-d');
				}elseif(preg_match('/<span class="date">Just posted<\/span>/is',$resultFile,$matcher)){
					$jobListDate=date('Y-m-d');
				}elseif(preg_match('/<span class="date">(\d+)\s*days\s*ago<\/span>/is',$resultFile,$matcher)){
					$jobListDate=date('Y-m-d', strtotime("-$matcher[1] days"));
				}elseif(preg_match('/<span class="date">(\d+)\s*day\s*ago<\/span>/is',$resultFile,$matcher)){
					$jobListDate=date('Y-m-d', strtotime("-$matcher[1] days"));
				}elseif(preg_match('/<span class="date">\s*Active\s*(\d+)\s*days\s*ago<\/span>/is',$resultFile,$matcher)){
					$jobListDate=date('Y-m-d', strtotime("-$matcher[1] days"));
				}
			}

			if(empty($jobListDate)){
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
            
			#Skip post if older than 30 days
			$records_to_keep=$this->db->get_where('websites',array('id'=>'3'))->row()->records_to_keep;
			$dateDiff=svi_datediff($jobListDate,date('Y-m-d'));
			if($dateDiff>$records_to_keep){
				return;
			}
			
			if(!empty($posted_date)){				
				if(!empty($posted_date_end)){
					if(!$this->checkDateBetween($jobListDate,$posted_date_start,$posted_date_end)){
						continue;
					}
				}elseif($jobListDate != $posted_date){
					continue;
				}
			}		
			$resultId = $this->db->get_where('results',array('unique_id'=>$uniqueId,'website_id'=>$categoryList['website_id']))->row()->result_id;
			if(empty($resultId)){
				$this->emailArr=Array();
				$this->scrapeData($url,$categoryList,$keywords,$websiteList,$uniqueId,$sid);
			}
		}
	}

	function scrapeData($url,$categoryList,$keywords,$websiteList,$uniqueId,$sid){
		
		$insertResult=0;
		$resultFile=$this->fetchPage($url);		
		#$this->writeToFile('htmlfile.html',$resultFile);

		$resultData=Array();
		$resultData['job_url']=$url;
		$resultData['unique_id']=$uniqueId;
		$resultData['website_id']=$categoryList['website_id'];
		$resultData['suburl_id']=$websiteList['suburl_id'];
		$resultData['sub_category_id']=$categoryList['sub_cat_id'];
		
		$resultData['employementType']='';
		$resultData['organization']='';
		$resultData['seniorityLevel']='';
		$resultData['keywords']=$keywords;

		if(preg_match('/jobsearch-JobInfoHeader-title">(.*?)<\/h1>/is',$resultFile,$matcher)){
			$resultData['title']=$matcher[1];
			$resultData['title']=$this->removeSpaces($resultData['title']);
			$resultData['title']=html_entity_decode($resultData['title']);
		}

		if(preg_match('/jobsearch-DesktopStickyContainer-companyrating">\s*<div.*?>(.*?)<\/div>/is',$resultFile,$matcher)){
			$resultData['organization']=strip_tags($matcher[1]);
		}
		if(preg_match('/formattedLocation\\\x22:\\\x22(.*?)\\\x/is',$resultFile,$matcher)){
			$resultData['location']=$matcher[1];
		}
		#<div class="jobsearch-JobMetadataFooter">
		if(preg_match('/class="jobsearch-jobDescriptionText">(.*?)<div\s*class="jobsearch-JobMetadataFooter"/is',$resultFile,$matcher)){
			$resultData['description']=$matcher[1];
			$resultData['description']=strip_tags($resultData['description'],'');
			$resultData['description']=html_entity_decode($resultData['description']);
		}

		if(preg_match('/indeed\.com\/cmp\/(.*?)\?/is',$resultFile,$matcher)){
			$resultData['companyUrl']="https://www.indeed.com/cmp/".$matcher[1];
		}
		
		/*if(preg_match('/<div>Today<\/div>/is',$resultFile,$matcher)){
			$resultData['posted_date']=date('Y-m-d');
		}elseif(preg_match('/<div>Just posted<\/div>/is',$resultFile,$matcher)){
			$resultData['posted_date']=date('Y-m-d');
		}elseif(preg_match('/<div>(\d+)\s*days\s*ago<\/div>/is',$resultFile,$matcher)){
			$resultData['posted_date']=date('Y-m-d', strtotime("-$matcher[1] days"));
		}elseif(preg_match('/<div>Active\s*(\d+)\s*days\s*ago<\/div>/is',$resultFile,$matcher)){
			$resultData['posted_date']=date('Y-m-d', strtotime("-$matcher[1] days"));
		}

		#Skip post if older than 30 days
		$records_to_keep=$this->db->get_where('websites',array('id'=>'3'))->row()->records_to_keep;
		$dateDiff=svi_datediff($resultData['posted_date'],date('Y-m-d'));
		if($dateDiff>$records_to_keep){
			return;
		}
		*/
		
		if(!empty($this->input->get('mak')) && $this->input->get('mak')=='Y'){
			$resultData['exact_match']='Y';
		}else{
			$resultData['exact_match']='N';
		}

		$description=$resultData['description'];
		$description=strip_tags($description,'');
		if($this->checkKeywords($description,$keywords)){
			$resultData['keywords']=str_replace(' OR ',',',$keywords);	
			$resultData['scraped_date']=$this->scraped_date;	
			$resultData['search_id']=$sid;	
			$resultData = $this->security->xss_clean($resultData);
			
			$resultId=$this->common_model->insert($resultData, 'results');		
			
			if(!empty($resultData['companyUrl'])){
				
				$companyUrl=$resultData['companyUrl'];
				$resultFileCompany=$this->fetchPage($companyUrl);
				$employeeDataCompany=Array();
				$employeeDataCompany=$this->fetchEmployeeDetails($resultFileCompany);
				
				$resultData['description']='';

				foreach ($employeeDataCompany as $empData){
					$dataToInsert=Array();
					$dataToInsert['result_id']=$resultId;
					$dataToInsert['person_name']=$empData['person_name'];
					$dataToInsert['designation']=$empData['designation'];
					$this->common_model->insert($dataToInsert, 'key_company_person');
				}

				$companyUrl=$resultData['companyUrl']."/about";
				$resultFileCompany=$this->fetchPage($companyUrl);				

				$resultDataCompany=Array();

				if(preg_match('/website<\/(span|a)>/is',$resultFileCompany,$matcher)){
					$resultFileCompanyTemp=$this->before($matcher[0],$resultFileCompany);
					if(preg_match('/.*href="(.*?)"/is',$resultFileCompanyTemp,$matcher)){
						$resultDataCompany['companyWebsite']=$matcher[1];
					}
				}				
				
				if(preg_match('/class="twitter-timeline" href="(.*?)"/is',$resultFileCompany,$matcher)){
					$resultDataCompany['twitterURL']=$matcher[1];
				}
				if(preg_match('/class="fb-page"\s*data-href="(.*?)"/is',$resultFileCompany,$matcher)){
					$resultDataCompany['facebookURL']=$matcher[1];
				}

				if(!empty($resultDataCompany['companyWebsite'])){
					$this->resultFile=$this->fetchPage($resultDataCompany['companyWebsite']);
					$this->parseEmail($this->resultFile);
					$baseUrl=$resultDataCompany['companyWebsite'];
					$resultFileBufferDetail=$this->resultFile;
					if(preg_match('/>(Contact Us|Contact)\s*<\/a>/is',$resultFileBufferDetail,$matcher)){
						$resultFile=$this->before($matcher[0],$resultFileBufferDetail);
						if(preg_match('/.*href=["\'](.*?)["\']/is',$resultFile,$matcher)){				
							
							$url=$matcher[1];
							if(!preg_match('/(http|www)/i',$url,$match)){
								if(preg_match('/^\//i',$url,$match)){
									$url=$baseUrl.$url;
								}else{
									$url=$baseUrl."/".$url;
								}
							}
							$this->resultFile=$this->fetchPage($url);
							$this->parseEmail($this->resultFile);
						}
					}
				}

				if(count($this->emailArr)>0){				
					foreach ($this->emailArr as $key => $value){					
						if(!preg_match('/(\.png|\.jpg|\.gif|\.jpeg|@sentry\.wixpress\.com)$/',$key,$matcher)){
							$resultDataCompany['emails'] .= $key.",";
						}
					}
				}
				if(!empty($resultDataCompany)){
					$resultDataCompany['emails']=rtrim($resultDataCompany['emails'],',');
					$condition=Array(
						'result_id'=>$resultId
					);
					$this->common_model->update($resultDataCompany, 'results',$condition);
				}
			}
		}
	}
	function checkKeywords($resultFile,$keywords){
		$keywords=str_replace(' OR ','|',$keywords);
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

        sleep(5);
        $proxyDetail=$this->common_model->getProxy($this->website_id);
		$this->curl->create($url);

		//  To Temporarily Store Data Received From Server
		#$this->curl->option('buffersize', 10);
		//  To support Different Browsers
		#$this->curl->option('useragent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
		$this->curl->option('useragent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2');
		#$this->curl->option('CURLOPT_USERAGENT', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
		#curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
		//  To Receive Data Returned From Server
		$this->curl->option('returntransfer', 1);
		#$this->curl->option('referer', 'https://search.google.com');
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
		    
		    if(preg_match("/<title>hCaptcha solve page/is",$data,$matcher)){
		        $this->insertLogData['message']="Website throwing captcha";
			    $this->insertLogData['status']='0';
			    if($this->captchaFailed>300){
			        if(!empty($this->schedule_id)){
        		        $dataToUpdate=Array();
        		        $dataToUpdate['status']='F';
						$dataToUpdate['completion_time']=date('Y-m-d H:i:s');
        		        $updateCondition=Array();
        		        $updateCondition['schedule_id']=$this->schedule_id;						
        		        $this->common_model->update($dataToUpdate, 'schedules',$updateCondition);   
        		    }
			        exit;
			    }
			    $this->captchaFailed++;
		    }else{
			    $this->insertLogData['message']="Response Success.";
			    $this->insertLogData['status']='1';
		    }
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
		//  To Display Returned Data
		return $data;

	}
	public function fetchEmployeeDetails($resutFile){
		$finalData=Array();
		
		if(preg_match('/"aboutCeo":{"name":"(.*?)"/is',$resutFile,$matcher)){
			$resultArray['person_name']=$matcher[1];
			$resultArray['designation']='CEO';
			#$resultArray['profile_url']=$profile_url;
			$finalData[]=$resultArray;
		}
		return $finalData; 
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

	function parseEmail($resultFile){
		while(preg_match('/email-protection#(.*?)"/is',$resultFile,$matcher1)){
			$resultFile=$this->after($matcher1[0],$resultFile);
			$email=$this->deCFEmail($matcher1[1]);
			if(empty($this->emailArr[$email])){
				$this->emailArr[$email]=1;
			}
		}

		$resultFile=$this->resultFile;
		while(preg_match('/([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})/is',$resultFile,$matcher1)){
			$resultFile=$this->after($matcher1[0],$resultFile);
			$email=$matcher1[1];
			if(empty($this->emailArr[$email])){
				$this->emailArr[$email]=1;
			}
		}
	}
	function deCFEmail($c){
		$k = hexdec(substr($c,0,2));
		for($i=2,$m='';$i<strlen($c)-1;$i+=2)$m.=chr(hexdec(substr($c,$i,2))^$k);
		return $m;
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
