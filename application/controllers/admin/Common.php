<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('common_model');
    }

    public function index() {
        return false;
    }
	public function getKeywords() {  
		if(!empty($this->input->get('website_id'))){
			$defauktWebsiteId=$this->input->get('website_id');
			echo $this->db->get_where('keywords',array('website_id'=>$defauktWebsiteId))->row()->keywords;
		}
    }
    public function updateNotificationReadStatus() {  
		$dataToUpdate=Array();
		$dataToUpdate['read_status']='R';
		$updateCondition=Array();
		$updateCondition['user_id']=$this->session->userdata('id');
		$this->common_model->update($dataToUpdate, 'notification',$updateCondition);
    }
	public function addQualify() {  
		if(!empty($this->input->get('qualifyName'))){
			$result=Array();
			$data=array();
			$data['name']=trim($this->input->get('qualifyName'));			
			$data = $this->security->xss_clean($data);
			$qid=$this->db->get_where('qualify',array('name'=>$data['name']))->row()->qid;
			if(empty($qid)){
				$insertId = $this->common_model->insert($data, 'qualify');
				$qualifySearchArray=svi_buildArray($this->common_model->get_qualify(),'qid','name');
				$options='';
				$options .="<option value=''>All</option>\n";
				foreach ($qualifySearchArray as $key=>$value){
					$selected='';
					if($value==$data['name']){
						$selected=" selected='selected' ";
					}
					$options .="<option value='".$value."'".$selected.">".$value."</option>\n";
				}
				$options .="<option value='-----'>---------------------</option>\n";
				$options .="<option value='ADD#NEW'>Add new</option>\n";
				$result['option']=$options;
				$result['message']='<font class="text-success">New qualify addes successfully</font>';
				$result['status']='1';
				
			}else{
				$result['message']='<font class="text-danger">Qualify already exists</font>';
				$result['status']='0';
			}
			print json_encode($result);
			
		}
    }
}