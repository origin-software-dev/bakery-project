<?php
namespace Opencart\Catalog\Model\Extension\Themability4newsletter\Module;
class Themabilitynewsletter extends \Opencart\System\Engine\Model {

	public function checkalreadyemail($data) {  	   
		$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "subscribe where email='".$data['themability_newsletter_usr_email']."'");	   
		return $query->num_rows;	   
	}	
	 
	public function getList($data) {	
		$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "subscribe where email='".$data['themability_newsletter_usr_email']."'");	    
		return $query->num_rows;		
	}
	
	public function subscribe(array $data) {
		$this->db->query(" INSERT INTO " . DB_PREFIX . "subscribe SET email='".$data['themability_newsletter_usr_email']."' ,date= CURDATE() ");
	}
	
	public function unsubscribe($data) {		
		$this->db->query("DELETE FROM " . DB_PREFIX . "subscribe WHERE email='".$data['themability_newsletter_usr_email']."'");
	}  

}
