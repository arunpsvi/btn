<?php
    Class Jobs_model extends CI_model{
        public function __construct(){
            parent::__construct();
            $this->load->database();
        }
        public function get_all_jobs($condition=Array(),$limit=10, $start=0){
            $this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location,web.url as websiteName');
            $this->db->from('results res');
            $this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
            $this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
            $this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
            $this->db->join('websites web','web.id  = res.website_id','LEFT');
            $this->db->where($condition);
            $this->db->order_by('res.scraped_date','DESC');
            $this->db->order_by('res.result_id','DESC');
            $this->db->limit( $limit, $start);
            $query = $this->db->get();
            #print $this->db->last_query();exit;
            return $query->result_array();  
        }
        public function get_total_jobs($condition=Array()){
            $this->db->select('res.*,sub_cat.sub_category_name,cat.category_name,suburls.name as location,web.url as websiteName');
            $this->db->from('results res');
            $this->db->join('subcategories sub_cat','sub_cat.sub_cat_id = res.sub_category_id','LEFT');
            $this->db->join('categories cat','cat.category_id = sub_cat.category_id','LEFT');
            $this->db->join('suburls','suburls.suburl_id  = res.suburl_id','LEFT');
            $this->db->join('websites web','web.id  = res.website_id','LEFT');
            $this->db->where($condition);
            $query = $this->db->get();
            return $query->num_rows();  
        }
       
       
    }

?>