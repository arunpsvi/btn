<?php
class Common_model extends CI_Model {

    //-- insert function
	public function insert($data,$table){
		$data=my_clear_fields($data);
        $this->db->insert($table,$data);  
        return $this->db->insert_id();
    }

    

    //-- update function
    function update($action, $table,$condition){
		$data=my_clear_fields($action);
		$this->db->where($condition);
        $this->db->update($table,$data);
		//print $this->db->last_query();
        return;
    } 

    //-- delete function
    function delete($table,$condition){
        $this->db->delete($table,$condition);
        return;
    }

	function deleteByIn($table,$field_name,$delete_ids){
        $this->db->where_in($field_name, $delete_ids);
		$this->db->delete($table);
        return;
    }
	//-- select function
    function select($table){
        $this->db->select();
        $this->db->from($table);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

	function get_all_websites(){
		
		$this->db->select();
        $this->db->from('websites');
		$this->db->where('status','Y');
        $this->db->order_by('name','ASC');
        $query = $this->db->get();
        return $query->result_array(); 
	}
	function get_all_keywords($condition){
		
		$this->db->select();
        $this->db->from('keyword_list');
		$this->db->where($condition);
        $this->db->order_by('keyword','ASC');
        $query = $this->db->get();

        return $query->result_array(); 
	}

	function get_all_suburls($condition){		
		$this->db->select();
        $this->db->from('suburls');
		$this->db->where($condition);
        $this->db->order_by('name','ASC');
        $query = $this->db->get();
        return $query->result_array(); 
	}

	function get_all_subcategories($condition=Array(),$subcategoryId=Array()){		
		$this->db->select();
        $this->db->from('subcategories');
		$this->db->where($condition);
		if(!empty($subcategoryId[0])){
			$this->db->where_in('sub_cat_id', $subcategoryId);
		}
        $this->db->order_by('sub_cat_id','ASC');
        $this->db->order_by('sub_category_name','ASC');
        $query = $this->db->get();
		//print $this->db->last_query();
        return $query->result_array(); 
	}

	function get_all_user(){
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

	function get_all_proxy(){
        $this->db->select('p.*');
        $this->db->from('proxy p');
        //$this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }

	function get_user_roles(){
        $this->db->select('ur.*');
        $this->db->from('user_role ur');
		 $this->db->order_by('ur.role_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array(); 
        return $query;
    }

	function get_user_total(){
        $this->db->select('*');
        $this->db->select('count(*) as total');

        $this->db->from('users');
        $this->db->group_by("user_id");
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }

	function get_job_listings($condition=Array(), $limit=0, $start=0,$sort_order=''){	
        
		$this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location,web.url');
        $this->db->from('results res');
		$this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
		$this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
		$this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
		$this->db->join('websites web','web.id  = res.website_id','LEFT');
		$this->db->where($condition);
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" res.search_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }        

		if(!empty($sort_order)){
			$this->db->order_by('qualify',$sort_order);
		}
        $this->db->order_by('scraped_date','DESC');
        $this->db->order_by('result_id','DESC');
		if($limit>0){
			$this->db->limit( $limit, $start );
		}
        $query = $this->db->get();
		#print $this->db->last_query();exit;
        return $query->result_array(); 
	}
	function get_job_listings_total($condition=Array()){		
		$this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location');
        $this->db->from('results res');
		$this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
		$this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
		$this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
		$this->db->where($condition);
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" res.search_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }
		
        $query = $this->db->get();
        return $query->num_rows(); 
	}

	function get_all_searches($condition=Array(),$sort=''){
        
		$this->db->select('keyw.*');
        $this->db->from('keywords keyw');
		$this->db->where($condition);		
        if(empty($sort)){
            $this->db->order_by('keyw.keyword_id','DESC');
        }
        #Checked not empty because condition need to be skipped for cron jobs
        if(!empty($this->session->userdata('role')) && $this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" keyw.keyword_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=keyw.keyword_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }
        if(!empty($sort) && $sort=='search_name'){
            $this->db->order_by('keyw.search_name','ASC');
        }
       
        $query = $this->db->get();
		//print $this->db->last_query();
        return $query->result_array(); 
	}

	function getSearchByid($keyword_id){

		$this->db->select('kyds.*,web.name as website_name');
        $this->db->from('keywords kyds');
		$this->db->join('websites web','web.id = kyds.website_id','LEFT');
		$this->db->where('keyword_id',$keyword_id);		
        $query = $this->db->get();
        return $query->row(); 
	}

	function get_subcategories($sub_cat){

		$this->db->select();
        $this->db->from('subcategories');
		
		if(!empty($sub_cat)){
			$this->db->where("sub_cat_id in ($sub_cat)");
		}
        $this->db->order_by('sub_cat_id','ASC');
        $this->db->order_by('sub_category_name','ASC');
        $query = $this->db->get();
		//print $this->db->last_query();
        return $query->result_array(); 
	}

	function countResults($condition){

		$this->db->select('count(*) as total');
        $this->db->from('results');
		$this->db->where($condition);
		$query = $this->db->get();
	//	print $this->db->last_query();
        return $query->row(); 
	}
	function get_single_user_info($id){
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.user_id',$id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }
	function get_single_proxy_info($id){
        $this->db->select('p.*');
        $this->db->from('proxy p');
        $this->db->where('p.id',$id);
        $query = $this->db->get();
        $query = $query->row();  
        return $query;
    }
	 public function check_email($email){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {                 
            return $query->result();
        }else{
            return false;
        }
    }
	public function get_all_schedules($condition=Array()){

		$this->db->select('*');
        $this->db->from('schedule_records');
		
		$this->db->where($condition);
        $this->db->order_by('id','ASC');
        $query = $this->db->get();
        return $query->result_array(); 
	}
	public function get_upcoming_schedules($condition=Array()){
		$date=date("Y-m-d H:i").":00";
		$this->db->select('*');
        $this->db->from('schedules');		
		$this->db->where($condition);
		$this->db->where("scheduled_time>='$date'");
        $this->db->order_by('scheduled_time','ASC');
        $query = $this->db->get();
        return $query->result_array(); 
	}

	public function get_latest_schedule($condition=Array()){
		
		$currentDate=date("Y-m-d H:i").":00";
		$this->db->select('*');
        $this->db->from('schedules');		
		$this->db->where('keyword_id',$condition['keyword_id']);		
		$this->db->where("scheduled_time >= '$currentDate'");
		$this->db->where("status","P");
        $this->db->order_by('schedule_id','ASC');
		$this->db->limit( 1, 0 );
        $query = $this->db->get();
		#print $this->db->last_query();
        return $query->row(); 
	}
	function get_qualify(){
        $this->db->select('*');
        $this->db->from('qualify');
        $this->db->where('status','A');
		$this->db->order_by('name','ASC');
        $query = $this->db->get();
        return $query->result_array();  
    }
    function getProxy(){
        $this->db->select('*');
        $this->db->from('proxy');
        $this->db->where('status','Y');
		$this->db->order_by('RAND()','ASC');
		$this->db->limit( 1, 0);
        $query = $this->db->get();
        return $query->row();  
    }
	function getEmailsForNotification($searchData){
		$this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.status','1');
        $this->db->where('u.email_notification','1');
        $search_id = $searchData['keyword_id'];
        $accessSearchqry=" (u.user_id IN ( SELECT user_id FROM svi_search_access WHERE search_id=$search_id AND user_id=u.user_id) or u.category=1 )";
        $this->db->where($accessSearchqry);      
        $query = $this->db->get();
        #print $this->db->last_query();
        return $query->result_array();  
	}
	function getLastScrapeStatus($condition){
		$this->db->select('*');
        $this->db->from('schedules');
		if(!empty($condition['keyword_id'])){
			$this->db->where('keyword_id',$condition['keyword_id']);
		}
		$this->db->where("start_time > '2020-01-01'");
		$this->db->group_start();
        $this->db->where('status','C');
        $this->db->or_where('status','S');
		$this->db->group_end();
		$this->db->order_by('start_time','DESC');
		$this->db->order_by('completion_time','DESC');
		
		$this->db->limit(1, 0);
        $query = $this->db->get();
		#print $this->db->last_query();
        return $query->row();  
	}
	public function getScheduleById($condition=Array()){

		$this->db->select('*');
        $this->db->from('schedules');
		$this->db->where($condition);
        //$this->db->order_by('id','ASC');
        $query = $this->db->get();
        return $query->row(); 
	}
	function get_all_active_users(){
        $this->db->select('u.*');
        $this->db->from('users u');
		$this->db->where('status','1');
        $this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
	function get_notifications_by_user(){
        $this->db->select('n.*,k.search_name');
        $this->db->from('notification n');
		$this->db->join('keywords k','n.search_id = k.keyword_id','LEFT');
		$this->db->where('n.read_status','U');
		$this->db->where('n.user_id',$this->session->userdata('id'));
        $query = $this->db->get();
        return $query->result_array();
    }
	function get_key_persons($formData){
        $this->db->select('kcp.*');
        $this->db->from('key_company_person kcp');
		#$this->db->join('keywords k','n.search_id = k.keyword_id','LEFT');
		$this->db->where('kcp.result_id',$formData['result_id']);
		#$this->db->where('n.user_id',$this->session->userdata('id'));
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_all_activeusers(){
        $this->db->select('u.*,CONCAT( u.first_name, " ", u.last_name ) AS fullname');
        $this->db->from('users u');
        $this->db->where('u.status',1);
        $this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
    function get_users_by_searchid($search_id){
        $this->db->select('*');
        $this->db->from('search_access');
        $this->db->where('search_id',$search_id);
       # $this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
    function get_users_for_push_notification($searchId){
        $this->db->select('u.*');
        $this->db->from('users u');
        $this->db->where('u.status',1);
        $accessSearchqry=" (u.user_id IN ( SELECT user_id FROM svi_search_access WHERE search_id=$searchId AND user_id=u.user_id) or u.category=1) ";
        $this->db->where($accessSearchqry);
        #$this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        #print $this->db->last_query();
        $query = $query->result_array();  
        return $query;
    }
    function check_duplicate_proxy($condition){
        $this->db->select('*');
        $this->db->from('proxy');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows(); 
    }
    function get_all_users($userCondition){
        $this->db->select('u.*,CONCAT( u.first_name, " ", u.last_name ) AS fullname');
        $this->db->from('users u');
        #$this->db->where($userCondition);
        $this->db->order_by('u.first_name','ASC');
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
}