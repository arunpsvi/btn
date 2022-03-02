<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// *************************************************************************
// *                                                                       *
// * Softvision India                              *
// * Copyright (c) Softvision India. All Rights Reserved                   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@softvisionindia.com										*
// * Website: http://softvisionindia.com								   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// *                                                                       *
// *************************************************************************

//LOCATION : application - controller - Auth.php

class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('common_model');
    }


    public function index(){
		$condition=Array();
		$condition['ip_address']=$this->input->ip_address();
		$ipID=$this->db->get_where('ipblocklist',$condition)->row()->ip_id;
		if(!empty($ipID)){
			$this->session->sess_destroy();
			redirect(site_url('guest'));
		}else{
			if(!empty($this->session->userdata('id'))){
				redirect(site_url() . '/admin/dashboard', 'refresh');
				return;
			}
			$data = array();
			$data['page'] = 'Auth';
			$this->load->view('admin/login', $data);
		}
    }



 /****************Function login**********************************
     * @type            : Function
     * @function name   : log
     * @description     : Authenticatte when uset try lo login. 
     *                    if autheticated redirected to logged in user dashboard.
     *                    Also set some session date for logged in user.   
     * @param           : null 
     * @return          : null 
     * ********************************************************** */
    public function log(){

        if($_POST){ 
            $query = $this->login_model->validate_user();
            
            //-- if valid
            if($query){
				if($query[0]->status){
					$data = array();
					foreach($query as $row){
						#$roleData=$this->common_model->get_user_role($row->role);
						$data = array(
							'id' => $row->user_id,
							'name' => $row->first_name. " ".$row->last_name,
							'email' =>$row->email,                        
							'role' =>$this->db->get_where('user_role',array('role_id'=>$row->category))->row()->role_name,                       
							'user_cat_id' =>$row->category,                        
							'is_login' => TRUE
						);
						$this->session->set_userdata($data);
                        $dataToInsert=Array();
                        $dataToInsert['user_id']=$row->user_id;
                        $dataToInsert['ip_address'] = $this->input->ip_address();
				        $dataToInsert = $this->security->xss_clean($dataToInsert);
				        $this->common_model->insert($dataToInsert, 'user_log');
						$url = site_url('admin/dashboard');
					}
					redirect(site_url() . '/admin/dashboard', 'refresh');
				}else{
					$this->session->set_flashdata('error_msg', 'Your login is inactive, Please contact admin');
					redirect(site_url() . '/auth', 'refresh');
				}	
            }else{
				$this->session->set_flashdata('error_msg', 'Invalid Username or Password.Please Retry.. ');
				redirect(site_url() . '/auth', 'refresh');
            }
            
        }else{
            $this->load->view('auth',$data);
        }
    }

 /*     * ***************Function logout**********************************
     * @type            : Function
     * @function name   : logout
     * @description     : Log Out the logged in user and redirected to Login page  
     * @param           : null 
     * @return          : null 
     * ********************************************************** */
    
    function logout(){
        $this->session->sess_destroy();
        $data = array();
        $data['page'] = 'logout';
        $this->load->view('admin/login', $data);
    }

}