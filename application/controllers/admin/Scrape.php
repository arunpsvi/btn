<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Scrape extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');
		if(!in_array($this->session->userdata('role'), $this->config->item('adminAccess'))){
			#redirect(site_url('admin/dashboard'));
		}
    }

    public function index() {
		
		$condition=Array();
		$searches=$this->common_model->get_all_searches($condition,'','JOBS');

		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		$data['searches'] = $searches;
		$data['websiteListNameArr'] = $websiteListNameArr;
		$data['main_content'] = $this->load->view('admin/scrape/scrape', $data, TRUE);
	    $this->load->view('admin/index', $data);
	}

	public function addSchedule1($keywordId) {
		
		$keywordData = $this->common_model->getSearchByid($keywordId);
		
		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		$this->form_validation->set_rules('start_date', 'start date', 'required');
		$this->form_validation->set_rules('numdays', 'Number of days', 'required');	
		if ($this->form_validation->run() == false) {
			$data['searches'] = $searches;
			$data['keywordData'] = $keywordData;
			$data['websiteListNameArr'] = $websiteListNameArr;
			$data['main_content'] = $this->load->view('admin/scrape/addschedule', $data, TRUE);
			$this->load->view('admin/index', $data);
		}else{
			
			$numdays = $this->input->post('numdays');
			$startDate = $this->input->post('start_date');
			$website_id = $this->input->post('website_id');
			$keyword_id = $this->input->post('keyword_id');
			$start_time = $this->input->post('start_time');

			$dataArray=array();
			$dataArray['website_id']=$website_id;
			$dataArray['keyword_id']=$keyword_id;
			$dataArray['start_date']=$startDate;
			$dataArray['no_of_days']=$numdays;
			$dataArray['time_to_start']=$start_time;
			if(!empty($this->input->post('monday'))){
				$dataArray['monday']='Y';
			}else{
				$dataArray['monday']='N';
			}
			if(!empty($this->input->post('tuesday'))){
				$dataArray['tuesday']='Y';
			}else{
				$dataArray['tuesday']='N';
			}
			if(!empty($this->input->post('wednesday'))){
				$dataArray['wednesday']='Y';
			}else{
				$dataArray['wednesday']='N';
			}
			if(!empty($this->input->post('thursday'))){
				$dataArray['thursday']='Y';
			}else{
				$dataArray['thursday']='N';
			}
			if(!empty($this->input->post('friday'))){
				$dataArray['friday']='Y';
			}else{
				$dataArray['friday']='N';
			}
			if(!empty($this->input->post('saturday'))){
				$dataArray['saturday']='Y';
			}else{
				$dataArray['saturday']='N';
			}
			if(!empty($this->input->post('sunday'))){
				$dataArray['sunday']='Y';
			}else{
				$dataArray['sunday']='N';
			}

			$dataArray = $this->security->xss_clean($dataArray);
			$sr_id=$this->common_model->insert($dataArray, 'schedule_records');
			
			$next_date=$startDate;
			for($i=1;$i<=$numdays;$i++){
				$dayOfWeek=strtolower(date('l', strtotime($next_date)));
				$data=array();
				$data['sr_id']=$sr_id;
				$data['website_id']=$website_id;
				$data['keyword_id']=$keyword_id;
				$data['scheduled_time']=$next_date." $start_time:00:00";

				$data = $this->security->xss_clean($data);
				$keywordId = $this->common_model->insert($data, 'schedules');

				$next_date = date('Y-m-d', strtotime($next_date .' +1 day'));				
			}
			redirect(site_url('admin/scrape/addSchedule/'.$keywordId),'refresh');
		}
	}
	public function favourite($bot_id) {		
		$data=array();
		$data['bot_id']=$bot_id;
		$data['user_id']=$this->session->userdata('id');
		$data = $this->security->xss_clean($data);
		$keywordId = $this->common_model->insert($data, 'favoritebots');
	} 
	public function unfavourite($bot_id) {		
		$condition=array();
		$condition['bot_id']=$bot_id;
		$condition['user_id']=$this->session->userdata('id');
		$condition = $this->security->xss_clean($condition);
		$keywordId = $this->common_model->delete( 'favoritebots',$condition);
	}    
}

