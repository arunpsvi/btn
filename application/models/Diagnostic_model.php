<?php
class Diagnostic_model extends CI_Model {

    function getDiagnosticSchedules($postType='JOBS',$data=Array(), $limit=0, $start=0,$sort_order=''){
        
        $nextDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $this->db->select('sch.*,web.name as website_name,keyw.search_name as bot_name');
        $this->db->from('schedules sch,websites web,keywords keyw');
        
        //$this->db->where('sch.status','P');
        $this->db->where('web.id=sch.website_id');        
        $this->db->where("web.post_type",$postType);
      
        $this->db->where('keyw.keyword_id=sch.keyword_id');

        $this->db->where("sch.scheduled_time < '$nextDate'");
        $this->db->order_by('sch.scheduled_time','Desc');
        if($limit>0){
			$this->db->limit( $limit, $start );
		}
	    $query = $this->db->get();
        //print $this->db->last_query();
        return $query->result_array(); 
    }
    function getDiagnosticSchedulesTotal($postType='JOBS'){
        
        $nextDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
        $this->db->select('sch.*,web.name as website_name,keyw.search_name as bot_name');
        $this->db->from('schedules sch,websites web,keywords keyw');
        
        //$this->db->where('sch.status','P');
        $this->db->where('web.id=sch.website_id');        
        $this->db->where("web.post_type",$postType);
      
        $this->db->where('keyw.keyword_id=sch.keyword_id');

        $this->db->where("sch.scheduled_time < '$nextDate'");
        $this->db->order_by('sch.scheduled_time','Desc');
       
	    $query = $this->db->get();
        //print $this->db->last_query();
        return $query->num_rows(); 
    }
    
    // dbarchive also used to keep log information to reduce the load from original table 
    function insertLog($data,$table){
        $dbarchive = $this->load->database('dbarchive', TRUE);
		$data=my_clear_fields($data);
        $dbarchive->insert($table,$data);
        return $this->db->insert_id();
    }

    function get_log_details($data=Array()){
        $dbarchive = $this->load->database('dbarchive', TRUE);

        $dbarchive->select('dl.*');
        $dbarchive->from('diagnostic_log dl');
           
        $dbarchive->where($data);

        $dbarchive->order_by('dl.status','asc');
        $dbarchive->order_by('dl.log_time','asc');
       
	    $query = $dbarchive->get();
        return $query->result_array(); 
		
    }
}