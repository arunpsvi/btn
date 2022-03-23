<?php
require APPPATH.'libraries/REST_Controller.php';
Class Jobs extends REST_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('API/jobs_model');        
        #print_r($_SERVER['HTTP_APIKEY']);
        #exit;
        #$headers = apache_request_headers();
        $apikey=$_SERVER['HTTP_APIKEY'];
        #$apikey="db27100f11a6a013d49e5057440cb9c9";
        $this->user_authenticity=check_user_authenticity($apikey);
    }  
    public function index_get(){
        if($this->user_authenticity==0){
            return false;
        }else{            
            $formData=$_REQUEST;
            $bot_id=$formData['bid'];
            $pageNo=$formData['pageNo'];
            if(empty($pageNo)){
                $pageNo=1;
            }
            
            $recordsPerPage=10;
            $start=($pageNo-1)*$recordsPerPage;
            
            $totalPages=0;

            $action='Fetch_All_Jobs';
            //Api  : Branch_MPR_List
            if($action=='Fetch_All_Jobs'){
                $searchBotCondition=array();
                $searchBotCondition['res.search_id']=$bot_id;
                $totalRecords=$this->jobs_model->get_total_jobs($searchBotCondition);
                if($totalRecords>0){
                    $totalPages=ceil($totalRecords/$recordsPerPage);
                }

                $all_jobs_lists=$this->jobs_model->get_all_jobs($searchBotCondition,$recordsPerPage,$start);
                $results=array();
                $i=0;
                if(count($all_jobs_lists)>0){
                    foreach($all_jobs_lists as $all_jobs_list ){
                        // $results[$i]['website_id']=$all_jobs_list['website_id'];
                        $results[$i]['SNo']=$all_jobs_list['result_id'];
                        $results[$i]['SourceWebsite']=$all_jobs_list['websiteName'];
                        $results[$i]['Qualify']=$all_jobs_list['qualify'];
                        $results[$i]['DecisionMaker']=$all_jobs_list['decision_maker'];
                        $results[$i]['Name']=$all_jobs_list['name'];
                        $results[$i]['Phone']=$all_jobs_list['phone'];
                        $results[$i]['Email']=$all_jobs_list['email'];
                        #$results[$i]['Source']=$all_jobs_list['url'];
                        $results[$i]['ScrapedDate']=$all_jobs_list['scraped_date'];
                        $results[$i]['PostedDate']=$all_jobs_list['posted_date'];
                        $results[$i]['Location']=$all_jobs_list['location'];
                        $results[$i]['Category']=$all_jobs_list['category_name']."";
                        $results[$i]['SubCategory']=$all_jobs_list['sub_category_name']."";     
                        $results[$i]['Keyword']= $all_jobs_list['keywords'];
                        $results[$i]['PostTitle']= $all_jobs_list['title'];
                        $results[$i]['PostUrl']= $all_jobs_list['job_url'];

                        #$results[$i]['Exact match']= $all_jobs_list['exact_match'];

                        $results[$i]['Compensation']=$all_jobs_list['compensation']; 
                        $results[$i]['Description']= $all_jobs_list['description'];
                        $results[$i]['Applications']= $all_jobs_list['applications'];
                        $results[$i]['Organization']= $all_jobs_list['organization'];
                        $results[$i]['ProfileUrl']= $all_jobs_list['companyUrl'];
                        $results[$i]['EmployementType']= $all_jobs_list['employementType'];
                        $results[$i]['Senioritylevel']= $all_jobs_list['seniorityLevel'];
                        $results[$i]['Industries']= $all_jobs_list['industries'];
                        $results[$i]['JobFunction']= $all_jobs_list['jobFunction'];
                        $results[$i]['Companyurl']= $all_jobs_list['companyWebsite'];
                        $results[$i]['Employees']= $all_jobs_list['employees'];
                        $results[$i]['EmployeesOnLinkedin']= $all_jobs_list['employeesOnLinkedin'];
                        $results[$i]['Emails']= $all_jobs_list['emails'];

                        /*$results[$i]['Key Contact 1']= $all_jobs_list['result_id'];
                        $results[$i]['Profile 1']= $all_jobs_list['result_id'];
                        $results[$i]['Key Contact 2']= $all_jobs_list['result_id'];
                        $results[$i]['Profile 2']= $all_jobs_list['result_id'];
                        $results[$i]['Key Contact 3']= $all_jobs_list['result_id'];
                        $results[$i]['Profile 3']= $all_jobs_list['result_id'];
                        $results[$i]['Key Contact 4']= $all_jobs_list['result_id'];
                        $results[$i]['Profile 4']=$all_jobs_list['result_id'];*/
                        
                        $results[$i]['Job Poster']= $all_jobs_list['job_poster_name'];
                        $results[$i]['Job Poster Profile']= $all_jobs_list['job_poster_url'];
                        #$results[$i]['Key Contact 1']= $all_jobs_list['result_id'];
                        $results[$i]['TwitterUrl']= $all_jobs_list['twitterURL'];
                        $results[$i]['FacebookUrl']=$all_jobs_list['facebookURL'];
                        $i++;
                    }               
                    $this->response(array(
                        "status"=>REST_Controller::HTTP_OK,
                        "message"=>"Success : Job Results Found",
                        "totalPages"=>$totalPages,
                        "pageNo"=>$pageNo,
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