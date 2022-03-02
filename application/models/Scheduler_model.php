<?php
class Scheduler_model extends CI_Model {

    function getSchedules(){
        
        $currentTime=date('Y-m-d H:s:i');
        $this->db->select('sch.*,web.name');
        $this->db->from('schedules sch');
        $this->db->from('websites web');
        $this->db->where('sch.status','P');
        $this->db->where('web.id=sch.website_id');
        $this->db->where("sch.scheduled_time <= '$currentTime'");
        $this->db->order_by('sch.schedule_id','ASC');
		$this->db->limit( 1, 0);
        $query = $this->db->get();
        //print $this->db->last_query();
        return $query->row(); 
    }	
}