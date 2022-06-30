<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Linkedin extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('diagnostic_model');
		$this->load->model('reject_model');
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
				#'website_id' => $searchData[0]['website_id'],
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
			$jobTypes='';
			$remote='';
			foreach($categoryLists as $categoryList){
				if($categoryList['sub_cat_url']=='f_WRA'){
					$remote = "&f_CF=f_WRA";
				}else{
					$jobTypes .=$categoryList['sub_cat_url'].",";
				}
			}
			$jobTypes=rtrim($jobTypes,',');
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
				$this->appendFile('log.txt',"going for Linkedin ".$websiteList['name']."\n");
				#$websiteList['sub_url']='https://newyork.craigslist.org';

				$resultCount=0;
				for($i=0;$i<10;$i++){
					$searchUrl="https://www.linkedin.com/jobs/search/?f_TPR=r604800&keywords=".urlencode($keywords)."&location=".urlencode($websiteList['name'])."&position=1&pageNum=".$i."&redirect=false&sortBy=DD";
					if(!empty($jobTypes)){
						$searchUrl .="&f_JT=".urlencode($jobTypes);
					}
					if(!empty($remote)){
						$searchUrl .=$remote;
					}
					#$this->appendFile('log.txt',$searchUrl."\n");
					$resultFile=$this->fetchPage($searchUrl);
					#$this->appendFile('result.html',$resultFile."\n");
			
					if(preg_match('/<span class="results-context-header__job-count">(.*?)<\/span>/is',$resultFile,$matcher)){
						$resultCount=$matcher[1];
						$this->appendFile('log.txt',$resultCount."\n");
						$resultCount=preg_replace("/[^0-9]/", "", $resultCount);
						$resultCount= intdiv($resultCount, 25);
					}
					
					#$this->writeToFile('linkedin.html',$resultFile.$resultCount);
					$this->fetchListings($resultFile,$searchData[0],$keywords,$websiteList,$posted_date_start,$posted_date_end,$sid);	
					if($i >= $resultCount){
						break;
					}					
				}	
				$counter++;
				/*if($counter>20){
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
		
		if(preg_match('/We\s*couldn\&rsquo;t\s*find/is',$resultFile,$matcher)){
			return;
		}
		#$this->writeToFile('linkedin.html',$resultFile);
		
		while(preg_match('/(<span class="screen-reader-text">|data-tracking-control-name="public_jobs_jserp-result_search-card")/is',$resultFile,$matcher)){
			
			$resultFileTmp=$this->before($matcher[0],$resultFile);
			$resultFile=$this->after($matcher[0],$resultFile);	
			$uniqueId='';
			
			if(preg_match('/.*href="(.*?)"/is',$resultFileTmp,$matcher1)){
				$url=$matcher1[1];
				$url=preg_replace('/\&amp;/is','&',$url);				
			}
			if(preg_match('/.*jobPosting:(.*?)"/is',$resultFileTmp,$matcher1)){
				$uniqueId=$matcher1[1];
			}
			$jobListDate='';
			
			#$this->writeToFile('linkedin.html',$resultFile);
			#<time class="job-search-card__listdate" datetime="2021-11-01">
			#if(preg_match('/class="job-result-card__listdate" datetime="(.*?)"/is',$resultFile,$matcher1)){

			if(preg_match('/class="(job-search-card__listdate|job-search-card__listdate|job-search-card__listdate--new)" datetime="(.*?)"/is',$resultFile,$matcher1)){
				$jobListDate=$matcher1[2];
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
			
			#Skip post if older than 60 days
			$records_to_keep=$this->db->get_where('websites',array('id'=>'2'))->row()->records_to_keep;
			$dateDiff=svi_datediff($jobListDate,date('Y-m-d'));
			if($dateDiff>$records_to_keep){
				continue;
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
				#$url='https://www.linkedin.com/jobs/view/mid-level-python-developer-w-2-hourly-tx-at-howard-systems-international-2000052433?refId=4e651da7-fe51-47ae-a1db-cb82ed8c0e11&amp;position=1&amp;pageNum=0&amp;trk=public_jobs_job-result-card_result-card_full-click';
				$this->emailArr=Array();
				//print $url."<br>";
				$this->scrapeData($url,$categoryList,$keywords,$websiteList,$uniqueId,$sid);
			}
		}
	}

	function scrapeData($url,$categoryList,$keywords,$websiteList,$uniqueId,$sid){
		$insertResult=0;
		$resultFile=$this->fetchPage($url);
		#$this->writeToFile('scrape.html',$resultFile);
		
		$resultData=Array();
		$resultData['job_url']=$url;
		$resultData['unique_id']=$uniqueId;
		$resultData['website_id']=$categoryList['website_id'];
		$resultData['suburl_id']=$websiteList['suburl_id'];
		$resultData['sub_category_id']=$categoryList['sub_cat_id'];
		$resultData['employementType']='';
		$resultData['seniorityLevel']='';
		$resultData['keywords']=$keywords;
		if(preg_match('/<figcaption class="num-applicants__caption">(.*?)<\/figcaption>/is',$resultFile,$matcher)){
			$resultData['applications']=$matcher[1];
		}elseif(preg_match('/<span\s*class="topcard__flavor--metadata topcard__flavor--bullet num-applicants__caption">(.*)<\/span>\s*<\/h3>/is',$resultFile,$matcher)){
			$resultData['applications']=$this->removeSpaces($matcher[1]);
		}
		
		if(preg_match('/"datePosted":"(\d\d\d\d-\d\d-\d\d)/is',$resultFile,$matcher)){
			$resultData['posted_date']=$matcher[1];
		}
		if(preg_match('/"title":"(.*?)"/is',$resultFile,$matcher)){
			$resultData['title']=$this->removeSpaces($matcher[1]);
			$resultData['title']=html_entity_decode($resultData['title']);
		}
		if(preg_match('/<div class="show-more-less-html__markup show-more-less-html__markup--clamp-after-5">(.*?)<\/div>/is',$resultFile,$matcher)){
			$resultData['description']=$matcher[1];
			$resultData['description']=strip_tags($resultData['description'],'');
			$resultData['description']=html_entity_decode($resultData['description']);
			$resultData['description']=$this->removeSpaces($resultData['description']);
		}
		if(preg_match('/"Organization","name":"(.*?)"/is',$resultFile,$matcher)){
			$resultData['organization']=$matcher[1];
			#$resultData['organization']=$resultData['organization'];
		}
		if(preg_match('/"sameAs":"(.*?)"/is',$resultFile,$matcher)){
			$resultData['companyUrl']=$matcher[1];
			$resultData['companyUrl']=$resultData['companyUrl'];
		}
		if(preg_match('/<span\s*class="sub-nav-cta__meta-text">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['location']=$matcher[1];
			$resultData['location']=$resultData['location'];
		}
		if(preg_match('/>Employment\s*type<\/h3>\s*<span class="job-criteria__text\s*job-criteria__text--criteria">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['employementType']=$matcher[1];
			$resultData['employementType']=$resultData['employementType'];
		}
		if(preg_match('/>Seniority level<\/h3>\s*<span\s*class="job-criteria__text\s*job-criteria__text--criteria">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['seniorityLevel']=$matcher[1];
			$resultData['seniorityLevel']=$resultData['seniorityLevel'];
		}
		if(preg_match('/>Industries<\/h3>\s*<span\s*class="job-criteria__text\s*job-criteria__text--criteria">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['industries']=$matcher[1];
			$resultData['industries']=$resultData['industries'];
		}
		if(preg_match('/>Job\s*function<\/h3>\s*<span\s*class="job-criteria__text\s*job-criteria__text--criteria">(.*?)<\/span>/is',$resultFile,$matcher)){
			$resultData['jobFunction']=$matcher[1];
			$resultData['jobFunction']=$resultData['jobFunction'];
		}
		if(preg_match('/<a aria-label="Message\s+(.*?)"\s*class="message-the-recruiter__cta"\s*href="(.*?)"/is',$resultFile,$matcher)){
			$jobPosterName=$matcher[1];
			$jobPosterUrl=$matcher[2];
			$resultData['job_poster_name']=$jobPosterName;
			if(preg_match('/linkedin\%2Ecom\%2Fin\%2F(.*?)\&/is',$jobPosterUrl,$matcher)){
				$resultData['job_poster_url']="https://www.linkedin.com/in/".$matcher[1];
			}			
		}
		
		if(!empty($this->input->get('mak')) && $this->input->get('mak')=='Y'){
			$resultData['exact_match']='Y';
		}else{
			$resultData['exact_match']='N';
		}
		
		$description=$resultData['description'];
		$description=strip_tags($description,'');
	
		if($this->checkKeywords($description,$keywords)){			
			#print "Arun"; exit;
			$companyUrl=$resultData['companyUrl'];
			$companyUrl = str_replace('https://www.linkedin.com/company/','https://www.linkedin.com/organization-guest/company/',$companyUrl);
			#$resultFileCompany=$this->fetchPage($companyUrl);
			#$companyUrl='https://www.linkedin.com/organization-guest/company/cvs-health';
			$resultFileCompany=$this->fetchPage($companyUrl);


			$resultDataCompany=Array();
			if(preg_match('/<a\s*class="external-link about-us__link".*?>(.*?)</is',$resultFileCompany,$matcher)){
				$resultDataCompany['companyWebsite']=$this->removeSpaces($matcher[1]);
			}
			if(preg_match('/Company size\s*<\/dt>\s*<dd\s*class="basic-info-item__description">(.*?)<\/dd>/is',$resultFileCompany,$matcher)){
				$resultDataCompany['employees']=$matcher[1];
				$resultDataCompany['employees']=str_replace('employees','',$resultDataCompany['employees']);
				$resultDataCompany['employees']=$this->removeSpaces($resultDataCompany['employees']);
			}
			if(preg_match('/"org-employees_cta"\s*data-tracking-will-navigate>\s*View\s+all\s+(.*?)\s+employees/is',$resultFileCompany,$matcher)){
				$resultDataCompany['employeesOnLinkedin']=$matcher[1];
				$resultDataCompany['employeesOnLinkedin']=$this->removeSpaces($resultDataCompany['employeesOnLinkedin']);
				$resultDataCompany['employeesOnLinkedin']=preg_replace('/,/is','',$resultDataCompany['employeesOnLinkedin']);
			}

			/* Skip if this job was posted by a company exists in rejected list 
				companyUrl, organization
			*/
			
			$companyWebsite=$resultDataCompany['companyWebsite'];
			$companyWebsite=preg_replace('/http:\/\//is','',$companyWebsite);
			$companyWebsite=preg_replace('/https:\/\//is','',$companyWebsite);
			if(preg_match('/(.*?)\/$/is',$companyWebsite,$matcherCompany)){
				$companyWebsite=$matcherCompany[1];
			}
			
			$rejectCondition=Array();
			$rejectCondition['website_id']=$resultData['website_id'];
			$rejectCondition['companyUrl']=$resultData['companyUrl'];
			$rejectCondition['organization']=$resultData['organization'];
			$rejectCondition['companyWebsite']=$companyWebsite;
			$rejectCondition['organization']=preg_replace('/\W+/isg','',$rejectCondition['organization']);
			$rejectCondition['company_name_formatted']=strtolower($rejectCondition['organization']);
			$rejectCondition['companyUrl']=preg_replace('/\W+$/isg','',$rejectCondition['companyUrl']);
			#print_r($rejectCondition);
			if($this->reject_model->checkInRejectedList($rejectCondition)){
				return false; #Return from the function without inserting any record
			}
			#exit;
			/* End Skip post if company in rejected list */


			$resultData['keywords']=str_replace(' OR ',',',$keywords);	
			$resultData['scraped_date']=$this->scraped_date;	
			$resultData['search_id']=$sid;	
			$resultData = $this->security->xss_clean($resultData);
			
			$resultId=$this->common_model->insert($resultData, 'results');		
			#print_r($resultData);
			#exit;
			$employeeDataCompany=Array();
			$employeeDataCompany=$this->fetEmployeeDetails($resultFileCompany);
			foreach ($employeeDataCompany as $empData){
				$dataToInsert=Array();
				$dataToInsert['result_id']=$resultId;
				$dataToInsert['person_name']=$empData['person_name'];
				$dataToInsert['designation']=$empData['designation'];
				$dataToInsert['profile_url']=$empData['profile_url'];
				$this->common_model->insert($dataToInsert, 'key_company_person');
			}

			
			
			if(!empty($resultDataCompany['companyWebsite'])){
				
				$this->resultFile=$this->fetchPage($resultDataCompany['companyWebsite']);
				$this->parseEmail($this->resultFile);
				#$this->writeToFile('htmlfile.html',$this->resultFile);	
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
		}
		//  To Execute 'option' Array Into cURL Library & Store Returned Data Into $data
		$data = $this->curl->execute();
		#if(empty($data)){
		if(!preg_match('/linkedin/is',$data,$matcher)){	
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

		//  To Display Returned Data
		return $data;

	}
	public function fetEmployeeDetails($resutFileLinkeDin){
		$finalData=Array();
		
		if(preg_match('/<ul class="employees__list">(.*?)<\/ul>/is',$resutFileLinkeDin,$matcher)){
			$resutFileLinkeDin=$matcher[1];
			while(preg_match('/<h3\s*class="result-card__title\s*">\s*(.*?)\s*<\/h3>\s+<h4\s*class="result-card__subtitle\s*">(.*?)<\/h4>/is',$resutFileLinkeDin,$matcher)){
				$profileHtml=$this->before($matcher[0],$resutFileLinkeDin);	
				$resutFileLinkeDin=$this->after($matcher[0],$resutFileLinkeDin);	
				$resultArray=Array();				
				$name=$this->removeSpaces($matcher[1]);
				$position=$this->removeSpaces($matcher[2]);
				$profile_url='';
				if(preg_match('/.*<a\s*class="result-card__full-card-link"\s*href="(.*?)\?trk=/is',$profileHtml,$matcher1)){
					$profile_url=$matcher1[1];
				}
				$resultArray['person_name']=$name;
				$resultArray['designation']=$position;
				$resultArray['profile_url']=$profile_url;
				$finalData[]=$resultArray;
			}
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
	function removeSpaces($str){
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
	function removeCur($str){
		$str=preg_replace('/^\$/is','',$str);
 		return $str;
	}
	function writeToFile($fileName,$data){
		$fh = fopen($fileName, 'w');
		fwrite($fh, $data);
		fclose($fh);
	}
	function appendFile($fileName,$message){
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