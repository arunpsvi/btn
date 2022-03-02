<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reject extends CI_Controller {

	public function __construct(){
        parent::__construct();
        check_login_user();
		$this->load->model('common_model');
		$this->load->model('reject_model');
		$this->load->library('form_validation');
		$this->load->library('pagination');	
		if($this->session->userdata('role')=='ADMIN'){
			//$breadcrumbs[]='<li><a href="'.site_url('admin/user/all_user_list').'">Users List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}
    }

	public function index(){	

		if(!in_array($this->session->userdata('role'), $this->config->item('adminAccess'))){
			redirect(site_url('admin/dashboard'));
		}

		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url','Please select');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		#$websiteListArr['']='Please select';
		$data['breadcrumbs']=$breadcrumbs;
		$data['websiteListArr']=$websiteListArr;
		$data['website_id']=$this->input->get('website_id');

		$breadcrumbs=array();
		
		$condition=Array();
		$condition['website_id']=$data['website_id'];
		$config['base_url'] = site_url().'/admin/reject/';
		$config['uri_segment'] = 3;
		$config['per_page'] = 50;
		$config['total_rows'] = $this->reject_model->get_all_rejectlist_total($condition);
		$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = true;

		$this->pagination->initialize($config);
		$page= $this->input->get('per_page');
		$sort_order= $this->input->get('sort_order');
		$data['pagination'] = $this->pagination->create_links();

		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/reject/add').'">Add new</a></li>';
			$data['companies'] = $this->reject_model->get_all_rejectlist($condition, $config['per_page'], $page,$sort_order);			
		}else{
			//$data['proxies'] = $this->common_model->get_single_proxy_info($this->session->userdata('id'));
		}
		
	 	$data['page_title'] = 'Reject list';
        
        $roles = $this->common_model->get_user_roles();
		$userRoles=Array();
		foreach ($roles as $role){
			$userRoles[$role['role_id']]=$role['role_display'];
		}

		$data['user_roles']=$userRoles;

        //$data['country'] = $this->common_model->select('country');
        $data['count'] = $this->common_model->get_user_total();
        $data['main_content'] = $this->load->view('admin/reject/list', $data, TRUE);

		$data['breadcrumbs']=$breadcrumbs;
        $this->load->view('admin/index', $data);
    }

    

    //-- add new user by admin
    public function add() {   
		
		$this->form_validation->set_rules('website_id', 'Website', 'required|trim');	
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');	
		$this->form_validation->set_rules('companyUrl', 'Company Url', 'required|trim');
		if($this->session->userdata('role')=='ADMIN'){
			$breadcrumbs[]='<li><a href="'.site_url('admin/reject').'">Reject List</a></li>';
		}else{
			 redirect(site_url('admin/dashboard'));
		}
		$company_name_formatted= $this->input->post('company_name');
		$company_name_formatted = preg_replace('/\W+/','',$company_name_formatted);
		$company_name_formatted = strtolower($company_name_formatted);
        if ($this->form_validation->run() == false) {
			$website_list = $this->common_model->get_all_websites();
			
			$websiteListNameArr=svi_buildArray($website_list,'id','name');
			#$websiteListArr['']='Please select';
			$websiteListArr=svi_buildArray($website_list,'id','url','Please select');
			$data['breadcrumbs']=$breadcrumbs;
			$data['websiteListArr']=$websiteListArr;
			$data['action']=site_url('admin/reject/add');
			$data['main_content'] = $this->load->view('admin/reject/add', $data, TRUE);
			$this->load->view('admin/index', $data);	
        }else{
			$data = array(
                'website_id' => $this->input->post('website_id'),
                'company_name' => $this->input->post('company_name'),
                'companyUrl' => $this->input->post('companyUrl'),
                'website_url' => $this->input->post('website_url'),
                'company_name_formatted' => $company_name_formatted,
                'added_by' => $this->session->userdata('id')
            );           
            $user_id = $this->common_model->insert($data, 'reject_list');					
			$this->session->set_flashdata('msg', 'Company added Successfully to the reject list.');
			redirect(site_url('admin/reject'));			
		}
    }

    
	
	//-- update users info
    public function update($id) {
		if($this->session->userdata('role')!='ADMIN'){
			redirect(site_url('admin/dashboard'));	
		}
		$this->form_validation->set_rules('website_id', 'Website', 'required|trim');	
		$this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');	
		//$this->form_validation->set_rules('companyUrl', 'Company Url', 'required|trim');
		
		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		$websiteListArr['']='Please select';
			

        if ($this->form_validation->run() == true) {
			$company_name_formatted= $this->input->post('company_name');
			$company_name_formatted = preg_replace('/\W+/','',$company_name_formatted);
			$company_name_formatted = strtolower($company_name_formatted);
            $data = array(
                'company_name' => $this->input->post('company_name'),
                'companyUrl' => trim($this->input->post('companyUrl')),
                'website_url' => trim($this->input->post('website_url')),
                'website_id' => $this->input->post('website_id'),
                'added_by' => $this->session->userdata('id'),                                     
                'company_name_formatted' => $company_name_formatted
            );
            $data = $this->security->xss_clean($data);
			$condition=array(
				"reject_id" =>$id
			);
			$this->common_model->update($data,'reject_list',$condition);
            $this->session->set_flashdata('msg', 'Information Updated Successfully');
            redirect(site_url('admin/reject'));

        }else{
			$condition=Array();
			$condition['reject_id']=$id;
			$companyData = $this->reject_model->get_single_company_info($condition);
			
			$data = array(
				'company_name' => $companyData->company_name,
				'website_url' => $companyData->website_url,
				'companyUrl' => $companyData->companyUrl,
				'website_id' => $companyData->website_id
			);
			$data['websiteListArr']=$websiteListArr;
			$data['action']=site_url('admin/reject/update/'.$id);
			$data['user_roles']=$userRoles;	
			$data['main_content'] = $this->load->view('admin/reject/add', $data, TRUE);
			$data['page_title'] = 'Update company - '.$companyData->company_name;
			$data['breadcrumbs']=$breadcrumbs;
			$this->load->view('admin/index', $data);
		}        
    }

	public function uploadCSV(){
		$this->form_validation->set_rules('website_id', 'Website', 'required|trim');
		$website_list = $this->common_model->get_all_websites();
		$websiteListArr=svi_buildArray($website_list,'id','url','Please select');
		$websiteListNameArr=svi_buildArray($website_list,'id','name');
		$data['websiteListArr']=$websiteListArr;
		if ($this->form_validation->run() == true) {			
			if(!empty($_FILES['rejectlist_file']['name'])){
				$config['upload_path'] = 'uploads/';
				$config['overwrite'] = TRUE;
				$config['allowed_types'] = 'csv';
				$config['file_name'] = $_FILES['rejectlist_file']['name'];
				
				$this->load->library('upload',$config);
				$this->upload->initialize($config);
			
				if($this->upload->do_upload('rejectlist_file')){
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
							$condition=Array();
							$condition['website_id'] = $this->input->post('website_id');
							$condition['company_name_formatted']=$emapData[0];
							$condition['company_name_formatted']=preg_replace('/\W+/','',$condition['company_name_formatted']);
							$condition['company_name_formatted']=strtolower($condition['company_name_formatted']);
							#$condition['port']=$emapData[1];
							
							if(!$this->reject_model->checkInRejectedList($condition)){
								$recordstoInsert['website_id']=$condition['website_id'];
								$recordstoInsert['company_name']=$emapData[0];
								$recordstoInsert['companyUrl']=$emapData[1];							
								$recordstoInsert['website_url']=$emapData[2];							
								$recordstoInsert['companyUrl']=preg_replace('/\/$/','',$recordstoInsert['companyUrl']);
								$recordstoInsert['company_name_formatted']=$condition['company_name_formatted'];
								$recordstoInsert['added_by']=$this->session->userdata('id');
								$this->common_model->insert($recordstoInsert,'reject_list');	
								$count++;							
							}						
						}		
						fclose($file);
						$no_of_records=$count;
						
						if($no_of_records>0){
							$this->session->set_flashdata('msg', "$no_of_records records have been inserted successfully!!" );
						}else{
							$this->session->set_flashdata('error_msg', 'No record inserted');
							
						}
						redirect(site_url('admin/reject'));					
					} else {
						$this->session->set_flashdata('error_msg', 'Please Upload only CSV File');
						$data['main_content'] = $this->load->view('admin/reject/add', $data, TRUE);
						$this->load->view('admin/index', $data);	
						if(isset($_SESSION['error_msg'])){
							unset($_SESSION['error_msg']);
						}
					}
				}else{
					$this->session->set_flashdata('error_msg', 'Please upload valid csv file');
					$data['main_content'] = $this->load->view('admin/reject/add',  $data, TRUE);				
					$this->load->view('admin/index', $data);
					if(isset($_SESSION['error_msg'])){
						unset($_SESSION['error_msg']);
					}
				}			
			}else{
				#$data['websiteListArr']=$websiteListArr;
				$this->session->set_flashdata('error_msg', 'Please upload valid csv file');
				$data['main_content'] = $this->load->view('admin/reject/add', $data, TRUE);
				$this->load->view('admin/index', $data);			
				if(isset($_SESSION['error_msg'])){
					unset($_SESSION['error_msg']);
				}
			}
		}else{
			$data['main_content'] = $this->load->view('admin/reject/add', $data, TRUE);
			$this->load->view('admin/index', $data);			
		}
	}

    //-- delete user
    public function delete($id)
    {
		$condition['reject_id']=$id;
		$this->common_model->delete('reject_list',$condition); 
		$this->session->set_flashdata('msg', 'Company deleted from reject list Successfully');
		#redirect(site_url('admin/proxy'));
    }

	public function block($websiteId,$resId){
		$condition=Array();
		$condition['result_id']=$resId;
		$jobDetails=$this->common_model->get_job_details($condition);
		$rejectCondition=Array();
		$companyWebsite=$jobDetails->companyWebsite;
		$companyWebsite=preg_replace('/http:\/\//is','',$companyWebsite);
		$companyWebsite=preg_replace('/https:\/\//is','',$companyWebsite);
		if(preg_match('/(.*?)\/$/is',$companyWebsite,$matcherCompany)){
			$companyWebsite=$matcherCompany[1];
		}
		$rejectCondition['companyWebsite']=$companyWebsite;
		$rejectCondition['website_id']=$websiteId;
		if(empty($this->reject_model->checkInRejectedList($rejectCondition))){
			$data = array(
                'website_id' => $websiteId,
                'company_name' => $jobDetails->organization,
                'companyUrl' => $jobDetails->companyUrl,
                'website_url' => $companyWebsite,
                /*'company_name_formatted' => $company_name_formatted,*/
                'added_by' => $this->session->userdata('id')
            );           
            $rejectId = $this->common_model->insert($data, 'reject_list');	
		}		
		//print_r($jobDetails);
    }

	public function unblock($resId){
		$condition=Array();
		$condition['result_id']=$resId;
		$jobDetails=$this->common_model->get_job_details($condition);
		print_r($jobDetails);
    }
	
}