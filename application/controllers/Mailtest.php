<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mailtest extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
    }
    public function index(){
       // echo"Amit";exit;
    }
     public function sendEmail() {
		//return;
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
		
		$to = 'amitpandey1780@gmail.com';
		$subject = 'test';
		$message = 'test message';
		
		
		$admin_email = 'info@botnum.com';
		
		$this->email->from($admin_email, 'Test Email Cron');
	//	$this->email->reply_to($admin_email, 'Job Post Scraper1');
		
        $this->email->to($to);
		$this->email->bcc('amit.softvisionindia@gmail.com');
		$this->email->subject($subject);
		$this->email->message(nl2br($message));		
	 
		
		$this->email->send();
	}
   
			
}
