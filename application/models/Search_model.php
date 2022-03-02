<?php
class Search_model extends CI_Model {

    function get_assigned_searches($user_id){
        $this->db->select('sa.*,keyw.search_name');
        $this->db->from('search_access sa');
        $this->db->join('keywords keyw','keyw.keyword_id = sa.search_id','LEFT');
        $this->db->where('sa.user_id',$user_id);
        $query = $this->db->get();
        $query = $query->result_array();  
        return $query;
    }
}