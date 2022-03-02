<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proxy extends CI_Controller {

	public function __construct(){
        parent::__construct();
        check_login_user();
		$this->load->model('common_model');
		$this->load->model('login_model');
		$this->load->library('form_validation');
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}
    }

	public function index(){	

		if(!in_array($this->session->userdata('role'), $this->config->item('adminAccess'))){
			redirect(site_url('admin/dashboard'));
		}
		$breadcrumbs=array();
		
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/proxy/add').'">Add new</a></li>';
			$data['proxies'] = $this->common_model->get_all_proxy();			
		}else{
			$data['proxies'] = $this->common_model->get_single_proxy_info($this->session->userdata('id'));
		}
		
	 	$data['page_title'] = 'Proxy list';
        
        $roles = $this->common_model->get_user_roles();
		$userRoles=Array();
		foreach ($roles as $role){
			$userRoles[$role['role_id']]=$role['role_display'];
		}

		$data['user_roles']=$userRoles;

        //$data['country'] = $this->common_model->select('country');
        $data['count'] = $this->common_model->get_user_total();
        $data['main_content'] = $this->load->view('admin/proxy/list', $data, TRUE);

		$data['breadcrumbs']=$breadcrumbs;
        $this->load->view('admin/index', $data);
    }

    

    //-- add new user by admin
    public function add() {   
		
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/proxy').'">Proxy List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}

        if ($this->input->post()) {

            $data = array(
                'uname' => $this->input->post('uname'),
                'password' => $this->input->post('password'),
                'ip' => $this->input->post('ip'),
                'port' => $this->input->post('port'),
                'status' => $this->input->post('status')
            );           
            $user_id = $this->common_model->insert($data, 'proxy');					
			$this->session->set_flashdata('msg', 'Proxy added Successfully');
			redirect(site_url('admin/proxy'));
        }else{
			$data['breadcrumbs']=$breadcrumbs;
			$data['action']=site_url('admin/proxy/add');
			$data['main_content'] = $this->load->view('admin/proxy/add', $data, TRUE);
			$this->load->view('admin/index', $data);
		}
    }

    
	
	//-- update users info
    public function update($id) {
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/proxy').'">Proxy List</a></li>';
		}

        if ($_POST) {
            $data = array(
                'uname' => $this->input->post('uname'),
                'password' => $this->input->post('password'),
                'ip' => $this->input->post('ip'),
                'port' => $this->input->post('port'),                                     
                'status' => $this->input->post('status')
            );
            $data = $this->security->xss_clean($data);
			
            //$data['action']=site_url('admin/user/update/'.$id);
			$condition=array(
				"id" =>$id
			);
            $this->common_model->update($data,'proxy',$condition);
            $this->session->set_flashdata('msg', 'Information Updated Successfully');
            redirect(site_url('admin/proxy'));

        }else{
			$proxyData = $this->common_model->get_single_proxy_info($id);
			$data = array(
				'uname' => $proxyData->uname,
				'password' => $proxyData->password,
				'ip' => $proxyData->ip,
				'port' => $proxyData->port,				
				'status' => $proxyData->status
			);
			$data['action']=site_url('admin/proxy/update/'.$id);
			$data['user_roles']=$userRoles;	
			$data['main_content'] = $this->load->view('admin/proxy/add', $data, TRUE);
			$data['page_title'] = 'Update Proxy - '.$proxyData->ip;
			$data['breadcrumbs']=$breadcrumbs;
			$this->load->view('admin/index', $data);
		}        
    }

	public function uploadCSV(){
		#print "arun";exit;
		if(!empty($_FILES['proxylist_file']['name'])){
			$config['upload_path'] = 'uploads/';
			$config['overwrite'] = TRUE;
			$config['allowed_types'] = 'csv';
			$config['file_name'] = $_FILES['proxylist_file']['name'];
			
			$this->load->library('upload',$config);
			$this->upload->initialize($config);
		
			if($this->upload->do_upload('proxylist_file')){
				$uploadData = $this->upload->data();						
				$filename = $config['upload_path'].$uploadData['file_name'];
				
				/* MYcode */
				$extension=substr($filename,strrpos($filename,"."),(strlen($filename)-strrpos($filename,".")));
				//echo $extension;exit;
				if($extension==".csv"){
					$file = fopen($filename, "r");
					
					
					$count=0;
					$bankRecords=Array();
					while (($emapData = fgetcsv($file,",")) !== FALSE)
					{
						$recordstoInsert=Array();
						$tableName='bank_statement';				
						$condition=Array();
						$condition['ip']=$emapData[0];
						$condition['port']=$emapData[1];
						
						if(!$this->common_model->check_duplicate_proxy($condition)){
							$recordstoInsert['ip']=$emapData[0];
							$recordstoInsert['port']=$emapData[1];
							$recordstoInsert['uname']=$emapData[2];
							$recordstoInsert['password']=$emapData[3];
							$this->common_model->insert($recordstoInsert,'proxy');	
							$count++;							
						}						
					}	
					//exit;			
					fclose($file);
					$no_of_records=$count;
					
					if($no_of_records>0){
						$this->session->set_flashdata('msg', "$no_of_records records have been inserted successfully!!" );
					}else{
						$this->session->set_flashdata('error_msg', 'No record inserted');
						
					}
					redirect(site_url('admin/proxy'));
					/*$data['flag']=1;										
					$data['main_content'] = $this->load->view('admin/proxy/list', $data, TRUE);
					$this->load->view('admin/index', $data);
					if(isset($_SESSION['error_msg'])){
						unset($_SESSION['error_msg']);
					}
					if(isset($_SESSION['msg'])){
						unset($_SESSION['msg']);
					}*/
					
				} else {
					$this->session->set_flashdata('error_msg', 'Please Upload only CSV File');
					$data['main_content'] = $this->load->view('admin/proxy/add', '', TRUE);
					$this->load->view('admin/index', $data);	
					if(isset($_SESSION['error_msg'])){
						unset($_SESSION['error_msg']);
					}
				}
			}				
		}else{
			 $data['main_content'] = $this->load->view('admin/proxy/add', '', TRUE);
			 $this->load->view('admin/index', $data);			
		}
	}

    //-- delete Proxy
    public function delete($id)
    {
		$condition['id']=$id;
		$this->common_model->delete('proxy',$condition); 
		$this->session->set_flashdata('msg', 'Proxy deleted Successfully');
		#redirect(site_url('admin/proxy'));
    }

	/*
		Update proxy status
	*/
    public function blockUnblock($id,$status)
    {	
		if(!empty($id) && ($status=='Y' || $status=='N')){
			$condition['id']=$id;
			$dataToUpdate['status']=$status;
			$res=$this->common_model->update($dataToUpdate,'proxy',$condition);
			print "1";
		}else{
			print "0";
		}
    }
	
}