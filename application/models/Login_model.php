<?php
class Login_model extends CI_Model {

    public function edit_option_md5($action, $id, $table){
        $this->db->where('md5(id)',$id);
        $this->db->update($table,$action);
        return;
    }

    //-- check post email
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


    // check valid user by id
    public function validate_id($id){
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('md5(id)', $id); 
        $this->db->limit(1);
        $query = $this->db->get();
        if($query -> num_rows() == 1){                 
            return $query->result();
        }
        else{
            return false;
        }
    }



    //-- check valid user
    function validate_user(){            
        
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $this->input->post('user_name')); 
        $this->db->where('password', md5($this->input->post('password')));
        //$this->db->where('status', 1);
        $this->db->limit(1);
        $query = $this->db->get();   
        if($query->num_rows() == 1){ 			
           return $query->result();
        }
        else{
            return false;
        }
    }

	function getUserByEmail($email){
        $this->db->select('u.*');
        $this->db->from('users u');
		$this->db->where('email', $email); 
        $query = $this->db->get();
        return $query->row();  
    }
    function getLastAccessDetail($user_id){
        $this->db->select('ul.*');
        $this->db->from('user_log ul');
		$this->db->where('ul.user_id', $user_id); 
        $this->db->order_by('ul.login_datetime','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();  
    }
    function getUsersAccessDetail($condition, $limit=0, $start=0,$sort_order=''){
        $this->db->select('ul.*,u.first_name,u.last_name');
        $this->db->from('user_log ul');
        $this->db->join('users u','u.user_id = ul.user_id','LEFT');
		$this->db->where($condition); 
        $this->db->order_by('ul.login_datetime','DESC');
        if($limit>0){
			$this->db->limit( $limit, $start );
		}
        $query = $this->db->get();
        return $query->result_array();  
    }
    function getUsersAccessDetailTotal($condition){
        $this->db->select('ul.*,u.first_name,u.last_name');
        $this->db->from('user_log ul');
        $this->db->join('users u','u.user_id = ul.user_id','LEFT');
		$this->db->where($condition); 
        $this->db->order_by('ul.login_datetime','DESC');
       
        $query = $this->db->get();
        return $query->num_rows();  
    }
}