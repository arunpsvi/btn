<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Diagnostictools extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		if(!in_array($this->session->userdata('role'), $this->config->item('adminAccess'))){
			redirect(site_url('admin/dashboard'));
		}
		$this->load->model('common_model');
		$this->load->model('salerecords_model');
		$this->load->model('diagnostic_model');
		$this->load->model('bot_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
    }
	public function index() {
		$this->proxy();
	}

	public function salesBots(){

		$postType='SALE';

		$config['base_url'] = site_url().'/admin/diagnostictools/salesBots';
		$config['uri_segment'] = 3;
		$config['per_page'] = 50;
		$config['total_rows'] = $this->diagnostic_model->getDiagnosticSchedulesTotal($postType);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();
		
		$schedules=$this->diagnostic_model->getDiagnosticSchedules($postType,$condition, $config['per_page'], $page,$sort_order);

		$data['proxies'] = $this->common_model->get_all_proxy();
		$proxyStatus= $this->common_model->get_all_proxy_status();
		$proxyStatusData=Array();
		foreach ($proxyStatus as $status){
			#websiteID_ProxyID
			$key=$status['website_id'].'_'.$status['proxy_id'];
			$proxyStatusData[$key]=$status;
		}
		
		$data['schedules'] = $schedules;
		$data['controller'] = $this;
		$data['total_rows'] = $config['total_rows'];
		$data['main_content'] = $this->load->view('admin/diagnostictools/jobslist', $data, TRUE);
		
	    $this->load->view('admin/index', $data);
	}

	public function jobsBots(){

		$postType='JOBS';
		$config['base_url'] = site_url().'/admin/diagnostictools/jobsBots';
		$config['uri_segment'] = 3;
		$config['per_page'] = 50;
		$config['total_rows'] = $this->diagnostic_model->getDiagnosticSchedulesTotal($postType);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();
		
		$schedules=$this->diagnostic_model->getDiagnosticSchedules($postType,$condition, $config['per_page'], $page,$sort_order);

		$data['proxies'] = $this->common_model->get_all_proxy();
		$proxyStatus= $this->common_model->get_all_proxy_status();
		$proxyStatusData=Array();
		foreach ($proxyStatus as $status){
			#websiteID_ProxyID
			$key=$status['website_id'].'_'.$status['proxy_id'];
			$proxyStatusData[$key]=$status;
		}
		
		$data['schedules'] = $schedules;
		$data['controller'] = $this;
		$data['total_rows'] = $config['total_rows'];
		$data['main_content'] = $this->load->view('admin/diagnostictools/jobslist', $data, TRUE);
		
	    $this->load->view('admin/index', $data);
	}

    public function proxy() {
		
		$data['proxies'] = $this->common_model->get_all_proxy();
		$proxyStatus= $this->common_model->get_all_proxy_status();
		$proxyStatusData=Array();
		foreach ($proxyStatus as $status){
			#websiteID_ProxyID
			$key=$status['website_id'].'_'.$status['proxy_id'];
			$proxyStatusData[$key]=$status;
		}
		$data['proxyStatusData'] = $proxyStatusData;
		$data['controller'] = $this;
		$data['total_rows'] = $config['total_rows'];
		$data['main_content'] = $this->load->view('admin/diagnostictools/proxycheck', $data, TRUE);
		
	    $this->load->view('admin/index', $data);
    }

	public function getHtml($proxy,$proxyStatusData,$websiteID){
		
		$statusText='';
		$statusClass='';
		$key=$websiteID."_".$proxy['id'];
		if($proxyStatusData[$key]['fail']=='0'){
			$statusText='Success';  
			$statusClass='label-success';  
		}else if($proxyStatusData[$key]['success']=='0'){
			$statusText='Failed';  
			$statusClass='label-danger'; 
		}else{
			$statusText='Partial Success'; 
			$statusClass='label-warning'; 
		} 
		$websiteName=$proxyStatusData[$key]['website_name'];                                  
		//echo "Website Name:- <b>".$proxyStatusData[$key]['website_name'].'</b>';
		echo "Attempts:- <b>".$proxyStatusData[$key]['attempts'].'</b><br>';
		echo "Success:- <b>".$proxyStatusData[$key]['success'].'</b><br>';
		echo "Fail:- <b>".$proxyStatusData[$key]['fail'].'</b><br>';
		echo "Checked AT:- <b>".$proxyStatusData[$key]['date_checked'].'</b><br>';
		echo "<label class='label label-table $statusClass' >$statusText</label>";

		$pb_id=$this->db->get_where('proxy_block',array('proxy_id'=>$proxy['id'],'website_id'=>$websiteID))->row()->pb_id;
		if(!empty($pb_id)){
			#$key=$websiteID."_".$proxy['id'];
			$id=$key."_O";
			echo "<a class='proxy_block_website' id='$id' href='#' title='Open Proxy For $websiteName'><i class='fa fa-check text-success m-l-30'></i></a>";			
		}else{
			$id=$key."_B";
			echo "<a class='proxy_open_website' id='$id' href='#' title='Block Proxy For $websiteName'><i class='fa fa-ban text-danger m-l-30'></i></a>";			
		}
	}

	public function checkproxy(){
		$this->common_model->truncateTable('proxy_status');
		$this->load->library('curl');
		$arrProxies=$this->common_model->get_all_proxy();
		$condition=Array('allow_proxy_test'=>'1');
		$websites=$this->common_model->getWebsitesforProxyCheck($condition);
		foreach ($arrProxies as $proxy){
			foreach ($websites as $web){				
				for($i=1;$i<3;$i++){
					$response=$this->fetchPage($web['proxy_test_url'],$proxy);
					$this->saveRecords($proxy,$web,$i,$response);
				}				
			}
		}
		echo "1";
	}

	private function saveRecords($proxy,$web,$attempt,$response){

		$proxyStatus=$this->db->get_where('proxy_status',array('proxy_id'=>$proxy['id'],'website_id'=>$web['id']))->row();
		$dataToSave=Array();
				
		$dataToSave['proxy_id']=$proxy['id'];
		$dataToSave['website_id']=$web['id'];
		$dataToSave['website_name']=$web['name'];
		$dataToSave['checked_url']=$web['proxy_test_url'];
		$dataToSave['checked_by']=$this->session->userdata('id');
		$dataToSave['attempts']=$attempt;
		if(!empty($response)){
			$dataToSave['success']='1';
		}else{
			$dataToSave['fail']='1';
		}
		if(empty($proxyStatus->ps_id)){
			$dataToSave = $this->security->xss_clean($dataToSave);
			$psID=$this->common_model->insert($dataToSave, 'proxy_status');
		}else{
			$dataToSave['attempts']=$proxyStatus->attempts+1;
			$dataToSave['date_checked']=date('Y-m-d H:i:s');
			if(!empty($response)){
				$dataToSave['success']=$proxyStatus->success+1;
			}else{
				$dataToSave['fail']=$proxyStatus->fail+1;
			}
			$dataToSave = $this->security->xss_clean($dataToSave);
			$condition = array(
				'ps_id' => $proxyStatus->ps_id
			);
			$this->common_model->update($dataToSave, 'proxy_status',$condition);
		}		
	}

	private function fetchPage($url,$proxyDetail){
        
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
		$this->curl->option('connecttimeout', 60);
		
		$this->curl->option('PROXYPORT', $proxyDetail['port']);
		$this->curl->option('PROXYTYPE', 'HTTPS');
		$this->curl->option('PROXY', $proxyDetail['ip']);
		$this->curl->option('PROXYUSERPWD', $proxyDetail['uname'].":".$proxyDetail['password']);
		
		//  To Execute 'option' Array Into cURL Library & Store Returned Data Into $data
		$data = $this->curl->execute();
		//  To Display Returned Data
		return $data;

	}

	/*
		block / Unblock proxy for specific website.
	*/
    public function blockUnblock($websiteID,$proxyID,$status)
    {	
		if(!empty($websiteID) && !empty($proxyID) && !empty($status)){
			if($status=='B'){
				$pgID=$this->db->get_where('proxy_block',array('proxy_id'=>$proxyID,'website_id'=>$websiteID))->row()->pb_id;
				if(empty($pgID)){
					$dataToSave=Array();
					$dataToSave['proxy_id']=$proxyID;
					$dataToSave['website_id']=$websiteID;
					$dataToSave['blocked_by']=$this->session->userdata('id');
					$dataToSave = $this->security->xss_clean($dataToSave);
					$psID=$this->common_model->insert($dataToSave, 'proxy_block');
				}
			}elseif($status=='O'){
				$condition['proxy_id']=$proxyID;
				$condition['website_id']=$websiteID;
				$res=$this->common_model->delete('proxy_block',$condition);
			}else{
				
			}			
			print "1";
		}else{
			print "0";
		}
    }

	function resetBot($scheduleID){
		if(!empty($scheduleID)){
			$condition = array(
				'schedule_id' => $scheduleID
			);
			$dataToInsert=Array();
			$dataToInsert['status']='T';
			$dataToInsert['reset_by']=$this->session->userdata('id');
			$dataToInsert['completion_time']=date('Y-m-d h:i:s');
			$dataToInsert = $this->security->xss_clean($dataToInsert);
			$this->common_model->update($dataToInsert, 'schedules',$condition);
			echo "1";
		}else{
			echo "0";
		}
	}

	function viewLog($scheduleID){

		$condition=Array();
		$condition['keyword_id']=$this->db->get_where('schedules',array('schedule_id'=>$scheduleID))->row()->keyword_id;
		$botDetails=$this->bot_model->get_bot_details($condition);
				
		if(empty($botDetails->keyword_id)){
			$jsonArr['message']='Records not found.';
		}else{			
			$condition=Array();
			$condition['schedule_id']=$scheduleID;
			$logDetails=$this->diagnostic_model->get_log_details($condition);
			if(count($logDetails)==0){
				$jsonArr['message']='Records not found.';
			}else{
				$jsonArr['message']='';
				$jsonArr=$this->createJsonObj($logDetails);
			}			
			$jsonArr['botName']=$botDetails->search_name;
		}
		print(json_encode($jsonArr));
	}

	# createArray -- Create array of all columns available in the table of database
	function createJsonObj($logDetails){
		$jsonArr=Array();
		foreach ($logDetails as $logDetail){
			$jsonData=Array();
			$jsonData['url']=$logDetail['url'];
			$jsonData['message']=$logDetail['message'];
			$jsonData['proxy']=$logDetail['proxy'];
			$jsonData['status']=$logDetail['status'];
			$jsonData['log_time']=$logDetail['log_time'];
			$jsonArr['results'][]=$jsonData;
		}
		return $jsonArr;
	}	
}