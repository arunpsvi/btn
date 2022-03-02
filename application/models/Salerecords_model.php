<?php
class Salerecords_model extends CI_Model {

    function get_sale_listings($condition=Array(), $limit=0, $start=0,$sort_order='',$whereSearchIDIn=Array()){	
        //print_r($whereSearchIDIn);
		$this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location,web.url');
        $this->db->from('saleresults res');
		$this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
		$this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
		$this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
		$this->db->join('websites web','web.id  = res.website_id','LEFT');
		$this->db->where($condition);
        //if($this->session->userdata('role') !='ADMIN'){
        if(!in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" res.search_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }        
        if(!empty($whereSearchIDIn)){
            $this->db->where_in('res.search_id', $whereSearchIDIn);   
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
	function get_sale_listings_total($condition=Array(),$whereSearchIDIn=Array()){		
		$this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location');
        $this->db->from('saleresults res');
		$this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
		$this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
		$this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
		$this->db->where($condition);
        if(!in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
        #if($this->session->userdata('role') !='ADMIN'){
            $userID= $this->session->userdata('id');
            $accessSearchqry=" res.search_id IN ( SELECT search_id FROM svi_search_access WHERE search_id=res.search_id AND user_id=$userID) ";
            $this->db->where($accessSearchqry);
        }
		if(!empty($whereSearchIDIn)){
            $this->db->where_in('res.search_id', $whereSearchIDIn);   
        }
        $query = $this->db->get();
        return $query->num_rows(); 
	}
    function get_sale_details($condition=Array()){
		$this->db->select('res.*');
        $this->db->from('saleresults res');
		$this->db->where($condition);
        /* Don't allow unauthoruzed users to access the detail */
        //if($this->session->userdata('role') !='ADMIN'){
        if(!in_array($this->session->userdata('role'), $this->config->item('salesAccess'))){
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
            $this->db->where($accessSearchqry);
        }        
        $query = $this->db->get();
        return $query->row(); 
    }
}