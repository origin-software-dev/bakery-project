<?php
namespace Opencart\Admin\Model\Extension\Themability4testimonial\Module;
class ThemabilityTestimonial extends \Opencart\System\Engine\Model {
	public function install(){
		
		$sql = [];

		$sql[] = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."themability_testimonial` (
			`themability_testimonial_id` INT(11) NOT NULL AUTO_INCREMENT,
			`status` TINYINT(1) NOT NULL DEFAULT 0,
			`sort_order` INT(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`themability_testimonial_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."themability_testimonial_description`(
			`themability_testimonial_id` INT(11) NOT NULL,
			`language_id` INT(11) NOT NULL,
			`image` VARCHAR(255) NOT NULL ,
			`customer_name` VARCHAR(255) NOT NULL,
			`content` TEXT NOT NULL,
			PRIMARY KEY (`themability_testimonial_id`,`language_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";		

		foreach($sql as $q ){
			$query = $this->db->query( $q );
		}
		return true;
		
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_testimonial`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_testimonial_description`");
	}

	public function addThemabilityTestimonial($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "themability_testimonial SET status = '" . (int)$this->request->post['status'] . "', sort_order = '" . (int)$this->request->post['sort_order'] . "'");
		$themability_testimonial_id = $this->db->getLastId();
		foreach ($data['themability_testimonial_description'] as $language_id => $value) {			
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "themability_testimonial_description SET themability_testimonial_id = '" . (int)$themability_testimonial_id . "',language_id = '" . (int)$language_id . "', customer_name = '" . $this->db->escape($value['customer_name']). "',image = '".$this->db->escape($data['image'])."', content = '" . $this->db->escape($value['content']) . "'");
		}

		$this->cache->delete('themability_testimonial');
		return $themability_testimonial_id;
	}
	
	public function editThemabilityTestimonial($themability_testimonial_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "themability_testimonial SET status = '" . (int)$this->request->post['status'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE themability_testimonial_id = '" . (int)$themability_testimonial_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_testimonial_description WHERE themability_testimonial_id = '" . (int)$themability_testimonial_id . "'");
		
		foreach ($data['themability_testimonial_description'] as $language_id => $value) {

			$this->db->query("INSERT INTO " . DB_PREFIX . "themability_testimonial_description SET themability_testimonial_id = '" . (int)$themability_testimonial_id . "',language_id = '" . (int)$language_id . "', customer_name = '" . $this->db->escape($value['customer_name']) . "',image = '".$this->db->escape($data['image'])."', content = '" .  $this->db->escape($value['content']) . "'");
		}		
	}

	public function deleteThemabilityTestimonial($themability_testimonial_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_testimonial WHERE themability_testimonial_id = '" . (int)$themability_testimonial_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_testimonial_description WHERE themability_testimonial_id = '" . (int)$themability_testimonial_id . "'");		
	}

	public function getThemabilityTestimonial($themability_testimonial_id) {		
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "themability_testimonial t LEFT JOIN " . DB_PREFIX . "themability_testimonial_description td ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE t.themability_testimonial_id = '" . (int)$themability_testimonial_id . "' ");		
		
		return $query->row;
	}

	public function getThemabilityTestimonials($data = array()) {
		if ($data) {			
			$sql = "SELECT * FROM " . DB_PREFIX . "themability_testimonial_description td LEFT JOIN " . DB_PREFIX . "themability_testimonial t ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			$sort_data = array(
				'td.customer_name',
				't.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY td.customer_name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

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
		} else {
			$themability_testimonial_data = $this->cache->get('themability_testimonial.' . $this->config->get('config_language_id'));
			if (!$themability_testimonial_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_testimonial t LEFT JOIN " . DB_PREFIX . "themability_testimonial_description td ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY td.customer_name ASC");
				$themability_testimonial_data = $query->rows;
				$this->cache->set('themability_testimonial.' . $this->config->get('config_language_id'), $themability_testimonial_data);
			}
			return $themability_testimonial_data;
		}
	}

	public function getThemabilityTestimonialDescriptions($themability_testimonial_id) {
		$themability_testimonial_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_testimonial_description WHERE themability_testimonial_id = '" . (int)$themability_testimonial_id . "'");
		
		foreach ($query->rows as $result) {
			$themability_testimonial_description_data[$result['language_id']]= array(
				'customer_name'       => $result['customer_name'],
				'content' => $result['content']
			);
		}

		return $themability_testimonial_description_data;
	}

	public function getTotalThemabilityTestimonials() {		
		// $sql = "SELECT COUNT(*) AS total FROM `". DB_PREFIX . "themability_testimonial`";
		$sql = "SELECT COUNT(*) AS total FROM `". DB_PREFIX . "themability_testimonial` WHERE `status` = 1";
		$query = $this->db->query($sql);
		return (int)$query->row['total'];
	}
}
?>