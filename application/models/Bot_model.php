<?php
class Bot_model extends CI_Model {

	function get_bot_details($condition=Array(),$post_type=''){
        
		$this->db->select('keyw.*');
        $this->db->from('keywords keyw');
		$this->db->where($condition);		
        
        #Checked not empty because condition need to be skipped for cron jobs
        if(!in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" keyw.keyword_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=keyw.keyword_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }
        # for user access return all bots, while on bot page show bots for JOBS or SALE
        if(!empty($post_type)){
            $this->db->join('websites web','web.id  = keyw.website_id','LEFT');
           # $this->db->where('web.post_type',$post_type);	
        }

        
       
        $query = $this->db->get();
		#print $this->db->last_query();
        return $query->row(); 
	}
    function countResults($condition){

		$this->db->select('count(*) as total');
        $this->db->from('saleresults');
		$this->db->where($condition);
		$query = $this->db->get();
	//	print $this->db->last_query();
        return $query->row(); 
	}
}