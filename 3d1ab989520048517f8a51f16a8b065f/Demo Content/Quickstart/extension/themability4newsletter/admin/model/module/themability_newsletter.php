<?php
namespace Opencart\Admin\Model\Extension\Themability4newsletter\Module;
class ThemabilityNewsletter extends \Opencart\System\Engine\Model {
	
	public function getList($data = array()): array {	
		$sql = "SELECT * FROM " . DB_PREFIX . "subscribe";		
		if (isset($data['start']) || isset($data['limit'])) {			
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalUsers(): int {	
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "subscribe");
		return $query->num_rows;		
	}
	
	public function delete($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "subscribe WHERE id='".$data."'");
	}	

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "subscribe (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `email` varchar(225) NOT NULL,
				  `date` datetime NOT NULL,
				  PRIMARY KEY (`id`) 
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC");	
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "subscribe`");
	}

}
