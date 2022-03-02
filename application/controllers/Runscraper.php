<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Runscraper extends CI_Controller {

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
        exit;
        $schedules=$this->scheduler_model->getSchedules();
        
        if(!empty($schedules->keyword_id) && !empty($schedules->schedule_id)){
            
            $cmd="/usr/local/bin/php /home/botnumco/public_html/index.php $schedules->name index $schedules->keyword_id $schedules->schedule_id";
            //print $cmd;
           // exec($cmd);
        }
        
        //print "<pre>";
        //print_r($schedules);
    }
    
    

    public function sendEmail() {
		return;
	    $config = Array(
		  //'protocol' => 'smtp', 
		  'smtp_host' => 'localhost', 
		  'smtp_port' => '25', 
		  '_smtp_auth' => 'FALSE', 
		  'smtp_crypto' => 'false/none', 
		  'mailtype' => 'html', 
		  'charset' => 'utf-8',
		  'wordwrap' => TRUE
		);
		
		$this->load->library('email',$config);
		
		$to = 'arunpandey1985@gmail.com';
		$subject = 'test';
		$message = 'test message';
		
		
		$admin_email = 'info@botnum.com';
		
		$this->email->from($admin_email, 'Job Post Scraper');
		$this->email->reply_to($admin_email, 'Job Post Scraper');
		;
        $this->email->to($to);
		
		$this->email->subject($subject);
		$this->email->message(nl2br($message));		
	 
		
		$this->email->send();
	}
			
}
