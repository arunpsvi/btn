<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Download extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
    }

    public function index() {
		$defaultWebsiteId=0;
		if(!empty($this->input->get('website_id'))){
			$defaultWebsiteId=$this->input->get('website_id');
		}
		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url');
		
		if(empty($defaultWebsiteId)){
			foreach($websiteListArr as $key=>$value){
				$defaultWebsiteId=$key;
				break;
			}
		}
		
		if(!empty($this->input->get('bot_name'))){
			//$condition['res.search_id']= $this->input->get('bot_name');
		}
		$deleteCondition=Array();
		$condition['res.website_id']= $defaultWebsiteId;
		$deleteCondition['website_id']= $defaultWebsiteId;
		
		$defaultStartDate=$this->input->get('scraped_date_start');
		if(!isset($defaultStartDate)){
			$condition['res.scraped_date']=date('Y-m-d');
			$deleteCondition['scraped_date']= $this->input->get('scraped_date_start');
		}

		if(!empty($this->input->get('scraped_date_start')) && !empty($this->input->get('scraped_date_end'))){
			$condition['res.scraped_date >= ']= $this->input->get('scraped_date_start');
			$condition['res.scraped_date <= ']= $this->input->get('scraped_date_end');

			$deleteCondition['scraped_date >= ']= $this->input->get('scraped_date_start');
			$deleteCondition['scraped_date <= ']= $this->input->get('scraped_date_end');
		}elseif(!empty($this->input->get('scraped_date_start'))){
			$condition['res.scraped_date']= $this->input->get('scraped_date_start');
			$deleteCondition['scraped_date']= $this->input->get('scraped_date_start');
		}
		if(!empty($this->input->get('qualify'))){
			$condition['res.qualify']= $this->input->get('qualify');
			$deleteCondition['qualify']= $this->input->get('qualify');
		}

		if(!empty($this->input->get('deleteAllSearch')) && $this->input->get('deleteAllSearch')=='true' && $this->session->userdata('role')=='ADMIN'){
			$this->common_model->delete('results',$deleteCondition);
			redirect(site_url('admin/download?website_id='.$condition['res.website_id']),'refresh');
		}
		
		
		$showHideCols=$this->input->get('showHideCols');
		foreach ($showHideCols as $showHideCol){
			if(empty($data['hidSelectedOptions'])){
				$data['hidSelectedOptions'] .=$showHideCol;
			}else{
				$data['hidSelectedOptions'] .=','.$showHideCol;
			}
		}
		if(empty($this->input->get('showHideCols'))){
			$filterCondition=Array();
			$filterCondition['user_id']=$this->session->userdata('id');
			$filterCondition['website_id']=$defaultWebsiteId; 
			$filterCondition['search_id']=0; 
			/*
			if(empty($this->input->get('bot_name'))){
				$filterCondition['search_id']=0; 
			}else{
				$filterCondition['search_id']=$this->input->get('bot_name'); 
			}*/
			$data['hidSelectedOptions']=$this->db->get_where('user_filters',$filterCondition)->row()->show_filters;
		}
		
		$searchBotCondition=array();
		$searchBotCondition['keyw.website_id']=$defaultWebsiteId;
		$allBots=$this->common_model->get_all_searches($searchBotCondition);
		
		
		$botArray=Array();
		#$botArray['']='All';
		foreach ($allBots as $bot){
			$key='BOT_'.$bot['keyword_id'];
			$botArray[$key]=$bot['search_name'];
		}
		$showHideBots=$this->input->get('showHideBots');
		if(empty($showHideBots)){
			foreach ($botArray as $botkey=>$botval){
				if(empty($data['hidSelectedBots'])){
					$data['hidSelectedBots'] .=$botkey;
				}else{
					$data['hidSelectedBots'] .=",".$botkey;
				}
			}
		}else{
			foreach ($showHideBots as $showHideBot){
				if(empty($data['hidSelectedBots'])){
					$data['hidSelectedBots'] .=$showHideBot;
				}else{
					$data['hidSelectedBots'] .=",".$showHideBot;
				}
			}
			//$data['hidSelectedBots']=$showHideBots;
		}

		if(!empty($data['hidSelectedBots'])){
			$botIds=preg_replace('/BOT_/i','',$data['hidSelectedBots']);
			$botIdArr=explode(',',$botIds);
		}
		
		//print $data['hidSelectedBots'];exit;
		//print_r($botArray);exit;
		$config['base_url'] = site_url().'/admin/download/';
		$config['uri_segment'] = 3;
		$config['per_page'] = 500;
		$config['total_rows'] = $this->common_model->get_job_listings_total($condition,$botIdArr);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();
		
		$qualifyArray=svi_buildArray($this->common_model->get_qualify(),'name','name',' ');
		$qualifySearchArray=svi_buildArray($this->common_model->get_qualify(),'name','name','All');
		if($this->session->userdata('role') != 'ENDUSER'){
			$qualifySearchArray['-----']='---------------------';
			$qualifySearchArray['ADD#NEW']='Add new';
		}
		

		$data['qualify']=$this->input->get('qualify');
		$data['qualifySearchArr']=$qualifySearchArray;
		$data['qualifyArr']=$qualifyArray;
		$data['website_id']=$defaultWebsiteId;
		$data['websiteListArr'] = $websiteListArr;
		$data['botArray'] = $botArray;
		//$data['bot_name'] = $this->input->get('bot_name');
	
		$data['userRole'] = $this->session->userdata('role');
		if(!empty($this->input->get('scraped_date_end'))){
			$data['scraped_date_end'] = $this->input->get('scraped_date_end');
		}
		if(!empty($this->input->get('scraped_date_start'))){
			$data['scraped_date_start'] = $this->input->get('scraped_date_start');
		}
		if(!isset($defaultStartDate)){
			$data['scraped_date_start']= date('Y-m-d');
		}
		
		$jobListings = $this->common_model->get_job_listings($condition, $config['per_page'], $page,$sort_order,$botIdArr);
		$data['jobListings'] = $jobListings;
		$data['total_rows'] = $config['total_rows'];
		//echo"<pre>";
		//print_r($data);exit;
		if($data['website_id']==1){
			$data['main_content'] = $this->load->view('admin/download/craiglist', $data, TRUE);
		}elseif($data['website_id']==2){
			$data['main_content'] = $this->load->view('admin/download/linkedin', $data, TRUE);
		}elseif($data['website_id']==3){
			$data['main_content'] = $this->load->view('admin/download/indeed', $data, TRUE);
		}
	    $this->load->view('admin/index', $data);
    }
	function mybots(){
		//echo"<pre>";
		//print_r($_GET);exit;
		$searchBotCondition=array();
		if(!empty($this->input->get('wid'))){
			$searchBotCondition['keyw.website_id']=$this->input->get('wid');
		}

		$defaultWebsiteId=0;
		if(!empty($this->input->get('wid'))){
			$defaultWebsiteId=$this->input->get('wid');
		}
		$website_list = $this->common_model->get_all_websites();
		
		$websiteListArr=svi_buildArray($website_list,'id','url','All');
		if(empty($defaultWebsiteId)){
			foreach($websiteListArr as $key=>$value){
				$defaultWebsiteId=$key;
				break;
			}
		}
		
		$data['bots']=$this->common_model->get_all_searches($searchBotCondition);
		
		
		$data['websiteListArr']=$websiteListArr;

		$config['base_url'] = site_url().'/admin/download/mybots';
		$config['uri_segment'] = 3;
		$config['per_page'] = 50;
		//$config['total_rows'] = $this->login_model->getUsersAccessDetailTotal($condition);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();


		//$userAccessDetails = $this->login_model->getUsersAccessDetail($condition, $config['per_page'], $page,$sort_order);
		
		$data['website_id']=$defaultWebsiteId;
        $data['main_content'] = $this->load->view('admin/download/mybots', $data, TRUE);
        $this->load->view('admin/index', $data);
	}

	function downloadCSV(){
		$defaultWebsiteId=0;
		$condition=Array();
		$whereIN=Array();
		if(!empty($this->input->get('website_id'))){
			$defaultWebsiteId=$this->input->get('website_id');
		}
		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url');
		
		if(empty($defaultWebsiteId)){
			foreach($websiteListArr as $key=>$value){
				$defaultWebsiteId=$key;
				break;
			}
		}
		$defaultStartDate=$this->input->get('scraped_date_start');
		if(!isset($defaultStartDate)){
			$condition['res.scraped_date']=date('Y-m-d');
		}

		if(!empty($this->input->get('scraped_date_start')) && !empty($this->input->get('scraped_date_end'))){
			$condition['res.scraped_date >= ']= $this->input->get('scraped_date_start');
			$condition['res.scraped_date <= ']= $this->input->get('scraped_date_end');
		}elseif(!empty($this->input->get('scraped_date_start'))){
			$condition['res.scraped_date']= $this->input->get('scraped_date_start');
		}


		if(!empty($this->input->get('website_id'))){
			$condition['res.website_id']= $this->input->get('website_id');
		}
		
		if(!empty($this->input->get('qualify')) && $this->input->get('qualify')!='undefined'){
			$condition['res.qualify']= $this->input->get('qualify');
		}
		
		
		if(!empty($this->input->get('bot_name'))){
			$botIds=preg_replace('/BOT_/i','',$this->input->get('bot_name'));
			$botIdArr=explode(',',$botIds);
		}
		
		$data['website_id']=$defaultWebsiteId;
		$data['websiteListArr'] = $websiteListArr;
		
		$jobListings = $this->common_model->get_job_listings($condition,'','','',$botIdArr);
		
		$fileName = $websiteListArr[$defaultWebsiteId].date('Y-m-d').'.xlsx';
		$this->load->library('Excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'No.');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Qualify');
		if($defaultWebsiteId==2){
			$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Decision Maker');
		}else{
        	$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Name');
		}
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Phone');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Source');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Scraped Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Posted Date');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Location');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Category');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Sub Category');       
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Keyword'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Post Title'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Post Url'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Exact match'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Compensation'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Description'); 
		if($defaultWebsiteId==2){
			$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Applications'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Organization'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Profile url'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'Employement Type'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Seniority level'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('W1', 'Industries'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Job function'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'Company url'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Employees'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AA1', 'Employees on linkedin'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AB1', 'Emails'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AC1', 'Key Contact 1'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AD1', 'Profile 1'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AE1', 'Key Contact 2'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AF1', 'Profile 2'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AG1', 'Key Contact 3'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AH1', 'Profile 3'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AI1', 'Key Contact 4'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AJ1', 'Profile 4');
			$objPHPExcel->getActiveSheet()->SetCellValue('AK1', 'Job Poster'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('AL1', 'Job Poster Profile'); 
		}elseif($defaultWebsiteId==3){
			$objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Profile url'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Organization'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Company url'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('U1', 'Emails'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Key Contact 1'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('W1', 'Twitter'); 
			$objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Facebook'); 
		}
		$rowCount = 2;
		foreach($jobListings as $jobListing){
			$keyContacts=$this->common_model->get_key_persons($jobListing);
			
			if($jobListing['exact_match']=='Y'){
				$jobListing['exact_match']='Yes';
			}else{
				$jobListing['exact_match']='No';
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $jobListing['result_id']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $jobListing['qualify']); 
			if($defaultWebsiteId==2){
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $jobListing['decision_maker']); 
			}else{
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $jobListing['name']); 
			}
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $jobListing['phone']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $jobListing['email']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $jobListing['url']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $jobListing['scraped_date']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $jobListing['posted_date']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $jobListing['location']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $jobListing['category_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $jobListing['sub_category_name']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $jobListing['keywords']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $jobListing['title']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $jobListing['job_url']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $jobListing['exact_match']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $jobListing['compensation']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $jobListing['description']); 

			if($defaultWebsiteId==2){
				$objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $jobListing['applications']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $jobListing['organization']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $jobListing['companyUrl']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $jobListing['employementType']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $jobListing['seniorityLevel']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, $jobListing['industries']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $jobListing['jobFunction']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('Y' . $rowCount, $jobListing['companyWebsite']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('Z' . $rowCount, $jobListing['employees']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('AA' . $rowCount, $jobListing['employeesOnLinkedin']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('AB' . $rowCount, $jobListing['emails']); 
				if(!empty($keyContacts[0]['person_name'])){
					$objPHPExcel->getActiveSheet()->SetCellValue('AC' . $rowCount, $keyContacts[0]['person_name']."(".$keyContacts[0]['designation'].")"); 
					$objPHPExcel->getActiveSheet()->SetCellValue('AD' . $rowCount,$keyContacts[0]['profile_url']); 
				}
				if(!empty($keyContacts[1]['person_name'])){
					$objPHPExcel->getActiveSheet()->SetCellValue('AE' . $rowCount, $keyContacts[1]['person_name']."(".$keyContacts[1]['designation'].")"); 
					$objPHPExcel->getActiveSheet()->SetCellValue('AF' . $rowCount,$keyContacts[1]['profile_url']); 
				}

				if(!empty($keyContacts[2]['person_name'])){
					$objPHPExcel->getActiveSheet()->SetCellValue('AG' . $rowCount, $keyContacts[2]['person_name']."(".$keyContacts[2]['designation'].")"); 
					$objPHPExcel->getActiveSheet()->SetCellValue('AH' . $rowCount,$keyContacts[2]['profile_url']); 
				}

				if(!empty($keyContacts[3]['person_name'])){
					$objPHPExcel->getActiveSheet()->SetCellValue('AI' . $rowCount, $keyContacts[3]['person_name']."(".$keyContacts[3]['designation'].")"); 
					$objPHPExcel->getActiveSheet()->SetCellValue('AJ' . $rowCount,$keyContacts[3]['profile_url']);
				}		
				$objPHPExcel->getActiveSheet()->SetCellValue('AK' . $rowCount, $jobListing['job_poster_name']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('AL' . $rowCount, $jobListing['job_poster_url']);		
			}elseif($defaultWebsiteId==3){
				$keycontact='';
				if(!empty($keyContacts[0]['person_name'])){
					$keycontact=$keyContacts[0]['person_name']."(".$keyContacts[0]['designation'].")";
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('R'. $rowCount, $jobListing['companyUrl']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('S'. $rowCount, $jobListing['organization']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('T'. $rowCount, $jobListing['companyWebsite']); 
				$objPHPExcel->getActiveSheet()->SetCellValue('U'. $rowCount, $jobListing['emails']);
				$objPHPExcel->getActiveSheet()->SetCellValue('V'. $rowCount, $keycontact);
				$objPHPExcel->getActiveSheet()->SetCellValue('W'. $rowCount, $jobListing['twitterURL']);
				$objPHPExcel->getActiveSheet()->SetCellValue('X'. $rowCount, $jobListing['facebookURL']);
			}
			$rowCount++;
		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        $objWriter->save('uploads/'.$fileName);
        header("Content-Type: application/vnd.ms-excel");
		redirect(base_url().'uploads/'.$fileName);

	}

    public function updateRecords() {
		if($this->session->userdata('role') == 'ENDUSER'){
			return false;
		}
		$id=$this->input->get('id');
		$qualify=$this->input->get('qualify')."";
		$updateQualify=$this->input->get('updateQualify')."";
		$decision_maker=$this->input->get('decision_maker')."";
		$name=$this->input->get('name')."";
		$phone=$this->input->get('phone')."";
		$email=$this->input->get('email')."";
		$dataArray=Array();
		$conditionArray=Array(
			"result_id" =>$id 
		);
		if(!empty($updateQualify)){			
			$dataArray['qualify']=$qualify;			
		}else{
			$dataArray['email']=$email;
			$dataArray['name']=$name;
			$dataArray['phone']=$phone;
			$dataArray['decision_maker']=$decision_maker;
		}		
		//print_r($dataArray);
		if(!empty($dataArray)){
			$this->common_model->update($dataArray,'results',$conditionArray);
		}
	} 
	
	public function delete() {
		if($this->session->userdata('role') == 'ENDUSER'){
			return false;
		}
		$record_delete=$this->input->post('record_delete');
		$recordsToDelete='';
		foreach($record_delete as $key=>$val){
			$recordsToDelete .="'".$val."',";
		}
		$recordsToDelete=rtrim($recordsToDelete,',');
		
		if(!empty($record_delete)){
			$this->common_model->deleteByIn('results','result_id',$record_delete);
			$this->session->set_flashdata('msg', 'Records deleted successfully');	
		}else{
			$this->session->set_flashdata('error_msg', 'Nothing selected to delete');
		}
		$url="website_id=".$this->input->post('website_id')."&scraped_date=".$this->input->post('scraped_date')."&qualify=".$this->input->post('qualify');
		redirect(site_url('admin/download?'.$url),'refresh');
	}

	public function saveMyFilter(){
		if(!empty($this->input->get('website_id'))){
			$dataToInsert=Array();
			$dataToInsert['user_id']=$this->session->userdata('id');
			$dataToInsert['website_id']=$this->input->get('website_id'); 
			if(empty($this->input->get('search_id'))){
				$dataToInsert['search_id']=0; 
			}else{
				$dataToInsert['search_id']=$this->input->get('search_id'); 
			}
			$filter_id=$this->db->get_where('user_filters',$dataToInsert)->row()->filter_id;
			if(empty($filter_id)){
				$dataToInsert['show_filters']=$this->input->get('showHideCols');
				$dataToInsert = $this->security->xss_clean($dataToInsert);
				$this->common_model->insert($dataToInsert, 'user_filters');
				$response['message']='Filter saved successfully';
				print json_encode($response, JSON_PRETTY_PRINT);
			}else{
				$dataToUpdate=Array();
				$dataToUpdate['show_filters']=$this->input->get('showHideCols');
				$condition = array(
					'filter_id' => $filter_id
				);
				$dataToUpdate = $this->security->xss_clean($dataToUpdate);
				$this->common_model->update($dataToUpdate, 'user_filters',$condition);
				$response['message']='Filter saved successfully';
				print json_encode($response, JSON_PRETTY_PRINT);				
			}
		}		
	}
	function fetchDetails($result_id){
		$condition=Array();
		$condition['result_id']=$result_id;
		$jobDetails=$this->common_model->get_job_details($condition);
		if(empty($jobDetails->result_id)){
			$jsonArr['message']='Records not found.';
		}else{
			
			$jsonArr=$this->createJsonObj($jobDetails);
			$jsonArr['message']='';
		}
		
		print(json_encode($jsonArr));
	}

	# createArray -- Create array of all columns available in the table of database
	function createJsonObj($jobDetail){
		$jsonArr=Array();
		$jsonArr['qualify']=$jobDetail->qualify;	
		$jsonArr['name']=$jobDetail->name;	
		$jsonArr['phone']=$jobDetail->phone;	
		$jsonArr['email']=$jobDetail->email;
		$jsonArr['result_id']=$jobDetail->result_id;	
		$jsonArr['bot_name']=$this->db->get_where('keywords',array('keyword_id'=>$jobDetail->search_id))->row()->search_name;	
		$jsonArr['website_name']=$this->db->get_where('websites',array('id'=>$jobDetail->website_id))->row()->name;	
		#$jsonArr['suburl_id']=$jobDetail->suburl_id;	
		#$jsonArr['sub_category_id']=$jobDetail->sub_category_id;	
		$jsonArr['job_url']=$jobDetail->job_url;	
		$jsonArr['title']=$jobDetail->title;	
		$jsonArr['compensation']=$jobDetail->compensation;	
		$jsonArr['description']=$jobDetail->description;	
		$jsonArr['keywords']=$jobDetail->keywords;	
		$jsonArr['applications']=$jobDetail->applications;	
		$jsonArr['posted_date']=$jobDetail->posted_date;	
		$jsonArr['location']=$jobDetail->location;	
		$jsonArr['organization']=$jobDetail->organization;	
		$jsonArr['employementType']=$jobDetail->employementType;	
		$jsonArr['seniorityLevel']=$jobDetail->seniorityLevel;	
		$jsonArr['industries']=$jobDetail->industries;	
		$jsonArr['companyUrl']=$jobDetail->companyUrl;	
		$jsonArr['profile_url']=$jobDetail->profile_url;	
		$jsonArr['twitterURL']=$jobDetail->twitterURL;	
		$jsonArr['facebookURL']=$jobDetail->facebookURL;	
		$jsonArr['jobFunction']=$jobDetail->jobFunction;	
		$jsonArr['companyWebsite']=$jobDetail->companyWebsite;	
		$jsonArr['employees']=$jobDetail->employees;	
		$jsonArr['employeesOnLinkedin']=$jobDetail->employeesOnLinkedin;	
		$jsonArr['emails']=$jobDetail->emails;	
		$jsonArr['scraped_date']=$jobDetail->scraped_date;	
		$jsonArr['exact_match']=$jobDetail->exact_match;	
		$jsonArr['decision_maker']=$jobDetail->decision_maker;
		$jsonArr['job_poster_name']=$jobDetail->job_poster_name;
		$jsonArr['job_poster_url']=$jobDetail->job_poster_url;
		return $jsonArr;
	}	
	
}

