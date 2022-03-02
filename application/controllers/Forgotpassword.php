<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// *************************************************************************
// *                                                                       *
// * Softvision India                              *
// * Copyright (c) Softvision India. All Rights Reserved                   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@softvisionindia.com										*
// * Website: https://softvisionindia.com								   *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// *                                                                       *
// *************************************************************************

//LOCATION : application - controller - Auth.php

class Forgotpassword extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('common_model');
    }


    public function index(){
		if(!empty($this->session->userdata('id'))){
			redirect(site_url() . '/admin/dashboard', 'refresh');
			return;
		}
        $data = array();
        $data['page'] = 'Forgot Password';
        $this->load->view('admin/password/sendotp', $data);
    }

	public function sendKey(){
		$emaild=$this->input->post('emaild');  
		if(!empty($emaild)){
			$result=$this->login_model->getUserByEmail($emaild);
			if(!empty($result->user_id)){
				$randomNumber=10000+rand(100,9999);
				$dataToUpdate=Array();
				$dataToUpdate['secretkey']=$randomNumber;
				$updateCondition=Array();
				$updateCondition['user_id']=$result->user_id;
				$data['user_id']=$result->user_id;
				$this->sendEmail($result,$randomNumber);
				$this->common_model->update($dataToUpdate, 'users',$updateCondition);
				$this->load->view('admin/password/forgotpassword', $data);
			}else{
				$this->load->view('admin/password/forgotpassword', $data);
			}
		}else{
			$this->session->set_flashdata('error_msg', "Email Id can't be blank");
			redirect(site_url() . '/forgotpassword', 'refresh');					
		}
	}
	public function updatePassword(){
		
		$user_id=$this->input->post('user_id');  
		if(!empty($user_id)){
			$result=$this->common_model->get_single_user_info($user_id);			
			if($this->input->post('secretkey') == $result->secretkey){
				if($this->input->post('password') == $this->input->post('conformpassword')){
					$dataToUpdate=Array();
					$dataToUpdate['password']=md5($this->input->post('password'));
					$dataToUpdate['secretkey']='';
					$updateCondition=Array();
					$updateCondition['user_id']=$result->user_id;
					$this->common_model->update($dataToUpdate, 'users',$updateCondition);
					$this->session->set_flashdata('msg', 'Password changed successfully!!');
					redirect(site_url() . '/auth', 'refresh');
				}else{
					$this->session->set_flashdata('error_msg', 'Conform password did not match.');
					$data['user_id']=$result->user_id;
					$this->load->view('admin/password/forgotpassword', $data);
				}
			}else{
				$this->session->set_flashdata('error_msg', 'Secret key did not match.');
				$data['user_id']=$result->user_id;
				$this->load->view('admin/password/forgotpassword', $data);
			}
		}
	}

    public function sendEmail($userData,$secretKey) {
		
	    $config = Array(
		  //'protocol' => 'smtp', 
		  'smtp_host' => 'localhost', 
		  'smtp_port' => '25', 
		  '_smtp_auth' => 'FALSE', 
		  'smtp_crypto' => 'false/none', 
		  'mailtype'  => 'html',  
		  'charset' => 'utf-8',
		  'wordwrap' => TRUE
		);
		

		$this->load->library('email',$config);
		
		$to = $userData->email;
		$subject = 'Secret key for forgot password';
		$message = "Dear ".$userData->first_name." ".$userData->last_name.",";
		$message .="<br><br>";
		$message .="Here is the secret key for your forgot password request";
		$message .="<br><br><b>Secret Key:$secretKey</b>";
		$message .="<br><br>Kindly use the same to change the password.";
		$message .="<br><br>Thanks,<br>Admin Botnum";		
		
		$admin_email = 'info@botnum.com';
		$this->email->set_header('Content-Type', 'text/html');
		$this->email->from($admin_email, 'Botnum');
		$this->email->reply_to($admin_email, 'Botnum');
        $this->email->to($to);
		
		$this->email->subject($subject);
		$this->email->message($message);	
		$this->email->send();
	}

}