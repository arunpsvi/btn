<?php
    Class Jobs_model extends CI_model{
        public function __construct(){
            parent::__construct();
            $this->load->database();
        }
        public function get_all_jobs($condition=Array()){
            $this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location');
            $this->db->from('results res');
            $this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
            $this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
            $this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
            $this->db->where($condition);
           
            $query = $this->db->get();
            //print $this->db->last_query();exit;
            return $query->result_array();  
        }
       
       
    }

?>