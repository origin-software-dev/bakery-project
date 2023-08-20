<?php
namespace Opencart\Catalog\Model\Extension\Themability4blogger\Module;
class ThemabilityBlogger extends \Opencart\System\Engine\Model {
	public function getBlogsByModule($module_id, $limit = 0) {
		if ($limit) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE b.status = '1' AND bd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND b.module_id = '" . (int)$module_id . "' ORDER BY b.date_added DESC LIMIT " . (int)$limit);
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE b.status = '1' AND bd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND b.module_id = '" . (int)$module_id . "' ORDER BY b.date_added DESC");
		}

		return $query->rows;
	}

	public function getBlog($themability_blogger_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger b LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON (b.themability_blogger_id = bd.themability_blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND b.themability_blogger_id = '" . (int)$themability_blogger_id . "'");

		return $query->row;
	}

	public function addComment($themability_blogger_id, $data, $language_id){
		$this->db->query("INSERT INTO " . DB_PREFIX . "themability_blogger_comment SET themability_blogger_id = '" . (int)$themability_blogger_id . "', approved = '" . (int)$data['auto_approve'] . "', author = '" . $this->db->escape($data['author']) . "', email = '" . $this->db->escape($data['email']) . "', date_added = now()");

		$themability_blogger_comment_id = $this->db->getLastId();

		$this->db->query("INSERT INTO " . DB_PREFIX . "themability_blogger_comment_description SET themability_blogger_comment_id = '" . (int)$themability_blogger_comment_id . "', language_id = '" . (int)$language_id . "', comment = '" . $this->db->escape($data['comment']) . "'");

		$blog_info = $this->getBlog($themability_blogger_id);			
		$this->load->controller('mail/authorize');
			// $mail = new Mail();
			$mail = new \Opencart\System\Library\Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($data['email']);
			$mail->setSender($data['author']);
			$mail->setSubject(sprintf($this->language->get('email_subject'), $blog_info['title']));
			$mail->setText(sprintf($this->language->get('email_content'), $blog_info['title']));		
			
			### email_send_status ###		
			error_reporting(0);
			if(!$mail->send()){
				$email_send_status = $this->session->data['email_send_status'] = 0;
			}else{
				$email_send_status = $this->session->data['email_send_status'] = 1;
			}
			### .email_send_status ###
	}

	public function getBlogComments($themability_blogger_id, $language_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger_comment bc LEFT JOIN " . DB_PREFIX . "themability_blogger_comment_description bcd ON (bc.themability_blogger_comment_id = bcd.themability_blogger_comment_id) WHERE bcd.language_id = '" . (int)$language_id . "' AND bc.approved = '1' AND bc.themability_blogger_id = '" . (int)$themability_blogger_id . "' ORDER BY bc.date_added DESC");
		return $query->rows;
	}

	public function getBlogs() {
		$blog_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "themability_blogger WHERE status = '1' GROUP BY module_id");
		if ($query->rows) {
			foreach ($query->rows as $blog) {
				if ($this->getLayoutModule('themability_blogger.' . $blog['module_id'])) {
					$blog_data[] = $blog;
				}
			}
		}
		return $blog_data;
	}

	public function getLayoutModule($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route lr LEFT JOIN " . DB_PREFIX . "layout_module lm ON (lr.layout_id = lm.layout_id) WHERE lr.store_id = '" . (int)$this->config->get('config_store_id') . "' AND lm.code = '" . $this->db->escape($code) . "'");
		if ($query->rows) {
			return true;
		} else {
			return false;
		}
	}

	public function getModule($module_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");
		return $query->row;
	}

	public function getModulesByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE code = '" . $this->db->escape($code) . "'");
		return $query->rows;
	}

	public function getLanguageByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE code = '" . $this->db->escape($code) . "'");
		return $query->row;
	}
	public function getTotalBlogComments($themability_blogger_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "themability_blogger_comment WHERE themability_blogger_id = '" . (int)$themability_blogger_id . "'");
		if ($query->row) {
			return $query->row['total'];
		} else {
			return false;
		}
	}

	### getAllBlogs ###
	public function getAllBlogs($pageno = 1 , $no_of_records_per_page = 10){
		$themability_blogger_status = 1;
		## pagination ##
		$offset = ($pageno-1) * $no_of_records_per_page; 
		$module_status = '%\"status\":\"1\"%'; # check for module setting in status #
		$total_blogs_sql = "SELECT 
				COUNT(b.themability_blogger_id) AS total_blogs
			FROM
				" . DB_PREFIX . "themability_blogger b
				LEFT JOIN " . DB_PREFIX . "module bbm ON
					b.module_id = bbm.module_id AND bbm.setting LIKE '".$module_status."'
				LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON
					b.themability_blogger_id = bd.themability_blogger_id
				WHERE
					b.status = '". (int)$themability_blogger_status ."' AND bd.language_id = '". (int)$this->config->get('config_language_id') ."' AND bbm.module_id IN(SELECT bb.module_id FROM " . DB_PREFIX . "themability_blogger bb WHERE STATUS = '". (int)$themability_blogger_status."' GROUP BY module_id)
				ORDER BY
					b.date_added
				DESC";

		$total_blogs_query = $this->db->query($total_blogs_sql);
		$total_blogs = $total_blogs_query->row['total_blogs'];	
		$total_pages = ceil($total_blogs / $no_of_records_per_page);
		## .pagination ##		

		$all_blogs_sql = "SELECT
							bd.*,b.*,bbm.*, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "themability_blogger_comment bbc WHERE bbc.themability_blogger_id = b.themability_blogger_id  AND bbc.approved = 1) AS total
						FROM
						" . DB_PREFIX . "themability_blogger b
						LEFT JOIN " . DB_PREFIX . "module bbm ON
							b.module_id = bbm.module_id AND bbm.setting LIKE '".$module_status."'
						LEFT JOIN " . DB_PREFIX . "themability_blogger_description bd ON    
							b.themability_blogger_id = bd.themability_blogger_id
						WHERE
							b.status = '". (int)$themability_blogger_status ."' AND bd.language_id = '". (int)$this->config->get('config_language_id') ."'    
							AND 
							bbm.module_id in (SELECT bb.module_id FROM " . DB_PREFIX . "themability_blogger bb WHERE status = '". (int)$themability_blogger_status."' GROUP BY module_id)
						ORDER BY
							b.date_added
						DESC 
						LIMIT ".$offset.", ".$no_of_records_per_page;

		$query = $this->db->query($all_blogs_sql);
		$all_blog_data = array();
		if ($query->rows) {
			foreach ($query->rows as $blog) {	
				$blog['pageno'] = $pageno;			
				$blog['total_blogs'] = $total_blogs;			
				$blog['total_pages'] = $total_pages;
				$blog['offset'] = $offset;
				$all_blog_data[] = $blog;
			}
		}		
		return $all_blog_data;		
	}
	### getAllBlogs ###
}
?>