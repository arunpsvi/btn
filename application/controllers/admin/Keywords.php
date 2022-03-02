<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Keywords extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		$this->load->model('common_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('pagination');		
    }

    public function index() {
		
		$this->form_validation->set_rules('keywords', 'keywords', 'required');
		$this->form_validation->set_rules('website_id', 'Website', 'required');	
		$data['formData']=$this->input->post();
		$defauktWebsiteId=0;
		if(!empty($this->input->get('website_id'))){
			$defauktWebsiteId=$this->input->get('website_id');
		}
		if ($this->form_validation->run() == false) {
			$website_list = $this->common_model->get_all_websites();
			$websiteListArr=svi_buildArray($website_list,'id','url');
			
			if(empty($defauktWebsiteId)){
				foreach($websiteListArr as $key=>$value){
					$defauktWebsiteId=$key;
					break;
				}
			}

			//$this->db->get_where('keywords',array('website_id'=>1))->row()->keywords
			$data['website_id']=$defauktWebsiteId;
			$data['loadDB']=$this->input->get('loadDB');
			$dbKeywords='';
			if(!empty($data['loadDB']) && $data['loadDB']==1){
				$condition = array(
					'website_id' => $data['website_id']
				);
				$allKeywords=$this->common_model->get_all_keywords($condition);
				
				foreach ($allKeywords as $keyword){
					if(empty($dbKeywords)){
						$dbKeywords .=$keyword['keyword'];
					}else{
						$dbKeywords .=','.$keyword['keyword'];
					}
				}
				$data['keywords'] = $dbKeywords;
			}else{
				$data['keywords'] = $this->db->get_where('keywords',array('website_id'=>$defauktWebsiteId))->row()->keywords;
			}

			$data['websiteListArr'] = $websiteListArr;
			$data['main_content'] = $this->load->view('admin/keywords/add', $data, TRUE);
	        $this->load->view('admin/index', $data);
		}else{
			$keywordId = $this->db->get_where('keywords',array('website_id'=>$this->input->post('website_id')))->row()->keyword_id;
			if(empty($keywordId)){
				$data = array(
					'website_id' => $this->input->post('website_id'),
					'keywords' => $this->input->post('keywords')
				);
				$data['keywords']=preg_replace('/,\s+/',',',$data['keywords']);
				$data['keywords']=preg_replace('/\s+,/',',',$data['keywords']);
				$data = $this->security->xss_clean($data);
				$keywordId = $this->common_model->insert($data, 'keywords');
				$keywords=explode(",",$data['keywords']);
				foreach ($keywords as $keyword){
					$keyword=trim($keyword);
					if(empty($this->db->get_where('keyword_list',array('website_id'=>$this->input->post('website_id'),'keyword'=>$keyword))->row()->id)){
						$data = array(
							'website_id' => $this->input->post('website_id'),
							'keyword' => $keyword
						);
						$this->common_model->insert($data, 'keyword_list');
					}
				}
				$this->session->set_flashdata('msg', 'keywords updated Successfully');
			}else{
				
				$data = array(
					'keywords' => $this->input->post('keywords')
				);
				$condition = array(
					'website_id' => $this->input->post('website_id')
				);
				$data['keywords']=preg_replace('/,\s+/',',',$data['keywords']);
				$data['keywords']=preg_replace('/\s+,/',',',$data['keywords']);
				$data = $this->security->xss_clean($data);
				$keywordId = $this->common_model->update($data, 'keywords',$condition);
				$keywords=explode(",",$data['keywords']);
				foreach ($keywords as $keyword){
					$keyword=trim($keyword);
					if(empty($this->db->get_where('keyword_list',array('website_id'=>$this->input->post('website_id'),'keyword'=>$keyword))->row()->id)){
						$data = array(
							'website_id' => $this->input->post('website_id'),
							'keyword' => $keyword
						);
						$this->common_model->insert($data, 'keyword_list');
					}
				}
				$this->session->set_flashdata('msg', 'keywords added Successfully');			
			}			
			redirect(site_url('admin/keywords?website_id='.$this->input->post('website_id')),'refresh');
		}
		
    }

    
}

