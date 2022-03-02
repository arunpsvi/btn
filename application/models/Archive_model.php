<?php
class Archive_model extends CI_Model {

   

	function getRowsTobeArchived($condition=Array()){
        
		$this->db->select('*');
        $this->db->from('results');
        
        $dayToKeep=$condition['records_to_keep'];
        #$date=date('Y-m-d',strtotime("- $dayyToKeep days"));
		$this->db->where("posted_date < NOW() - INTERVAL $dayToKeep DAY");		
		$this->db->where("posted_date != '0000-00-00'");		
		$this->db->where("website_id",$condition['id']);		
        $query = $this->db->get();
		//print $this->db->last_query();
        return $query->result_array(); 
	}

    public function insert($data,$table){
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$data=my_clear_fields($data);
        $dbarchive->insert($table,$data);  
        return $dbarchive->insert_id();
    }

    function get_job_listings($condition=Array(), $limit=0, $start=0,$sort_order='',$whereSearchIDIn=Array()){	
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$dbarchive->select('res.*');
        $dbarchive->from('results res');
		$dbarchive->where($condition);
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            #SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID

            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }        
        if(!empty($whereSearchIDIn)){
            $dbarchive->where_in('res.search_id', $whereSearchIDIn);   
        }
		if(!empty($sort_order)){
			$dbarchive->order_by('qualify',$sort_order);
		}
        $dbarchive->order_by('scraped_date','DESC');
        $dbarchive->order_by('result_id','DESC');
		if($limit>0){
			$dbarchive->limit( $limit, $start );
		}
        $query = $dbarchive->get();
		#print $this->db->last_query();exit;
        return $query->result_array(); 
	}
    
    function get_job_listings_total($condition=Array(),$whereSearchIDIn=Array()){	
        $dbarchive = $this->load->database('dbarchive', TRUE);	
		$dbarchive->select('res.*');
        $dbarchive->from('results res');
		$dbarchive->where($condition);
        
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }
		if(!empty($whereSearchIDIn)){
            $dbarchive->where_in('res.search_id', $whereSearchIDIn);   
        }
        $query = $dbarchive->get();
        return $query->num_rows(); 
	}
    function get_job_details($condition=Array()){
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$dbarchive->select('res.*');
        $dbarchive->from('results res');
		$dbarchive->where($condition);
        /* Don't allow unauthoruzed users to access the detail */
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }        
        $query = $dbarchive->get();
        return $query->row(); 
    }
    function get_sale_listings($condition=Array(), $limit=0, $start=0,$sort_order='',$whereSearchIDIn=Array()){	
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$dbarchive->select('res.*');
        $dbarchive->from('saleresults res');
		$dbarchive->where($condition);
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            #SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID

            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }        
        if(!empty($whereSearchIDIn)){
            $dbarchive->where_in('res.search_id', $whereSearchIDIn);   
        }
		if(!empty($sort_order)){
			$dbarchive->order_by('qualify',$sort_order);
		}
        $dbarchive->order_by('scraped_date','DESC');
        $dbarchive->order_by('result_id','DESC');
		if($limit>0){
			$dbarchive->limit( $limit, $start );
		}
        $query = $dbarchive->get();
		#print $this->db->last_query();exit;
        return $query->result_array(); 
	}
    
    function get_sale_listings_total($condition=Array(),$whereSearchIDIn=Array()){	
        $dbarchive = $this->load->database('dbarchive', TRUE);	
		$dbarchive->select('res.*');
        $dbarchive->from('saleresults res');
		$dbarchive->where($condition);
        
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }
		if(!empty($whereSearchIDIn)){
            $dbarchive->where_in('res.search_id', $whereSearchIDIn);   
        }
        $query = $dbarchive->get();
        return $query->num_rows(); 
	}
    function get_sale_details($condition=Array()){
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$dbarchive->select('res.*');
        $dbarchive->from('saleresults res');
		$dbarchive->where($condition);
        /* Don't allow unauthoruzed users to access the detail */
        if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $this->db->select('search_id');
            $this->db->from('search_access');
            $this->db->where("user_id",$userID);
            $query = $this->db->get();
            $results=$query->result_array();
            foreach($results as $result){
                $arr[]=$result['search_id'];
            }            
            $ids = join("','",$arr);   
            
            $accessSearchqry=" res.search_id IN ('$ids') ";
            $dbarchive->where($accessSearchqry);
        }        
        $query = $dbarchive->get();
        return $query->row(); 
    }

    function deleteByIn($table,$field_name,$delete_ids){
        $dbarchive = $this->load->database('dbarchive', TRUE);
        $dbarchive->where_in($field_name, $delete_ids);
		$dbarchive->delete($table);
        return;
    }
}