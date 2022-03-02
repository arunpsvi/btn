<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Progress extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
    }

    public function index() {
		$websiteId=$this->input->get('website_id');
		$websiteId=1;
		print $this->db->get_where('websites',array('id'=>$websiteId))->row()->progress;	
		#print $this->db->get_where('websites',array('id'=>'1'))->row()->progress;	
	}

    
}

