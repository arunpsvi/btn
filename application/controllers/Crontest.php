<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Crontest extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('scheduler_model');
		$this->scraped_date=date('Y-m-d h:i:s');
    }
    
    public function index($id='') {
        print date("h:i:sa");
        return;
        for($i=0;$i<100;$i++){
            $myfile = fopen("testing/$id.txt", "a") or die("Unable to open file!");
            $txt = "The time is " . date("h:i:sa");
            fwrite($myfile, "\n". $txt);
            fclose($myfile);
            sleep(5);
        }
    }
    
    public function startScraping() {
        
        $schedules=$this->scheduler_model->getSchedules();
        
        if(!empty($schedules->keyword_id) && !empty($schedules->schedule_id)){
            
            $cmd="/usr/local/bin/php /home/botnumco/public_html/index.php $schedules->name index $schedules->keyword_id $schedules->schedule_id";
            exec($cmd);
        }
    }
			
}
