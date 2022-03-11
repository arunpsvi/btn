<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Apimanagement extends CI_Controller {

	public function __construct(){
        parent::__construct();
        check_login_user();
		$this->load->model('common_model');		
		$this->load->model('login_model');
		
    }

    public function index() {		
        if($this->session->userdata('role')=='ADMIN' || $this->session->userdata('role')=='ENDUSER'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}
		$data = array();
        $data['page_title'] = 'API Management';
		
		$roles = $this->common_model->get_user_roles();
		$userRoles=Array();
		foreach ($roles as $role){
			$userRoles[$role['role_id']]=$role['role_display'];
		}
		//echo"<pre>";
		//print_r($this->session->userdata);exit;
		$userId=$this->session->userdata['id'];
		$userDetails = $this->common_model->get_single_user_info($userId);
		//echo"<pre>";
		//print_r($userDetails);exit;

		$data['user_roles']=$userRoles;
		$data['api_key']=$userDetails->api_key;
		$data['api_access']=$userDetails->api_access;
		$data['action']=site_url('admin/apimanagement/');
        $data['main_content'] = $this->load->view('admin/apimanagement/api_home', $data, TRUE);
        $this->load->view('admin/index', $data);
    }

    //-- add new user by admin
    public function add() {   
		
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}

        if ($this->input->post()) {

            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'password' => md5($this->input->post('password')),
                'mobile' => $this->input->post('mobile'),
                'category' => $this->input->post('user_role'),
                'email_notification' => $this->input->post('email_notification'),
                'api_access' => $this->input->post('api_access'),
                'status' => '1',
                'date_created' => current_datetime()
            );           
            //-- check duplicate email
            $email = $this->common_model->check_email($this->input->post('email'));
            if (empty($email)) {
				
				if($this->input->post('api_access')==1){
					$apiKey=md5(date('d-m-y h:i:s').uniqid());
					$data['api_key']=$apiKey;
				}
				$user_id = $this->common_model->insert($data, 'users');	
				if(!empty($user_id)){
					$useraccessArr=$this->input->post('useraccess');
					foreach ($useraccessArr as $searchID){
						$data = array(
							'user_id' => $user_id,
							'search_id' => $searchID
						);
						$this->common_model->insert($data, 'search_access');					
					}
				}
				
				$this->session->set_flashdata('msg', 'User added Successfully');
				redirect(site_url('admin/user/all_user_list'));
            } else {

				$data['page_title'] = 'User Management';
			
				$roles = $this->common_model->get_user_roles();
				$userRoles=Array();
				foreach ($roles as $role){
					$userRoles[$role['role_id']]=$role['role_display'];
				}
				$data['user_roles']=$userRoles;
				$data['breadcrumbs']=$breadcrumbs;
				$data['action']=site_url('admin/user/add');

				$data = $this->security->xss_clean($data);

                $this->session->set_flashdata('error_msg', 'Email already exist, try another email');
                $data['main_content'] = $this->load->view('admin/user/add', $data, TRUE);
				$this->load->view('admin/index', $data);
            }
        }
    }

    public function all_user_list(){
		if(!in_array($this->session->userdata('role'), $this->config->item('adminAccess'))){
			redirect(site_url('admin/dashboard'));
		}
		$breadcrumbs=array();
		
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/user').'">New User</a></li>';
			$data['users'] = $this->common_model->get_all_user();
			
		}else{
			$data['users'] = $this->common_model->get_single_user_info($this->session->userdata('id'));
		}
		
	 	$data['page_title'] = 'All Users';
        
        $roles = $this->common_model->get_user_roles();
		$userRoles=Array();
		foreach ($roles as $role){
			$userRoles[$role['role_id']]=$role['role_display'];
		}

		$data['user_roles']=$userRoles;

        //$data['country'] = $this->common_model->select('country');
        $data['count'] = $this->common_model->get_user_total();
        $data['main_content'] = $this->load->view('admin/user/users', $data, TRUE);

		$data['breadcrumbs']=$breadcrumbs;
        $this->load->view('admin/index', $data);
    }
	
	//-- update users info
    public function update($id) {
		if($this->session->userdata('role')!='ADMIN'){
			$this->session->set_flashdata('error_msg', 'You are not authorised to access this page.');
			redirect(site_url('admin/dashboard'));
		}
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		}
        if ($_POST) {
			$data = array(				
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'email' => $this->input->post('email'),
                'mobile' => $this->input->post('mobile'),                
                'category' => $this->input->post('user_role'),                
                'status' => $this->input->post('status'),               
                'api_access' => $this->input->post('api_access'),               
                'email_notification' => $this->input->post('email_notification'),               
                'date_created' => current_datetime()
            );	
			$condition=array(
				"user_id" =>$id
			);
			$api_key=$this->db->get_where('users',$condition)->row()->api_key;
			if($this->input->post('api_access')==1){
				if($api_key ==''){
					$apiKey=md5(date('d-m-y h:i:s').uniqid());
					$data['api_key']=$apiKey;
				}
			}
			
            $data = $this->security->xss_clean($data);
			
            //$data['action']=site_url('admin/user/update/'.$id);
			$condition=array(
				"user_id" =>$id
			);
            $this->common_model->update($data,'users',$condition);

			if(!empty($id)){
				
				$condition=array(
					'user_id' => $id
				);
				$this->common_model->delete('search_access',$condition);
				$useraccessArr=$this->input->post('useraccess');
				foreach ($useraccessArr as $searchID){
					$data = array(
						'user_id' => $id,
						'search_id' => $searchID
					);
					$this->common_model->insert($data, 'search_access');					
				}
			}

            $this->session->set_flashdata('msg', 'Information Updated Successfully');
            redirect(site_url('admin/user/all_user_list'));

        }else{
			$allSearchesList = $this->common_model->get_all_searches(Array(),'search_name');
			$allSearchesArr=svi_buildArray($allSearchesList,'keyword_id','search_name');
			$selectedSearchesList = $this->search_model->get_assigned_searches($id);

			#print_r($allSearchesList);
			$roles = $this->common_model->get_user_roles();
			$userRoles=Array();
			foreach ($roles as $role){
				$userRoles[$role['role_id']]=$role['role_display'];
			}
			$userData = $this->common_model->get_single_user_info($id);
			$data = array(
				'user_id' => $userData->user_id,
				'first_name' => $userData->first_name,
				'last_name' => $userData->last_name,
				'email' => $userData->email,				
				'mobile' => $userData->mobile,				
				'category' => $userData->category,				
				'email_notification' => $userData->email_notification,	
				'api_access' => $userData->api_access,			
				'api_key' => $userData->api_key,			
				'status' => $userData->status
				
			);
			
			$selectedSearchesToStr='';
			foreach ($selectedSearchesList as $selectedSearch){
				//print $selectedSearch['search_id']; 
				if(empty($data['hidSearchOptions'])){
					$data['hidSearchOptions'] .=$selectedSearch['search_id'];
					$selectedSearchesToStr .=$selectedSearch['search_id'];
				}else{
					$selectedSearchesToStr .=','.$selectedSearch['search_id'];
				}
			}
			//print $selectedSearchesToStr; exit;
			$data['allSearchesArr'] = $allSearchesArr;
			$data['selectedSearchesToStr'] = $selectedSearchesToStr;

			$data['action']=site_url('admin/user/update/'.$id);
			$data['user_roles']=$userRoles;	
			$data['main_content'] = $this->load->view('admin/user/add', $data, TRUE);
			$data['page_title'] = 'Update User - '.$userData->first_name." ".$userData->last_name;
			$data['breadcrumbs']=$breadcrumbs;
			$this->load->view('admin/index', $data);
		}
        
    }

    
    //-- active user
    public function active($id) 
    {
		if($this->session->userdata('role')!='ADMIN'){
			$this->session->set_flashdata('error_msg', 'You are not authorised to access this page.');
			 redirect(site_url('admin/user/all_user_list'));
		}
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->common_model->update($data, $id,'user');
        $this->session->set_flashdata('msg', 'User active Successfully');
        redirect(site_url('admin/user/all_user_list'));
    }

    //-- deactive user
    public function deactive($id) 
    {
		if($this->session->userdata('role')!='ADMIN'){
			$this->session->set_flashdata('error_msg', 'You are not authorised to access this page.');
			 redirect(site_url('admin/user/all_user_list'));
		}else{
			$data = array(
				'status' => 0
			);
			$data = $this->security->xss_clean($data);
			$this->common_model->update($data, $id,'user');
			$this->session->set_flashdata('msg', 'User deactive Successfully');
			redirect(site_url('admin/user/all_user_list'));
		}
    }

    //-- delete user
    public function delete($id)
    {
		redirect(site_url('admin/user/all_user_list'));
		exit;
		if($this->session->userdata('role')!='ADMIN'){
			$this->session->set_flashdata('error_msg', 'You are not authorised to access this page.');
			 redirect(site_url('admin/user/all_user_list'));
		}else{
			$this->common_model->delete($id,'user'); 
			$this->session->set_flashdata('msg', 'User deleted Successfully');
			redirect(site_url('admin/user/all_user_list'));
		}
    }
	public function changepassword($id){
		if($this->session->userdata('role')!='ADMIN'){
			if($_SESSION['id'] != $id){
				redirect(site_url('admin/user/changepassword/'.$_SESSION['id']));exit;
			}			
		}
		
		$userData = $this->common_model->get_single_user_info($id);		
		$this->form_validation->set_rules('new_password', 'New Password','required|matches[retype_password]');	
		$this->form_validation->set_rules('retype_password', 'Retyped Password', 'required|trim');	
		
		if ($this->form_validation->run() == false) {
			$data = array(
					'first_name' => $userData->first_name,
					'last_name' => $userData->last_name
				);
			$data['page_title'] = "Reset Password";
			$data['main_content'] = $this->load->view('admin/user/changepassword', $data, TRUE);
			$data['breadcrumbs']=$breadcrumbs;
			$this->load->view('admin/index', $data);
		}else{	
			
			$data = array(				
				'password' => md5($this->input->post('new_password'))
			);
			$data = $this->security->xss_clean($data);	
			$condition=array(
				"user_id" =>$id
				);
			$this->common_model->update($data,'users',$condition);			
			//$query = $this->common_model->saveNewPass(sha1($this->input->post('new_password')));
			
					
			$this->session->set_flashdata('msg', 'Information Updated Successfully');
			redirect(site_url('admin/user/changepassword/'.$id),'refresh');		
		}		  
	}
	public function userlog(){
		
		if($this->session->userdata('role')=='ADMIN'){
			
		}else{
			 redirect(site_url('admin/dashboard'));
		}
		$condition=Array();
		if(!empty($this->input->get('user_id'))){
			$condition['ul.user_id']=$this->input->get('user_id');
		}
		if(!empty($this->input->get('ip_address'))){
			$condition['ul.ip_address']=$this->input->get('ip_address');
		}
		
		$data = array();
        $data['page_title'] = 'Users Log';
		
		$roles = $this->common_model->get_user_roles();
		$userRoles=Array();
		foreach ($roles as $role){
			$userRoles[$role['role_id']]=$role['role_display'];
		}

		$config['base_url'] = site_url().'/admin/user/userlog';
		$config['uri_segment'] = 3;
		$config['per_page'] = 50;
		$config['total_rows'] = $this->login_model->getUsersAccessDetailTotal($condition);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();


		$userAccessDetails = $this->login_model->getUsersAccessDetail($condition, $config['per_page'], $page,$sort_order);
		$userCondition=Array();
		
		$users = $this->common_model->get_all_users($userCondition);
		$data['usersArr']=svi_buildArray($users,'user_id','fullname','All');


		$currentURL = current_url();
		$params   = $_SERVER['QUERY_STRING'];
		$reLoadUrl = $currentURL . '?' . $params;
		$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		$data['user_roles']=$userRoles;
		$data['breadcrumbs']=$breadcrumbs;
		$data['userAccessDetails']=$userAccessDetails;
		$data['user_id']=$this->input->get('user_id');
		$data['ip_address']=$this->input->get('ip_address');
		$data['reLoadUrl']=$reLoadUrl;
        $data['main_content'] = $this->load->view('admin/user/access_details', $data, TRUE);
        $this->load->view('admin/index', $data);
	}
	public function blockUnblock(){
		if(!$this->session->userdata('role')=='ADMIN'){
			return 0;
		}
		
		if(!empty($this->input->get('ip_address'))){
			if($this->input->get('block')=='true'){
				
				$dataToInsert=Array();
				$dataToInsert['ip_address']=$this->input->get('ip_address');
				$ipID=$this->db->get_where('ipblocklist',$dataToInsert)->row()->ip_id;
				if(empty($ipID)){
					$dataToInsert['blocked_by']=$this->session->userdata('id');
					$dataToInsert = $this->security->xss_clean($dataToInsert);
					$this->common_model->insert($dataToInsert, 'ipblocklist');
				}				
				$this->session->set_flashdata('msg', 'IP '.$condition['ip_address'].' blocked Successfully');	
			}else{
				$condition=Array();
				$condition['ip_address']=$this->input->get('ip_address');
				//print_r($condition);
				$this->common_model->delete('ipblocklist',$condition); 
				$this->session->set_flashdata('msg', 'IP '.$condition['ip_address'].' unblocked Successfully');
			}			
		}		
	}
}