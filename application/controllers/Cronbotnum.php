<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronbotnum extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('scheduler_model');
		$this->scraped_date=date('Y-m-d h:i:s');
    }
    
    public function index($id='') {
       exit;
    }
    
    public function startScraping() {
        
        $schedules=$this->scheduler_model->getSchedules();
        //print_r($schedules);exit;
        //exit;
        if(!empty($schedules->keyword_id) && !empty($schedules->schedule_id)){
            
            $cmd="/usr/local/bin/php /home/kocpvlmy/public_html/index.php $schedules->name index $schedules->keyword_id $schedules->schedule_id";
            //print $cmd;
            exec($cmd);
        }
    }
			
}
