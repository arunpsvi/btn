<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Salerecordsarchive extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->model('archive_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
    }

    public function index() {
		$defaultWebsiteId=0;
		if(!empty($this->input->get('website_id'))){
			$defaultWebsiteId=$this->input->get('website_id');
		}
		$website_list = $this->common_model->get_all_websites('SALE');
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
			$this->common_model->delete('saleresults',$deleteCondition);
			redirect(site_url('admin/salerecordsarchive?website_id='.$condition['res.website_id']),'refresh');
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
		}

		if(!empty($data['hidSelectedBots'])){
			$botIds=preg_replace('/BOT_/i','',$data['hidSelectedBots']);
			$botIdArr=explode(',',$botIds);
		}
		
		//print $data['hidSelectedBots'];exit;
		//print_r($botArray);exit;
		$config['base_url'] = site_url().'/admin/downloadarchive/';
		$config['uri_segment'] = 3;
		$config['per_page'] = 500;
		$config['total_rows'] = $this->archive_model->get_sale_listings_total($condition,$botIdArr);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();
		
		$qualifyArray=svi_buildArray($this->common_model->get_qualify(),'name','name',' ');
		$qualifySearchArray=svi_buildArray($this->common_model->get_qualify(),'name','name','All');
		#$qualifySearchArray['-----']='---------------------';
		#$qualifySearchArray['ADD#NEW']='Add new';

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
		}else{
			#$data['scraped_date']= date('Y-m-d');
		}
		
		$jobListings = $this->archive_model->get_sale_listings($condition, $config['per_page'], $page,$sort_order,$botIdArr);
		$data['jobListings'] = $jobListings;
		$data['total_rows'] = $config['total_rows'];
		$data['main_content'] =$this->load->view('admin/sale-archive/searchform', $data, TRUE);
		if($data['website_id']==4){
			$data['main_content'] .= $this->load->view('admin/sale-archive/craiglist', $data, TRUE);
		}
		$data['main_content'] .= $this->load->view('admin/sale-archive/download_footer', $data, TRUE);
	    $this->load->view('admin/index', $data);
    }

	function fetchDetails($result_id){
		$condition=Array();
		$condition['result_id']=$result_id;
		$jobDetails=$this->archive_model->get_sale_details($condition);
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
		$jsonArr['result_id']=$jobDetail->result_id;	
		$jsonArr['bot_name']=$this->db->get_where('keywords',array('keyword_id'=>$jobDetail->search_id))->row()->search_name;	
		$jsonArr['website_name']=$this->db->get_where('websites',array('id'=>$jobDetail->website_id))->row()->name;	
		#$jsonArr['suburl_id']=$jobDetail->suburl_id;	
		#$jsonArr['sub_category_id']=$jobDetail->sub_category_id;	
		$jsonArr['job_url']=$jobDetail->job_url;	
		$jsonArr['title']=$jobDetail->title;	
		$jsonArr['price']=$jobDetail->price;	
		$jsonArr['description']=$jobDetail->description;	
		$jsonArr['keywords']=$jobDetail->keywords;	
		$jsonArr['qualify']=$jobDetail->qualify;	
		$jsonArr['name']=$jobDetail->name;	
		$jsonArr['phone']=$jobDetail->phone;	
		$jsonArr['email']=$jobDetail->email;	
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
		return $jsonArr;
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

		$subcategories_list = $this->common_model->get_all_subcategories();

		$subcategoriesArr=svi_buildArray($subcategories_list,'sub_cat_id','sub_category_name');
		$subcategoriesCatIdArr=svi_buildArray($subcategories_list,'sub_cat_id','category_id');

		$categories_list = $this->common_model->get_all_categories();
		$categoriesArr=svi_buildArray($categories_list,'category_id','category_name');
		
		
		if(empty($defaultWebsiteId)){
			foreach($websiteListArr as $key=>$value){
				$defaultWebsiteId=$key;
				break;
			}
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
		if(!empty($this->input->get('qualify'))){
			$condition['res.qualify']= $this->input->get('qualify');
		}
		if(!empty($this->input->get('bot_name'))){
			$botIds=preg_replace('/BOT_/i','',$this->input->get('bot_name'));
			$botIdArr=explode(',',$botIds);
		}
		
		$data['website_id']=$defaultWebsiteId;
		$data['websiteListArr'] = $websiteListArr;
		$jobListings = $this->archive_model->get_sale_listings($condition,'','','',$botIdArr);
		
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
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Price'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Description'); 
		
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
			$categoryId=$subcategoriesCatIdArr[$jobListing['sub_category_id']];
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $jobListing['phone']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $jobListing['email']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $websiteListArr[$jobListing['website_id']]); 
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $jobListing['scraped_date']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $jobListing['posted_date']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $jobListing['location']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $categoriesArr[$categoryId]);
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $subcategoriesArr[$jobListing['sub_category_id']]); 
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $jobListing['keywords']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $jobListing['title']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $jobListing['job_url']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $jobListing['exact_match']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $jobListing['price']); 
			$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $jobListing['description']); 
			$rowCount++;
		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

        $objWriter->save('uploads/'.$fileName);
        header("Content-Type: application/vnd.ms-excel");
		redirect(base_url().'uploads/'.$fileName);

	}

    
	public function delete() {
		
		$record_delete=$this->input->post('record_delete');
		$recordsToDelete='';
		foreach($record_delete as $key=>$val){
			$recordsToDelete .="'".$val."',";
		}
		$recordsToDelete=rtrim($recordsToDelete,',');
		
		if(!empty($record_delete)){
			$this->archive_model->deleteByIn('saleresults','result_id',$record_delete);
			$this->session->set_flashdata('msg', 'Records deleted successfully');	
		}else{
			$this->session->set_flashdata('error_msg', 'Nothing selected to delete');
		}
		$url="website_id=".$this->input->post('website_id')."&scraped_date=".$this->input->post('scraped_date')."&qualify=".$this->input->post('qualify');
		redirect(site_url('admin/salerecordsarchive?'.$url),'refresh');
	}	
}

