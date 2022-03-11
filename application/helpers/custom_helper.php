<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 	
	//-- check logged user
	if (!function_exists('check_login_user')) {
	    function check_login_user() {
	        $ci = get_instance();
			$condition=Array();
			$condition['ip_address']=$ci->input->ip_address();
			$ipID=$ci->db->get_where('ipblocklist',$condition)->row()->ip_id;
			if(!empty($ipID)){
				$ci->session->sess_destroy();
				redirect(site_url('guest'));
			}else if ($ci->session->userdata('is_login') != TRUE) {
	            $ci->session->sess_destroy();
	            redirect(site_url('auth'));
	        }
	    }
	}

	if(!function_exists('check_user_authenticity')){
		function check_user_authenticity($apikey){ 
			$flag=0; 
			$ci = get_instance();	        
	        $ci->load->model('common_model');
	        $option = $ci->common_model->check_user_authenticity($apikey); 		
			if(!empty($apikey)){
				
				if(($option->api_access ==1) && ($option->status ==1) ){
					$bot_id=$ci->input->get('bid');
					if(!empty($bot_id)){
						$bot_condition=array(
							'search_id'=>$bot_id,
							'user_id'=>$option->user_id
						);
						$bot_option = $ci->common_model->check_user_bot_authenticity($bot_condition); 
						
						if(!empty($bot_option->user_id)){
							$flag=1;
						}else{
							$ci->response(array(
								"status"=>201,
								"message"=>"unauthorized access", 
							),REST_Controller::HTTP_CREATED);
						}
					}else{
						$ci->response(array(
							"status"=>201,
							"message"=>"key in bot id", 
						),REST_Controller::HTTP_CREATED);
					}		
					
				}else{
					$ci->response(array(
						"status"=>201,
						"message"=>"Unauthorized Access", 
					),REST_Controller::HTTP_CREATED);
				}
				
			}else{
				$ci->response(array(
					"status"=>201,
					"message"=>"Please send valid API key", 
				),REST_Controller::HTTP_CREATED);
			}
			return $flag;		
	    }
		
	}
	if(!function_exists('check_power')){
	    function check_power($type){        
	        $ci = get_instance();
	        
	        $ci->load->model('common_model');
	        $option = $ci->common_model->check_power($type);        
	        
	        return $option;
	    }
    } 

	//-- current date time function
	if(!function_exists('current_datetime')){
	    function current_datetime(){        
	        $dt = new DateTime('now', new DateTimezone('Asia/Dhaka'));
	        $date_time = $dt->format('Y-m-d H:i:s');      
	        return $date_time;
	    }
	}

	//-- show current date & time with custom format
	if(!function_exists('my_date_show_time')){
	    function my_date_show_time($date){       
	        if($date != ''){
	            $date2 = date_create($date);
	            $date_new = date_format($date2,"d M Y h:i A");
	            return $date_new;
	        }else{
	            return '';
	        }
	    }
	}

	//-- show current date with custom format
	if(!function_exists('my_date_show')){
	    function my_date_show($date){       
	        
	        if($date != ''){
	            $date2 = date_create($date);
	            $date_new = date_format($date2,"d M Y");
	            return $date_new;
	        }else{
	            return '';
	        }
	    }
	}
	

	if(!function_exists('my_clear_fields')){
	    function my_clear_fields($data){      
			$newData=Array();
	        foreach ($data as $key=>$value){
				//print "$key=>$value <br>";
				$value=preg_replace('/\s+$/im', '', $value); 
				$value=preg_replace('/^\s+/im', '', $value); 
				$value=preg_replace('/\s+/im', ' ', $value);
				$newData[$key]=$value;
			}
	        return $newData;
	    }
	}
	
	

	if(!function_exists('svi_trim_data')){
	    function svi_trim_data($data){    

			$data=preg_replace('/^\s+/is','',$data);
			$data=preg_replace('/\s+$/is','',$data);					
			$data=preg_replace('/\s+/isg',' ',$data);					
	        return $data;
	    }
	}

	
	if(!function_exists('svi_buildArray')){
	    function svi_buildArray($mdArray,$key,$value,$default=''){   
			$array=Array();
			if($default != ''){
				$array['']=$default;
			}			
			foreach($mdArray as $sdArray){
				$array[$sdArray[$key]]=$sdArray[$value];
			}
	        return $array;
	    }
	}

	if(!function_exists('svi_send_notification')){
	    function svi_send_notification($adminEmail,$emailArray,$emailData){   
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
			$ci = get_instance();
			$ci->load->library('email',$config);

			$to = $adminEmail;
			$subject = $emailData['subject'];				
			$message = $emailData['message'];				
			
			$admin_email = 'info@botnum.com';
			$ci->email->set_header('Content-Type', 'text/html');
			$ci->email->from($admin_email, 'Botnum');
			$ci->email->reply_to($admin_email, 'Botnum');
			$ci->email->to($to);
			$cc='';
			foreach($emailArray as $email=>$value){
				$cc .=$email.",";
			}
			$cc = rtrim($cc,',');
			if(!empty($cc)){
				$ci->email->cc($cc);
			}
			$ci->email->subject($subject);
			$ci->email->message($message);	
			
			if($ci->config->item('APPVERSION')!='DEV'){
				$ci->email->send();
			}else{
				print "<pre>";
				print_r($ci->email);	
			}
	    }
	}
	if(!function_exists('svi_datediff')){
	    function svi_datediff($date1,$date2){   
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			$datediff = $date2 - $date1;
			return round($datediff / (60 * 60 * 24));
	    }
	}