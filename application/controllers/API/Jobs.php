<?php
require APPPATH.'libraries\REST_Controller.php';
Class Jobs extends REST_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('API/jobs_model');        
        $headers = apache_request_headers();
        $apikey=$headers['apikey'];
        $this->user_authenticity=check_user_authenticity($apikey); 
    }  
    public function index_get(){
        if($this->user_authenticity==0){
            return false;
        }else{            
            $formData=$_REQUEST;
            $bot_id=$formData['bid'];
            $action='Fetch_All_Jobs';
            //Api  : Branch_MPR_List
            if($action=='Fetch_All_Jobs'){
                $searchBotCondition=array();
                $searchBotCondition['res.search_id']=$bot_id;
                $all_jobs_lists=$this->jobs_model->get_all_jobs($searchBotCondition);
                //echo"<pre>";
               // print_r($all_jobs_lists);exit;
                $results=array();
                $i=0;
                if(count($all_jobs_lists)>0){
                    foreach($all_jobs_lists as $all_jobs_list ){
                       // $results[$i]['website_id']=$all_jobs_list['website_id'];
                        $results[$i]['job_url']=$all_jobs_list['job_url'];
                        $results[$i]['title']=$all_jobs_list['title'];
                        $results[$i]['compensation']=$all_jobs_list['compensation'];
                        $results[$i]['description']=$all_jobs_list['description'];
                        $results[$i]['keywords']=$all_jobs_list['keywords'];
                        $results[$i]['posted_date']=$all_jobs_list['posted_date'];
                        $results[$i]['location']=$all_jobs_list['location'];
                        $results[$i]['sub_category_name']=$all_jobs_list['sub_category_name'];
                        $i++;
                    }               
                    $this->response(array(
                        "status"=>REST_Controller::HTTP_OK,
                        "message"=>"Success : Job Results Found",
                        "results"=>$results                
                    ),REST_Controller::HTTP_OK);
                }else{
                    $this->response(array(
                        "status"=>201,
                        "message"=>"Failed to get Job Results ", 
                    ),REST_Controller::HTTP_CREATED);
                }
            } 
        }     

    }
    
}
?>