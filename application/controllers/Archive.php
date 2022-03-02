<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Archive extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//check_login_user();
		$this->load->model('common_model');
		$this->load->model('archive_model');
		$this->scraped_date=date('Y-m-d h:i:s');
    }

    public function index() {
		
		#Do nothing
	}

	#get rows to be Archived from result table, copy content to new database and delete from live database
	public function archiveResults() {
		
		$websileList=$this->common_model->get_all_websites('JOBS');
		$this->archiveRecords($websileList,'results');

		$websileList=$this->common_model->get_all_websites('SALE');
		$this->archiveRecords($websileList,'saleresults');
		
	}

	function archiveRecords($websileList,$tableName){
		foreach ($websileList as $condition){
			$searchData=$this->archive_model->getRowsTobeArchived($condition);
			foreach ($searchData as $data){
				$dbarchive = $this->load->database('dbarchive', TRUE);
				$chk=$dbarchive->get_where($tableName,array('result_id'=>$data['result_id']))->row();
				if(empty($chk->result_id)){
					$dataToInsert=Array();
					$dataToInsert=$this->createArray($data);
					
					$dataToInsert = $this->security->xss_clean($dataToInsert);
					$manualScheduleId=$this->archive_model->insert($dataToInsert, $tableName);
					$deleteCondition=Array();
					$deleteCondition['result_id']=$data['result_id'];	
					$this->common_model->delete($tableName,$deleteCondition);
				}
			}
		}
	}

	# createArray -- Create array of all columns available in the table of database
	function createArray($dataRow=Array()){
		$dataToInsert=Array();
		foreach ($dataRow as $key=>$value){
			$dataToInsert[$key]=$value;	
		}
		return $dataToInsert;
	}
}
