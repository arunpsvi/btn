<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_include_path(APPPATH . '/third_party/phpsec');
require_once APPPATH."/third_party/phpsec/Net/SFTP.php";
require_once APPPATH."/third_party/phpsec/Net/SSH2.php";
class Phpsec {
	
	public function __construct($params = array()){
		$this->CI =& get_instance();
	}

	public function createDirOnFileshare($dir){

		$return=1;
		$ssh = new Net_SSH2($this->CI->config->item('FSip'));
		if (!$ssh->login($this->CI->config->item('FSuser'), $this->CI->config->item('FSpass'))) {
			$return=0;
		}else{
			$dirToCreate=$this->CI->config->item('FSBaseDir').$this->CI->config->item('FSRootDir').$dir;
			$result=$ssh->exec("mkdir '$dirToCreate'");
			$ssh->exec("chmod -R 777 '$dirToCreate'");#chmod -R 755
			if(preg_match('/(cannot create directory|syntax error|invalid option)/is',$result,$matcher)){
				$return=0;
			}
		}
		return $return;		
	}

	function renameDirOnFileshare($new,$old){
		
		$return=1;
		$ssh = new Net_SSH2($this->CI->config->item('FSip'));
		if (!$ssh->login($this->CI->config->item('FSuser'), $this->CI->config->item('FSpass'))) {
			$return=0;
		}else{
			$newDir=$this->CI->config->item('FSBaseDir').$this->CI->config->item('FSRootDir').$new;
			$oldDir=$this->CI->config->item('FSBaseDir').$this->CI->config->item('FSRootDir').$old;
			$result=$ssh->exec("mv '$oldDir' '$newDir'");
			if(preg_match('/No such file or directory/is',$result,$matcher)){
				$return=0;
			}
		}
		return $return;		
	}

	function getDirlisting($dir){

		$out="";
		$ssh = new Net_SSH2($this->CI->config->item('FSip'));
		if (!$ssh->login($this->CI->config->item('FSuser'), $this->CI->config->item('FSpass'))) {
			$return=0;
		}else{
			$dirToView=$this->CI->config->item('FSBaseDir').$this->CI->config->item('FSRootDir').$dir;
			$out=$ssh->exec("ls '$dirToView'");
		}
		#$out=preg_replace('/\n/','<br>',$out);
		return $out;		
	}

	function getSubversion($out){
		
		$counter=1;
		$subversion=0;
		$out=preg_replace("/\n/",'#######',$out);
		$outDir=explode("#######",$out);
		for($i=0;$i<sizeof($outDir);$i++){
			if(preg_match('/^(\d\d)\s+/is',$outDir[$i],$matcher)){
				#$out=$this->after($matcher[0],$out);
				$tempVersion=(int)$matcher[1];
				if($tempVersion>$subversion){
					$subversion=$tempVersion;
				}
				$counter++;
				if($counter>99){
					break;
				}
			}
		}
		return $subversion;		
	}
}