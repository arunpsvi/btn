<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Bots extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->model('bot_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');	
		if(!in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
			redirect(site_url('admin/dashboard'));
		}
		
    }
	public function index(){
		if(in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
			$breadcrumbs[]='<li><a href="'.site_url('admin/bots/add').'">Add New</a></li>';
		}
		$condition=Array();
		$searches=$this->common_model->get_all_searches($condition,'','SALE');

		$website_list = $this->common_model->get_all_websites('SALE');
		$websiteListArr=svi_buildArray($website_list,'id','url');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		$data['searches'] = $searches;
		$data['websiteListNameArr'] = $websiteListNameArr;
		$data['breadcrumbs']=$breadcrumbs;
		$data['main_content'] = $this->load->view('admin/bots/list', $data, TRUE);
	    $this->load->view('admin/index', $data);	
	}
    public function add() {

		if(empty($this->input->post('scrape_all'))){
			$this->form_validation->set_rules('keywords', 'keywords', 'required');
		}
		$this->form_validation->set_rules('website_id', 'Website', 'required');	
		$this->form_validation->set_rules('search_name', 'Search name', 'required');	
		$data['formData']=$this->input->post();
		$subcategories=$data['formData']['subcategories'];
		$defaultWebsiteId=0;
		$subcategoriesToStr='';
		if(!empty($this->input->get('website_id'))){
			$defaultWebsiteId=$this->input->get('website_id');
		}
		foreach ($subcategories as $subcategory){
			if(empty($data['hidSelectedOptions'])){
				$data['hidSelectedOptions'] .=$subcategory;
				$subcategoriesToStr .=$subcategory;
			}else{
				$subcategoriesToStr .=','.$subcategory;
			}
		}
		$locations=$data['formData']['locations'];
		$defaultWebsiteId=0;
		$locationsToStr='';
		if(!empty($this->input->get('website_id'))){
			$defaultWebsiteId=$this->input->get('website_id');
		}
		foreach ($locations as $location){
			if(empty($data['locations'])){
				$data['locations'] .=$location;
				$locationsToStr .=$location;
			}else{
				$locationsToStr .=','.$location;
			}
		}

		if ($this->form_validation->run() == false) {
			$website_list = $this->common_model->get_all_websites('SALE');
			$websiteListArr=svi_buildArray($website_list,'id','url');
			$websiteListNameArr=svi_buildArray($website_list,'id','name');
			$data['loadDB']=$this->input->get('loadDB');
			$data['website_id']=$this->input->get('website_id');
			if(empty($data['website_id'])){
				$data['website_id']=4;# Default website ID is 4 for crsiglist
			}
			$condition=Array(
				'website_id' => $data['website_id']
			);

			$subcategories_list = $this->common_model->get_all_subCategories($condition);
			$subcategoriesArr=svi_buildArray($subcategories_list,'sub_cat_id','sub_category_name');
			$activeUserList=$this->common_model->get_all_activeusers();
			$activeUsersArr=svi_buildArray($activeUserList,'user_id','fullname');
			$websiteLists = $this->common_model->get_all_suburls();	
			$locationsArr=svi_buildArray($websiteLists,'suburl_id','name');

			if(empty($defaultWebsiteId)){
				foreach($websiteListArr as $key=>$value){
					$defaultWebsiteId=$key;
					break;
				}
			}
			
			$dbKeywords='';
			if(!empty($data['loadDB']) && $data['loadDB']==1){
				$condition = array(
					'website_id' => $data['website_id']
				);
				$allKeywords=$this->common_model->get_all_keywords($condition);
				
				foreach ($allKeywords as $keyword){
					if(empty($dbKeywords)){
						$dbKeywords .=$keyword['keyword'];
					}else{
						$dbKeywords .=','.$keyword['keyword'];
					}
				}
				$data['keywords'] = $dbKeywords;
			}else{
				$data['keywords'] = $data['formData']['keywords'];
			}
			
			foreach ($subcategories as $subcategory){
				if(empty($data['hidSelectedOptions'])){
					$data['hidSelectedOptions'] .=$subcategory;
				}else{
					$data['hidSelectedOptions'] .=','.$subcategory;
				}
			}
				
			$data['action'] = site_url('admin/bots/add');
			$data['website_id']=$defaultWebsiteId;
			$data['websiteListArr'] = $websiteListArr;
			$data['websiteListNameArr'] = $websiteListNameArr;
			$data['subcategoriesArr'] = $subcategoriesArr;
			$data['activeUsersArr'] = $activeUsersArr;
			$data['locationsArr'] = $locationsArr;
			$data['search_name'] = $this->input->get('sn');
			$data['main_content'] = $this->load->view('admin/bots/add', $data, TRUE);
	        $this->load->view('admin/index', $data);
		}else{
			$scrape_all='N';
			if(!empty($this->input->post('scrape_all'))){
				$scrape_all='Y';
			}
			$data = array(
				'website_id' => $this->input->post('website_id'),
				'search_name' => $this->input->post('search_name'),
				'posted_date_start' => $this->input->post('posted_date_start'),
				'posted_date_end' => $this->input->post('posted_date_end'),
				'exact_match' => $this->input->post('exact_match'),
				'keywords' => $this->input->post('keywords'),
				'scrape_all' => $scrape_all,
				'locations' => $locationsToStr,
				'subcategories' => $subcategoriesToStr,
				'created_date' => date('Y-m-d')
			);
			
			$data = $this->security->xss_clean($data);
			$keywordId = $this->common_model->insert($data, 'keywords');
			$keywords=explode(",",$this->input->post('keywords'));
			foreach ($keywords as $keyword){
				$keyword=trim($keyword);
				if(empty($this->db->get_where('keyword_list',array('website_id'=>$this->input->post('website_id'),'keyword'=>$keyword))->row()->id)){
					$data = array(
						'website_id' => $this->input->post('website_id'),
						'keyword' => $keyword
					);
					$this->common_model->insert($data, 'keyword_list');
				}
			}

			if(!empty($keywordId)){
				$condition=array(
					'search_id' => $keywordId
				);
				$this->common_model->delete('search_access',$condition);
				$useraccessArr=$this->input->post('useraccess');
				foreach ($useraccessArr as $userID){
					$data = array(
						'user_id' => $userID,
						'search_id' => $keywordId
					);
					$this->common_model->insert($data, 'search_access');					
				}
			}
			$this->session->set_flashdata('msg', 'keywords updated Successfully');						
			redirect(site_url('admin/bots'),'refresh');
		}		
    }

	function update($keywordID){
	
		$searchData=$this->common_model->getSearchByid($keywordID);	
		$userAccessData=$this->common_model->get_users_by_searchid($keywordID);	
		if(empty($this->input->post('scrape_all'))){
			$this->form_validation->set_rules('keywords', 'keywords', 'required');
		}
		$this->form_validation->set_rules('website_id', 'Website', 'required');	
		$this->form_validation->set_rules('search_name', 'Search name', 'required');	
		if ($this->form_validation->run() == false) {
			$website_list = $this->common_model->get_all_websites('SALE');
			$websiteListArr=svi_buildArray($website_list,'id','url');
			$websiteListNameArr=svi_buildArray($website_list,'id','name');
			
			$condition=Array(
				'website_id' => $searchData->website_id
			);           

			$subcategories_list = $this->common_model->get_all_subCategories($condition);
			$subcategoriesArr=svi_buildArray($subcategories_list,'sub_cat_id','sub_category_name');	
			$activeUserList=$this->common_model->get_all_activeusers();
			$activeUsersArr=svi_buildArray($activeUserList,'user_id','fullname');
			
			$locations=$searchData->locations;
			$locations=preg_replace('/,/is',"','",$locations);
			$locations ="'$locations'";
			$alllocations=$this->common_model->get_all_suburls();
			$location=svi_buildArray($alllocations,'suburl_id','name');
			
			$data['website_id']=$searchData->website_id;
			
			$userAccessDataToStr='';			
			foreach ($userAccessData as $userAccess){
				if(empty($data['hidSelectedUserAccess'])){
					$data['hidSelectedUserAccess'] .=$userAccess['user_id'];
					$userAccessDataToStr .=$userAccess['user_id'];;
				}else{
					$userAccessDataToStr .=','.$userAccess['user_id'];
				}
			}

			$data['action'] = site_url('admin/bots/update/'.$keywordID);
			$data['websiteListArr'] = $websiteListArr;
			$data['websiteListNameArr'] = $websiteListNameArr;
			$data['subcategoriesArr'] = $subcategoriesArr;
			$data['activeUsersArr'] = $activeUsersArr;
			$data['loadDB']=$this->input->get('loadDB');
			
			$data['keywords']=$searchData->keywords;
			$data['search_name']=$searchData->search_name;
			$data['search_id']=$keywordID;
			$data['posted_date_start']=$searchData->posted_date_start;
			$data['posted_date_end']=$searchData->posted_date_end;
			$data['exact_match']=$searchData->exact_match;
			$data['scrape_all']=$searchData->scrape_all;
			$data['locationsArr']=$location;
			$data['hidSelectedOptions']=$searchData->subcategories;
			$data['hidSelectedUserAccess']=$userAccessDataToStr;
			$data['hidSelectedLocations']=$searchData->locations;
			
			
			$dbKeywords='';
			if(!empty($data['loadDB']) && $data['loadDB']==1){
				$condition = array(
					'website_id' => $data['website_id']
				);
				$allKeywords=$this->common_model->get_all_keywords($condition);
				
				foreach ($allKeywords as $keyword){
					if(empty($dbKeywords)){
						$dbKeywords .=$keyword['keyword'];
					}else{
						$dbKeywords .=','.$keyword['keyword'];
					}
				}
				$data['keywords'] = $dbKeywords;
			}	

			$data['main_content'] = $this->load->view('admin/bots/add', $data, TRUE);
			$this->load->view('admin/index', $data);
		}else{

			$subcategories=$this->input->post('subcategories');			
			$subcategoriesToStr='';			
			foreach ($subcategories as $subcategory){
				if(empty($data['hidSelectedOptions'])){
					$data['hidSelectedOptions'] .=$subcategory;
					$subcategoriesToStr .=$subcategory;
				}else{
					$subcategoriesToStr .=','.$subcategory;
				}
			}	
			$scrape_all='N';
			if(!empty($this->input->post('scrape_all'))){
				$scrape_all='Y';
			}
			$locations=$this->input->post('locations');	
			$locationsToStr='';	
			foreach ($locations as $location){
				if(empty($data['hidSelectedLocations'])){
					$data['hidSelectedLocations'] .=$location;
					$locationsToStr .=$location;
				}else{
					$locationsToStr .=','.$location;
				}
			}	

			$data = array(
				'website_id' => $this->input->post('website_id'),
				'search_name' => $this->input->post('search_name'),
				'posted_date_start' => $this->input->post('posted_date_start'),
				'posted_date_end' => $this->input->post('posted_date_end'),
				'exact_match' => $this->input->post('exact_match'),
				'keywords' => $this->input->post('keywords'),
				'scrape_all' => $scrape_all,
				'subcategories' => $subcategoriesToStr,
				'locations' => $locationsToStr,
				'created_date' => date('Y-m-d')
			);
			$condition = array(
				'keyword_id' => $keywordID
			);
			$data = $this->security->xss_clean($data);
			$this->common_model->update($data, 'keywords',$condition);
			$keywords=explode(",",$this->input->post('keywords'));
			foreach ($keywords as $keyword){
				$keyword=trim($keyword);
				if(empty($this->db->get_where('keyword_list',array('website_id'=>$this->input->post('website_id'),'keyword'=>$keyword))->row()->id)){
					$data = array(
						'website_id' => $this->input->post('website_id'),
						'keyword' => $keyword
					);
					$this->common_model->insert($data, 'keyword_list');
				}
			}
			if(!empty($keywordID)){
				$condition=array(
					'search_id' => $keywordID
				);
				$this->common_model->delete('search_access',$condition);
				$useraccessArr=$this->input->post('useraccess');
				foreach ($useraccessArr as $userID){
					$data = array(
						'user_id' => $userID,
						'search_id' => $keywordID
					);
					$this->common_model->insert($data, 'search_access');					
				}
			}
			$this->session->set_flashdata('msg', 'Search updated Successfully');						
			redirect(site_url('admin/bots'),'refresh');

		}
		
	}
	
	function checkunique(){		
		$search_name=trim($this->input->get('search_name'));
		$searchId = $this->db->get_where('svi_keywords',array('search_name'=>"$search_name"))->row()->keyword_id;
	#	print $this->db->last_query();
		#print "$search_name --> $searchId == > ".$this->input->get('search_id');
		if((!empty($searchId) && $searchId==$this->input->get('search_id')) || empty($searchId)){
			print "true";
		}else{
			print "false";
		}
	}

	function deleteSearch($searchId){		
		$condition = array(
			'keyword_id' => $searchId
		);
		$this->common_model->delete('keywords',$condition);
		$this->session->set_flashdata('msg', 'Search deleted Successfully');						
		redirect(site_url('admin/scrape'),'refresh');
	}

	function fetchDetails($bot_id){
		$condition=Array();
		$condition['keyword_id']=$bot_id;
		$botDetails=$this->bot_model->get_bot_details($condition);
		
		if(empty($botDetails->keyword_id)){
			$jsonArr['message']='Records not found.';
		}else{			
			$jsonArr=$this->createJsonObj($botDetails);
			$jsonArr['message']='';
		}			
		print(json_encode($jsonArr));
	}

	# createArray -- Create array of all columns available in the table of database
	function createJsonObj($jobDetail){
		$searchData['keyword_id']=$jobDetail->keyword_id;
		$resultStatus=$this->common_model->getLastScrapeStatus($searchData);
		
		if($jobDetail->exact_match =='on'){
			$jobDetail->exact_match='Yes';
		}else{
			$jobDetail->exact_match='No';
		}

		$search['keywords']=preg_replace('/,/is',', ',$jobDetail->keywords);
		$website=$this->db->get_where('svi_websites',array('id'=>$jobDetail->website_id))->row()->name;
		$subCategories=$jobDetail->subcategories;
		$subCategories=preg_replace('/,/is',"','",$subCategories);
		$subCategories ="'$subCategories'";

		$locations=$jobDetail->locations;
		$locations=preg_replace('/,/is',"','",$locations);
		$locations ="'$locations'";
		
		
		$categoryData=$this->common_model->get_subcategories($subCategories);
		$alllocations=$this->common_model->get_alllocations($locations);

		$location=$this->db->get_where('svi_suburls',array('suburl_id'=>$search['locations']))->row()->name;
		
		
		
		$subCategoriesTxt='';
		foreach ($categoryData as $category){
			if(empty($subCategoriesTxt)){
				$subCategoriesTxt .=$category['sub_category_name'];
			}else{
				$subCategoriesTxt .=','.$category['sub_category_name'];
			}
		}	
		
		$locationTxt='';
		foreach ($alllocations as $locations){
			if(empty($locationTxt)){
				$locationTxt .=$locations['name'];
			}else{
				$locationTxt .=','.$locations['name'];
			}
		}	

		$condition=Array(
			'keyword_id'=>$search['keyword_id']
			);
		$scheduleRecord=$this->common_model->get_latest_schedule($condition);

		$jsonArr=Array();
		$jsonArr['website_name']=$this->db->get_where('websites',array('id'=>$jobDetail->website_id))->row()->name;	
		$jsonArr['subcategories']=$subCategoriesTxt;
		$jsonArr['locations']=$locationTxt;		
		$jsonArr['keywords']=$jobDetail->keywords;	
		$jsonArr['bot_name']=$jobDetail->search_name;	
		$jsonArr['posted_date_start']=$jobDetail->posted_date_start;	
		$jsonArr['posted_date_end']=$jobDetail->posted_date_end;	
		$jsonArr['exact_match']=$jobDetail->exact_match;	
		$jsonArr['created_date']=$jobDetail->created_date;	
		$jsonArr['next_schedule_time']=$scheduleRecord->scheduled_time;	

		return $jsonArr;
	}    
}

