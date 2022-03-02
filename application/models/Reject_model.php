<?php
class Reject_model extends CI_Model {

    function checkInRejectedList($condition){
		
		if(!empty($condition['companyWebsite'])){
			$this->db->select('*');
			$this->db->from('reject_list rl');
			$this->db->where('website_id',$condition['website_id']);
			$this->db->like('website_url', $condition['companyWebsite']);
			#$this->db->where('companyUrl',$condition['companyUrl']);
			$query = $this->db->get();
			#print $this->db->last_query();exit;
			return $query->num_rows();
		}elseif(!empty($condition['company_name_formatted'])){
			$this->db->select('*');
			$this->db->from('reject_list rl');
			$this->db->where('website_id',$condition['website_id']);
			$this->db->where('company_name_formatted',$condition['company_name_formatted']);
			$query = $this->db->get();
			return $query->num_rows();
		}elseif(!empty($condition['companyUrl'])){
			$this->db->select('*');
			$this->db->from('reject_list rl');
			$this->db->where('website_id',$condition['website_id']);
			$this->db->where('companyUrl',$condition['companyUrl']);
			$query = $this->db->get();
			return $query->num_rows();
		}else{
			return 0;
		}
    }

	function get_all_rejectlist($condition=Array(), $limit=0, $start=0,$sort_order=''){
		$this->db->select('rl.*,web.name as website_name');
        $this->db->from('reject_list rl');
		if(!empty($condition['website_id'])){
			$this->db->where('website_id',$condition['website_id']);
		}
		$this->db->join('websites web','web.id = rl.website_id','LEFT');
        $this->db->order_by('rl.company_name','ASC');
		if($limit>0){
			$this->db->limit( $limit, $start );
		}
        $query = $this->db->get();
        $query = $query->result_array();  
		//print $this->db->last_query();exit;
        return $query;
	}

	function get_all_rejectlist_total($condition=Array()){
		$this->db->select('rl.*,web.name as website_name');
        $this->db->from('reject_list rl');
		if(!empty($condition['website_id'])){
			$this->db->where('website_id',$condition['website_id']);
		}
		$this->db->join('websites web','web.id = rl.website_id','LEFT');
        $this->db->order_by('rl.company_name','ASC');
        $query = $this->db->get();
	    return $query->num_rows(); 
	}
	
	function get_single_company_info($condition=Array()){
		$this->db->select('*');
		$this->db->from('reject_list rl');
		$this->db->where('reject_id',$condition['reject_id']);
		$query = $this->db->get();
		return $query->row();
	}
}