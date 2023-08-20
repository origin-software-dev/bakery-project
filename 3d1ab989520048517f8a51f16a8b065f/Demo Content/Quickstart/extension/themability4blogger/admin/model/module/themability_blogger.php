<?php
namespace Opencart\Admin\Model\Extension\Themability4blogger\Module;
class ThemabilityBlogger extends \Opencart\System\Engine\Model {

	public function createBlogs() {
		$create_themability_blogger = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "themability_blogger` (`themability_blogger_id` int(11) NOT NULL auto_increment, `module_id` int(11) NOT NULL, `status` int(1) NOT NULL default '0', `image` varchar(255) default NULL, `banner_image` varchar(255) default NULL, `date_added` datetime NOT NULL default '0000-00-00 00:00:00', `date_modified` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (`themability_blogger_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$this->db->query($create_themability_blogger);

		$create_themability_blogger_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "themability_blogger_description` (`themability_blogger_id` int(11) NOT NULL default '0', `language_id` int(11) NOT NULL default '0', `title` varchar(64) NOT NULL default '', `description` text NOT NULL, PRIMARY KEY (`themability_blogger_id`,`language_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$this->db->query($create_themability_blogger_description);

		$create_themability_blogger_comment = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "themability_blogger_comment` (`themability_blogger_comment_id` int(11) NOT NULL auto_increment, `themability_blogger_id` int(11) NOT NULL, `approved` int(1) NOT NULL default '0', `author` varchar(64) NOT NULL default '', `email` varchar(96) NOT NULL, `date_added` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (`themability_blogger_comment_id`, `themability_blogger_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$this->db->query($create_themability_blogger_comment);

		$create_themability_blogger_comment_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "themability_blogger_comment_description` (`themability_blogger_comment_id` int(11) NOT NULL default '0', `language_id` int(11) NOT NULL default '0', `comment` text NOT NULL, PRIMARY KEY (`themability_blogger_comment_id`,`language_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
		$this->db->query($create_themability_blogger_comment_description);
	}

	public function dropBlogs() {
		$drop_themability_blogger = "DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_blogger`;";
		$this->db->query($drop_themability_blogger);

		$drop_themability_blogger_description = "DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_blogger_description`;";
		$this->db->query($drop_themability_blogger_description);

		$drop_themability_blogger_comment = "DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_blogger_comment`;";
		$this->db->query($drop_themability_blogger_comment);

		$drop_themability_blogger_comment_description = "DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_blogger_comment_description`;";
		$this->db->query($drop_themability_blogger_comment_description);
	}

	public function addModule($code, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");

		$module_id = $this->db->getLastId();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE module_id = '" . (int)$module_id . "'");

		$settings = json_decode($query->row['setting'], true);

		$settings['module_id'] = $module_id;

		$this->db->query("UPDATE " . DB_PREFIX . "module SET setting = '" . $this->db->escape(json_encode($settings)) . "' WHERE module_id = '" . (int)$module_id . "'");

		return $module_id;
	}

	public function addBlog($data) {		
		$this->db->query("INSERT INTO " . DB_PREFIX . "themability_blogger SET module_id = '" . (int)$data['module_id'] . "', status = '" . (int)$data['status'] . "', date_added = now(), date_modified = now()");

		$themability_blogger_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger SET image = '" . $this->db->escape($data['image']) . "' WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
		}
		if (isset($data['banner_image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger SET banner_image = '" . $this->db->escape($data['banner_image']) . "' WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
		}

		foreach ($data['themability_blogger_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "themability_blogger_description SET themability_blogger_id = '" . (int)$themability_blogger_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}

	public function editBlog($themability_blogger_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger SET module_id = '" . (int)$data['module_id'] . "', status = '" . (int)$data['status'] . "', date_modified = now() WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger SET image = '" . $this->db->escape($data['image']) . "' WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
		}

		if (isset($data['banner_image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger SET banner_image = '" . $this->db->escape($data['banner_image']) . "' WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_blogger_description WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		foreach ($data['themability_blogger_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "themability_blogger_description SET themability_blogger_id = '" . (int)$themability_blogger_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}

	public function deleteBlog($themability_blogger_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_blogger WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_blogger_description WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
	}	

	public function getBlog($themability_blogger_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "themability_blogger WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		return $query->row;
	}

	public function getBlogTitle($themability_blogger_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		if ($query->row) {
			return $query->row['title'];
		} else {
			return false;
		}
	}

	public function getBlogDescriptions($themability_blogger_id) {
		$themability_blogger_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_description WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		foreach ($query->rows as $result) {
			$themability_blogger_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}

		return $themability_blogger_description_data;
	}

	public function deleteBlogComment($themability_blogger_comment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "themability_blogger_comment_description WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");
	}

	public function approveComment($themability_blogger_comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger_comment SET approved = '1' WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");
	}

	public function disapproveComment($themability_blogger_comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "themability_blogger_comment SET approved = '0' WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");
	}

	public function getTotalBlogComments($themability_blogger_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		if ($query->row) {
			return $query->row['total'];
		} else {
			return false;
		}
	}

	public function getBlogComments($themability_blogger_id, $data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'";

			$sort_data = array(
				'author',
				'date_added'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY date_added";
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
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");

			return $query->rows;
		}
	}

	public function getBlogComment($themability_blogger_comment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");

		return $query->row;
	}

	public function getBlogCommentDescriptions($themability_blogger_comment_id) {
		$themability_blogger_comment_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_comment_description WHERE themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "'");

		foreach ($query->rows as $result) {
			$themability_blogger_comment_data[$result['language_id']] = array(
				'comment' => $result['comment']
			);
		}

		return $themability_blogger_comment_data;
	}

	public function getBlogsByModule($module_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND b.module_id = '" . (int)$module_id . "' ORDER BY b.date_added");

		return $query->rows;
	}

	public function getBlogs($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'bd.title',
				'b.module_id',
				'b.date_added'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY b.module_id, b.date_added";
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
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY b.date_added ASC");

			return $query->rows;
		}
	}

	public function getTotalBlogs() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "themability_blogger");

		return $query->row['total'];
	}

	public function getModule($module_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

		return $query->row;
	}
}