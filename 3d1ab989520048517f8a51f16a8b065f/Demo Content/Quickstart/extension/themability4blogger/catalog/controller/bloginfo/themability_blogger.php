<?php
namespace Opencart\Catalog\Controller\Extension\Themability4blogger\Bloginfo;
use \Opencart\System\Helper AS Helper;
class ThemabilityBlogger extends \Opencart\System\Engine\Controller {
	private $error = [];

	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('setting/extension');
		$this->document->addStyle('extension/themability4blogger/catalog/view/css/style.css');	
		$this->document->addStyle('extension/themability4blogger/catalog/view/css/lightbox.css');
		$this->document->addScript('extension/themability4blogger/catalog/view/javascript/lightbox-2.6.min.js');
		$type="module";
		$results = $this->model_setting_extension->getExtensions($type);
		$data['blog_enable'] = 0;
		
		foreach($results as $result){			
			if($result['extension']==="themability4blogger"){
				$data['blog_enable'] = 1;
			}
		}	
		if($data['blog_enable']){
			
		}else{
			$this->response->redirect($this->url->link('common/home'));			
		}		
	}

	public function index(array $setting = []):void {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->load->model('tool/image');
		if (!isset($this->request->get['themability_blogger_id'])) {
			$blogs_data = $this->blogs();
			$this->response->setOutput($this->load->view('extension/themability4blogger/bloginfo/themability_blogger_blogs', $blogs_data));
			return;
		}
	
		if(!isset($this->session->data['language']) || (isset($this->session->data['language']) && $this->session->data['language'] == "")){
			$language_id =  $this->config->get('config_language_id');
			$languages = $this->model_localisation_language->getLanguages($language_id);
			$lang_code = "en-gb";
			foreach( $languages as  $language){
				if($language['language_id'] == $language_id){
					$lang_code = $language['code'];
				}
			}
			$this->session->data['language'] = $lang_code;
		}		

		$language = $this->model_extension_themability4blogger_module_themability_blogger->getLanguageByCode($this->session->data['language']);
		$blog_info = $this->model_extension_themability4blogger_module_themability_blogger->getBlog($this->request->get['themability_blogger_id']);

		if ($blog_info) {
		$url = '';
	
		$this->document->setTitle($blog_info['title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $blog_info['title'],
			'href' => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', 'themability_blogger_id=' . $this->request->get['themability_blogger_id'], true)
		);

		$data['heading_title'] = $blog_info['title'];		
		$data['date'] = date('j F, Y', strtotime($blog_info['date_added']));	
		

		$module = $this->model_extension_themability4blogger_module_themability_blogger->getModule($blog_info['module_id']);

		$setting = json_decode($module['setting'],true);
		
		$data['blogs'] = array(
			'image'  => $this->model_tool_image->resize($blog_info['image'], $setting['banner_width'], $setting['banner_height'])			
		);
		if(isset($blog_info['banner_image'])){
			$data['blogs']['banner_image'] = $this->model_tool_image->resize($blog_info['banner_image'], $setting['banner_width'], $setting['banner_height']);
		}
			
		$data['description'] = html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8');

		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_login_required'] = sprintf($this->language->get('text_login_required'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
		$data['text_your_comments'] = $this->language->get('text_your_comments');

		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_comment'] = $this->language->get('entry_comment');

		$data['button_comment_add'] = $this->language->get('button_comment_add');
		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_list_blogs'] = $this->language->get('button_list_blogs');
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ($this->error) {
				$data['div_display'] = 'block';
			} else {
				$data['div_display'] = 'none';
			}
		} else {
			$data['div_display'] = 'none';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['comment'])) {
			$data['error_comment'] = $this->error['comment'];
		} else {
			$data['error_comment'] = '';
		}		

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
		### email_send_status ###
		if (isset($this->session->data['email_send_status'])) {
			if ($this->session->data['email_send_status'] == 1) {
				$data['email_success'] = $this->language->get('text_email_success');
				$data['email_fail'] = '';
			} elseif($this->session->data['email_send_status'] == 0) {
				$data['email_fail'] = $this->language->get('text_email_error');
				$data['email_success'] = '';
			}
			unset($this->session->data['email_send_status']);
		} else {
			$data['email_success'] = '';
			$data['email_fail'] = '';			
		}
		### .email_send_status ###	
		$module = $this->model_extension_themability4blogger_module_themability_blogger->getModule($blog_info['module_id']);

		$setting = json_decode($module['setting'],true);

		$data['allow_comments'] = $setting['comments'];
		$data['login_required'] = $setting['login'];
		$data['auto_approve'] = $setting['auto_approve'];

		if ($this->customer->isLogged()) {
			$data['is_logged'] = true;
		} else {
			$data['is_logged'] = false;
		}
		$data['blog_comments'] = array();
		$blog_comments = $this->model_extension_themability4blogger_module_themability_blogger->getBlogComments($this->request->get['themability_blogger_id'], $language['language_id']);
	
		if ($blog_comments) {
			foreach ($blog_comments as $comment) {
				$data['blog_comments'][] = array(
					'author'     => $comment['author'],
					'comment'    => html_entity_decode($comment['comment'], ENT_QUOTES, 'UTF-8'),
					'date_added' => date($this->language->get('date_format_short'), strtotime($comment['date_added']))
				);
			}
		}
		
		$data['action'] = $this->url->link('extension/themability4blogger/bloginfo/themability_blogger|addcomment', 'themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
	
		if (isset($this->request->post['author'])) {
			if ($this->error) {
				$data['author'] = $this->request->post['author'];
			} else {
				$data['author'] = '';
			}
		} elseif ($this->customer->isLogged()) {
			$data['author'] = $this->customer->getFirstName();
		} else {
			$data['author'] = '';
		}

		if (isset($this->request->post['email'])) {
			if ($this->error) {
				$data['email'] = $this->request->post['email'];
			} else {
				$data['email'] = '';
			}
		} elseif ($this->customer->isLogged()) {
			$data['email'] = $this->customer->getEmail();
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['comment'])) {
			if ($this->error) {
				$data['comment'] = $this->request->post['comment'];
			} else {
				$data['comment'] = '';
			}
		} else {
			$data['comment'] = '';
		}
		
		// Captcha
		$this->load->model('setting/extension');
		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));
		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code']);
		} else {
			$data['captcha'] = '';
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', 'themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');	
		
		$this->response->setOutput($this->load->view('extension/themability4blogger/bloginfo/themability_blogger', $data));
	}else {
			$url = '';

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', 'themability_blogger_id=' . $this->request->get['themability_blogger_id'], true)
			);

			$this->document->setTitle($this->language->get('text_error'));
			$data['heading_title'] = $this->language->get('text_error');
			$data['text_error'] = $this->language->get('text_error');
			$data['button_continue'] = $this->language->get('button_continue');
			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	private function validate() {
		if ((Helper\Utf8\strlen(trim($this->request->post['author'])) < 1) || (Helper\Utf8\strlen(trim($this->request->post['author'])) > 32)) {
			$this->error['author'] = $this->language->get('error_author');
		}
		if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}
		if ((Helper\Utf8\strlen($this->request->post['comment']) < 10) || (Helper\Utf8\strlen($this->request->post['comment']) > 3000)) {
			$this->error['comment'] = $this->language->get('error_comment');
		}
		// Captcha		
		$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
		if ($captcha) {
			$this->error['captcha'] = $captcha;
		}
		return !$this->error;
	}


	public function addcomment(): void {

		$json = [];		

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		
		if ((Helper\Utf8\strlen(trim($this->request->post['author'])) < 1) || (Helper\Utf8\strlen(trim($this->request->post['author'])) > 32)) {
			$json['error']['author'] = $this->language->get('error_author');
		}
		if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$json['error']['email'] = $this->language->get('error_email');
		}
		if ((Helper\Utf8\strlen($this->request->post['comment']) < 10) || (Helper\Utf8\strlen($this->request->post['comment']) > 3000)) {
			$json['error']['comment'] = $this->language->get('error_comment');
		}

		// Captcha
		$this->load->model('setting/extension');
		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));
		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '|validate');
			if ($captcha) {
				$json['error']['captcha'] = $captcha;
			}
		}
		
		if (!$json) {
			$language = $this->model_extension_themability4blogger_module_themability_blogger->getLanguageByCode($this->session->data['language']);
			$json['success'] = $this->language->get('text_success');
			$this->model_extension_themability4blogger_module_themability_blogger->addComment($this->request->get['themability_blogger_id'], $this->request->post, $language['language_id']);

			$json['form_reset'] = "yes";

			if($this->session->data['email_send_status'] == 0){
				// $json['error'] = $this->language->get('email_fail');
				$json['error'] = $this->language->get('text_email_error');
				unset($this->session->data['email_send_status']);
			}
			if ($this->request->post['auto_approve']) {
				$json['success'] = $this->language->get('text_success');
			} else {
				$json['success'] = $this->language->get('text_approval');				
			}
			
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

	}

	public function blogs() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->load->model('tool/image');
		$total_blogs = 0;
		$total_pages = 0;
		$offset = 0;

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_blogs'] = $this->language->get('text_blogs');
		$data['text_no_blogs'] = $this->language->get('text_no_blogs');
		$data['text_read_more'] = $this->language->get('text_read_more');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_leave_comment'] = $this->language->get('text_leave_comment');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['column_title'] = $this->language->get('column_title');
		$data['button_view'] = $this->language->get('button_view');
		$data['blogs'] = array();
		

		### getAllBlogs ###		
		if (!isset ($_GET['page']) ) {  
			$pageno = 1;  
		} else {  
			$pageno = $_GET['page'];  
		}

		$no_of_records_per_page = $this->config->get('theme_bakery_blog_limit');

		$all_blogs = $this->model_extension_themability4blogger_module_themability_blogger->getAllBlogs($pageno,$no_of_records_per_page);		
		$data['all_blogs'] = array();
	
		foreach($all_blogs as $blog){			
			$get_setting = json_decode($blog['setting'],true);
			$setting = array();
			if(is_array($get_setting) && count($get_setting) > 0){
				$setting['width'] = $get_setting['width'];
				$setting['height'] = $get_setting['height'];
				$setting['char_limit'] = 180;
			}else{
				$setting['width'] = 100;
				$setting['height'] = 100;
				$setting['char_limit'] = 450;
			}
		
			$module_all_blog_data = array();
			$module_name = $blog['name'];
			$module_all_blog_data = array(
				'blog_id'       => $blog['themability_blogger_id'],
				'title'       => $blog['title'],
				'image' 	  => $this->model_tool_image->resize($blog['image'], $setting['width'], $setting['height']),
				'description' => Helper\Utf8\substr(strip_tags(html_entity_decode($blog['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['char_limit']) . '...',
				'date_added_date'  => date('j', strtotime($blog['date_added'])),
				'date_added_month'  => date('M', strtotime($blog['date_added'])),
				// 'date_added_year'  => date('Y', strtotime($blog['date_added'])),
				// 'date_added'  => date('j F, Y', strtotime($blog['date_added'])),
				'total_comments' => $blog['total'],				
				'href'        => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', 'themability_blogger_id=' . $blog['themability_blogger_id'], true)
			);
			
			$total_blogs = $blog['total_blogs'];
			$total_pages = $blog['total_pages'];
			$offset = $blog['offset'];
			
			$data['all_blogs'][] = array(
				'name'      => $module_name,
				'blog_data' => $module_all_blog_data
			);			
		}
	
		$page_link = $this->url->link('extension/themability4blogger/bloginfo/themability_blogger');
		$data['pagination'] = array();

		$data['pagination'] = array(
			'page_link' => $page_link,
			'total_blogs' => $total_blogs,
			'num_results_on_page' => $no_of_records_per_page,
			'total_pages' => $total_pages,
			'offset' => $offset,
			'page' => $pageno,
		);
		### .getAllBlogs ###		

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		return $data;
	}
}