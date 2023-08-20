<?php
namespace Opencart\Admin\Controller\Extension\Themability4blogger\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityBlogger extends \Opencart\System\Engine\Controller {	
	private $error = [];

	public function install(): void {
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->load->model('user/user_group');
		$this->load->model('setting/setting');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'themability_blogger');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'themability_blogger');

		$this->model_extension_themability4blogger_module_themability_blogger->createBlogs();

	}

	public function uninstall(): void {
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->load->model('user/user_group');
		$this->load->model('setting/setting');

		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'themability_blogger');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'themability_blogger');

		$this->model_extension_themability4blogger_module_themability_blogger->dropBlogs();
	}

	public function index():void {

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/module');
		

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_width'] = $this->language->get('text_width');
		$data['text_height'] = $this->language->get('text_height');
		$data['text_save_module'] = $this->language->get('text_save_module');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$this->language->get('themability_bloggerlist');

		if (!isset($this->request->get['module_id'])) {
			$data['themability_bloggerlist'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true);
			$data['text_view_list'] = sprintf($this->language->get('text_view_list'), $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$data['themability_bloggerlist'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['text_view_list'] = sprintf($this->language->get('text_view_list'), $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
		}

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image_size'] = $this->language->get('entry_image_size');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_allow_comments'] = $this->language->get('entry_allow_comments');
		$data['entry_login_required'] = $this->language->get('entry_login_required');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_char_limit'] = $this->language->get('entry_char_limit');
		$data['entry_auto_approve'] = $this->language->get('entry_auto_approve');

		$data['help_image'] = $this->language->get('help_image');
		$data['help_limit'] = $this->language->get('help_limit');
		$data['help_char_limit'] = $this->language->get('help_char_limit');
		$data['help_allow_comments'] = $this->language->get('help_allow_comments');
		$data['help_login_required'] = $this->language->get('help_login_required');
		$data['help_auto_approve'] = $this->language->get('help_auto_approve');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_comments'] = $this->language->get('column_comments');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_comments'] = $this->language->get('button_comments');
		$data['button_add_blog'] = $this->language->get('button_add_blog');
	
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'], true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/themability4blogger/module/themability_blogger|save', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/themability4blogger/module/themability_blogger|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true);

		if (!$this->request->get['module_id']) {
			$data['add_blog'] = '';
		} else {
			$data['add_blog'] = $this->url->link('extension/themability4blogger/module/themability_blogger|add', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->get['module_id'])) {
			$data['module_id'] = $this->request->get['module_id'];
		} elseif (!empty($module_info)) {
			$data['module_id'] = $module_info['module_id'];
		} else {
			$data['module_id'] = '';
		}

		if (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 80;
		}

		if (!empty($module_info)) {
			$data['banner_height'] = $module_info['banner_height'];
		} else {
			$data['banner_height'] = 80;
		}


		if (!empty($module_info)) {
			$data['banner_width'] = $module_info['banner_width'];
		} else {
			$data['banner_width'] = 80;
		}

		if (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 80;
		}

		if (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (!empty($module_info)) {
			$data['char_limit'] = $module_info['char_limit'];
		} else {
			$data['char_limit'] = 50;
		}

		if (!empty($module_info)) {
			$data['comments'] = $module_info['comments'];
		} else {
			$data['comments'] = '';
		}

		if (!empty($module_info)) {
			$data['login'] = $module_info['login'];
		} else {
			$data['login'] = '';
		}

		if (!empty($module_info)) {
			$data['auto_approve'] = $module_info['auto_approve'];
		} else {
			$data['auto_approve'] = '';
		}

		$data['themability_blogger_entries'] = array();

		if (!isset($this->request->get['module_id'])) {
			$results = array();
		} else {
			$results = $this->model_extension_themability4blogger_module_themability_blogger->getBlogsByModule($this->request->get['module_id']);
		}

		if ($results) {
			foreach ($results as $result) {
				$total_comments = $this->model_extension_themability4blogger_module_themability_blogger->getTotalBlogComments($result['themability_blogger_id']);

				$data['themability_blogger_entries'][] = array(
					'themability_blogger_id'     => $result['themability_blogger_id'],
					'title'          => $result['title'],
					'module_id'      => $result['module_id'],
					'total_comments' => number_format($total_comments),
					'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'comments'       => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $result['themability_blogger_id'] . '&module_id=' . $this->request->get['module_id'], true),
					'edit'           => $this->url->link('extension/themability4blogger/module/themability_blogger|edit', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $result['themability_blogger_id'] . '&module_id=' . $this->request->get['module_id'], true),
					'delete'         => $this->url->link('extension/themability4blogger/module/themability_blogger|delete', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $result['themability_blogger_id'] . '&module_id=' . $this->request->get['module_id'], true)
				);
			}
		}
		
		$data['entry_blogpage_items'] = sprintf($this->language->get('entry_blogpage_items'), $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=theme', true));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4blogger/module/themability_blogger', $data));
	}	

	public function add() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');	

		$this->getForm();
	}
	
	public function add_blog(): void {
		$json = [];	

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$json['error'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['themability_blogger_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen($value['title']) < 3) || (Helper\Utf8\strlen($value['title']) > 64)) {				
				$json['error']['blog-title-'.$language_id] = $this->language->get('error_title');
			}
			if (Helper\Utf8\strlen($value['description']) < 3) {
				$json['error']['blog-description-'.$language_id] = $this->language->get('error_description');
			}
		}

		if (!$this->request->post['image']) {
			$json['error']['image'] = $this->language->get('error_image');
		}

		// if (!$this->request->post['banner_image']) {
		// 	$json['error']['banner-image'] = $this->language->get('error_banner_image');
		// }

		if (!$json) {
			$this->load->model('setting/module');							
			$this->model_extension_themability4blogger_module_themability_blogger->addBlog($this->request->post, $this->request->post['module_id']);
			$url = '';
			if (isset($this->request->get['module_id'])) {
				$url .= '&module_id=' . $this->request->get['module_id'];
			}
			if (isset($this->request->post['module_id'])) {
				$url .= '&module_id=' . $this->request->post['module_id'];
			}
			$json['success'] = $this->language->get('text_success');				
			$json['redirect'] =$this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . $url, true);
		}else{
			$json['error']['warning']= $this->language->get('error_warning_item');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->getForm();
	}

	public function edit_blog(): void {	
		$json = [];		

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$json['error'] = $this->language->get('error_permission');
		}
 
		foreach ($this->request->post['themability_blogger_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen($value['title']) < 3) || (Helper\Utf8\strlen($value['title']) > 64)) {				
				$json['error']['blog-title-'.$language_id] = $this->language->get('error_title');
			}
			if (Helper\Utf8\strlen($value['description']) < 3) {
				$json['error']['blog-description-'.$language_id] = $this->language->get('error_description');
			}
		}

		if (!$this->request->post['image']) {
			$json['error']['image'] = $this->language->get('error_image');
		}

		// if (!$this->request->post['banner_image']) {
		// 	$json['error']['banner-image'] = $this->language->get('error_banner_image');
		// }

		if (!$json) {
			$this->load->model('setting/module');
			$this->model_extension_themability4blogger_module_themability_blogger->editBlog($this->request->get['themability_blogger_id'], $this->request->post);
			$json['success'] = $this->language->get('text_success');			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete():void {
		$json = [];	

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$json['error'] = $this->language->get('error_permission');
		}		
		
		if (!$json) {
			if (isset($this->request->post['page_name']) && $this->request->post['page_name'] == "blog_list_page") {
				
				if (isset($this->request->post['selected']) && count($this->request->post['selected']) > 0) {
					foreach ($this->request->post['selected'] as $themability_blogger_id) {
						$this->model_extension_themability4blogger_module_themability_blogger->deleteBlog($themability_blogger_id);
					}
					$json['success'] = $this->language->get('text_success');
				}else{
					$json['error'] = $this->language->get('error_module_select');
				}
				$url = '';
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$json['redirect'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . $url, true);

			} elseif ((isset($this->request->get['themability_blogger_id']) && isset($this->request->get['delete']))) {
				
				$this->model_extension_themability4blogger_module_themability_blogger->deleteBlog($this->request->get['themability_blogger_id']);
			
				$url = '';
				if (isset($this->request->get['module_id'])) {
					$url .= '&module_id=' . $this->request->get['module_id'];
				}
				$this->session->data['success'] = $this->language->get('text_success');
				$json['success'] = $this->language->get('text_success');				
				$json['redirect'] =$this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . $url, true);
			}else{			
				$this->model_extension_themability4blogger_module_themability_blogger->deleteBlog($this->request->get['themability_blogger_id']);
				$url = '';
				if (isset($this->request->get['module_id'])) {
					$url .= '&module_id=' . $this->request->get['module_id'];
				}
				$json['success'] = $this->language->get('text_success');
				$this->session->data['success'] = $this->language->get('text_success');
				$json['redirect'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . $url, true);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['themability_blogger_id']) ? $this->language->get('text_add') : $this->language->get('text_form');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_unallocated'] = $this->language->get('text_unallocated');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_module'] = $this->language->get('entry_module');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['module'])) {
			$data['error_module'] = $this->error['module'];
		} else {
			$data['error_module'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			];
		}
		
		if (!isset($this->request->get['themability_blogger_id'])) {
			$data['action'] = $this->url->link('extension/themability4blogger/module/themability_blogger|add_blog', 'user_token=' . $this->session->data['user_token'] , true);
		} else {
			$data['action'] = $this->url->link('extension/themability4blogger/module/themability_blogger|edit_blog', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		}

		if (isset($this->request->get['themability_blogger_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$themability_blogger_info = $this->model_extension_themability4blogger_module_themability_blogger->getBlog($this->request->get['themability_blogger_id']);
		}

		if (isset($this->request->get['module_id'])) {
			$data['cancel'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		} elseif (!empty($themability_blogger_info)) {
			$data['cancel'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $themability_blogger_info['module_id'], true);
		} else {
			$data['cancel'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token']. '&type=module', true);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['themability_blogger_id'])) {
			$data['themability_blogger_description'] = $this->model_extension_themability4blogger_module_themability_blogger->getBlogDescriptions($this->request->get['themability_blogger_id']);
		} else {
			$data['themability_blogger_description'] = array();
		}

		if (!empty($themability_blogger_info)) {
			$data['status'] = $themability_blogger_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->get['themability_blogger_id']) && !empty($themability_blogger_info)) {
			$data['module_id'] = $themability_blogger_info['module_id'];
		} elseif (isset($this->request->get['module_id'])) {
			$data['module_id'] = $this->request->get['module_id'];
		} else {
			$data['module_id'] = '';
		}

		if (!empty($themability_blogger_info)) {
			$data['image'] = $themability_blogger_info['image'];
		} else {
			$data['image'] = '';
		}

		if (!empty($themability_blogger_info)) {
			$data['banner_image'] = $themability_blogger_info['banner_image'];
		} else {
			$data['banner_image'] = '';
		}

		$this->load->model('tool/image');

		if (!empty($themability_blogger_info) && is_file(DIR_IMAGE . $themability_blogger_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($themability_blogger_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		if (!empty($themability_blogger_info) && is_file(DIR_IMAGE . $themability_blogger_info['banner_image'])) {
			$data['banner_image_thumb'] = $this->model_tool_image->resize($themability_blogger_info['banner_image'], 100, 100);
		} else {
			$data['banner_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['banner_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->load->model('setting/module');

		$data['themability_blogger_modules'] = array();

		$modules = $this->model_setting_module->getModulesByCode('themability4blogger.themability_blogger');

		foreach ($modules as $module) {
			if (!isset($data['themability_blogger_modules'][$module['code']])) {
				$data['themability_blogger_modules'][$module['code']]['name'] = $this->language->get('heading_title');
			}

			$data['themability_blogger_modules'][$module['code']]['module'][] = array(
				'module_id' => $module['module_id'],
				'name'      => $module['name']
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/themability4blogger/module/themability_blogger_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['themability_blogger_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen($value['title']) < 3) || (Helper\Utf8\strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (Helper\Utf8\strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		if (!$this->request->post['module_id']) {
			$this->error['module'] = $this->language->get('error_module');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function view_list() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->getlist();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'b.module_id, b.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'], true);
			$data['add'] = $this->url->link('extension/themability4blogger/module/themability_blogger|add', 'user_token=' . $this->session->data['user_token'], true);
			$data['delete'] = $this->url->link('extension/themability4blogger/module/themability_blogger|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['add'] = $this->url->link('extension/themability4blogger/module/themability_blogger|add', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['delete'] = $this->url->link('extension/themability4blogger/module/themability_blogger|delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}

		$data['themability_bloggers'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		);

		$themability_blogger_total = $this->model_extension_themability4blogger_module_themability_blogger->getTotalBlogs();

		$results = $this->model_extension_themability4blogger_module_themability_blogger->getBlogs($filter_data);

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		foreach ($results as $result) {
			$module_info = $this->model_extension_themability4blogger_module_themability_blogger->getModule($result['module_id']);

			if ($module_info) {
				$module = $module_info['name'];
			} else {
				$module = $this->language->get('text_unallocated');
			}

			$total_comments = $this->model_extension_themability4blogger_module_themability_blogger->getTotalBlogComments($result['themability_blogger_id']);

			$data['themability_bloggers'][] = array(
				'themability_blogger_id'     => $result['themability_blogger_id'],
				'title'          => $result['title'],
				'module'         => $module,
				'total_comments' => number_format($total_comments),
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'comments'       => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $result['themability_blogger_id'] . $url, true),
				'edit'           => $this->url->link('extension/themability4blogger/module/themability_blogger|edit', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $result['themability_blogger_id'], true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_module'] = $this->language->get('column_module');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_comments'] = $this->language->get('column_comments');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_return'] = $this->language->get('button_return');
		$data['button_comments'] = $this->language->get('button_comments');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['sort_title'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=bd.title' . $url, true);
			$data['sort_module'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.module_id' . $url, true);
			$data['sort_date_added'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.date_added' . $url, true);
		} else {
			$data['sort_title'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=bd.title' . '&module_id=' . $this->request->get['module_id'] . $url, true);
			$data['sort_module'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.module_id' . '&module_id=' . $this->request->get['module_id'] . $url, true);
			$data['sort_date_added'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.date_added' . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}		

		if (!isset($this->request->get['module_id'])) {
			$pagination_url = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		} else {
			$pagination_url = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url . '&page={page}', true);
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $themability_blogger_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),			
			'url'   => $pagination_url
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($themability_blogger_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($themability_blogger_total - $this->config->get('config_pagination_admin'))) ? $themability_blogger_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $themability_blogger_total, ceil($themability_blogger_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4blogger/module/themability_blogger_list', $data));
	}

	public function comments() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->getComments();
	}

	public function getComments() {
		if (!isset($this->request->get['themability_blogger_id'])) {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true));
		}

		$blog_info = $this->model_extension_themability4blogger_module_themability_blogger->getBlog($this->request->get['themability_blogger_id']);
		$blog_title = $this->model_extension_themability4blogger_module_themability_blogger->getBlogTitle($this->request->get['themability_blogger_id']);

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])){
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true)
			];

			$data['breadcrumbs'][] = [
				'text' => $blog_title,
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			];

			$data['breadcrumbs'][] = [
				'text' => $blog_title,
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])){
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true);
			$data['delete'] = $this->url->link('extension/themability4blogger/module/themability_blogger|delete_comment', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true);
		} else {
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['delete'] = $this->url->link('extension/themability4blogger/module/themability_blogger|delete_comment', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true);
		}

		$data['blog_comments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		);

		$blog_comment_total = $this->model_extension_themability4blogger_module_themability_blogger->getTotalBlogComments($this->request->get['themability_blogger_id']);

		$results = $this->model_extension_themability4blogger_module_themability_blogger->getBlogComments($this->request->get['themability_blogger_id'], $filter_data);

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		foreach ($results as $result) {
			$data['blog_comments'][] = array(
				'themability_blogger_comment_id' => $result['themability_blogger_comment_id'],
				'author'             => $result['author'],
				'email'              => $result['email'],
				'approved'           => ($result['approved'] ? $this->language->get('text_approved') : $this->language->get('text_unapproved')),
				'date_added'         => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'is_approved'        => $result['approved'],
				'approve'            => $this->url->link('extension/themability4blogger/module/themability_blogger|approve_comment', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . '&themability_blogger_comment_id=' . $result['themability_blogger_comment_id'] . $url, true),
				'disapprove'         => $this->url->link('extension/themability4blogger/module/themability_blogger|disapprove_comment', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . '&themability_blogger_comment_id=' . $result['themability_blogger_comment_id'] . $url, true),
				'view'               => $this->url->link('extension/themability4blogger/module/themability_blogger|view_comment', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . '&themability_blogger_comment_id=' . $result['themability_blogger_comment_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_author'] = $this->language->get('column_author');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_list'] = $this->language->get('button_list');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_approve'] = $this->language->get('button_approve');
		$data['button_disapprove'] = $this->language->get('button_disapprove');
		$data['button_view'] = $this->language->get('button_view');
		if (!isset($this->request->get['module_id'])) {
			$data['button_return'] = $this->language->get('button_list');
		} else {
			$data['button_return'] = $this->language->get('button_return');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['sort_author'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&sort=author' . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
			$data['sort_date_added'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		} else {
			$data['sort_author'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&sort=author' . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
			$data['sort_date_added'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&sort=date_added' . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (!isset($this->request->get['module_id'])) {
			$pagination_url = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . '&page={page}', true);
		} else {
			$pagination_url = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . '&page={page}', true);
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $blog_comment_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $pagination_url
		]);


		$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_comment_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($blog_comment_total - $this->config->get('config_pagination_admin'))) ? $blog_comment_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $blog_comment_total, ceil($blog_comment_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4blogger/module/themability_blogger_comments', $data));
	}

	public function approve_comment() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		$this->model_extension_themability4blogger_module_themability_blogger->approveComment($this->request->get['themability_blogger_comment_id']);

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (!isset($this->request->get['module_id'])) {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true));
		} else {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true));
		}
	}

	public function disapprove_comment() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		$this->model_extension_themability4blogger_module_themability_blogger->disapproveComment($this->request->get['themability_blogger_comment_id']);

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (!isset($this->request->get['module_id'])) {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true));
		} else {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true));
		}
	}

	public function delete_comment() {
		$json = [];	

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		
		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $themability_blogger_comment_id) {
					$this->model_extension_themability4blogger_module_themability_blogger->deleteBlogComment($themability_blogger_comment_id);
				}
				$url = '';
				if (isset($this->request->get['module_id'])) {
					$module_id = $this->request->get['module_id'];
					$url .= '&module_id='.$module_id;
				}
				if (isset($this->request->get['themability_blogger_id'])) {
					$themability_blogger_id = $this->request->get['themability_blogger_id'];
					$url .= '&themability_blogger_id='.$themability_blogger_id;
				}			
				$json['success'] = $this->language->get('text_success');
				$redirect = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token']. $url, true);
				$json['redirect'] = $redirect;
			}else{
				$json['error'] = $this->language->get('error_module_select');
			}			
		}
		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

	public function view_comment() {
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->viewComment();
	}

	protected function viewComment() {
		if (!isset($this->request->get['themability_blogger_id'])) {
			$this->response->redirect($this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true));
		}

		$blog_title = $this->model_extension_themability4blogger_module_themability_blogger->getBlogTitle($this->request->get['themability_blogger_id']);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_author'] = $this->language->get('text_author');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_comment'] = $this->language->get('text_comment');

		$data['button_back'] = $this->language->get('button_back');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']. '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])){
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'], true)
			];

			$data['breadcrumbs'][] = [
				'text' => $blog_title,
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			];

			$data['breadcrumbs'][] = [
				'text' => $blog_title,
				'href' => $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'] . $url, true)
			];
		}

		if (!isset($this->request->get['module_id'])){
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		} else {
			$data['return'] = $this->url->link('extension/themability4blogger/module/themability_blogger|comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&themability_blogger_id=' . $this->request->get['themability_blogger_id'], true);
		}

		$comment_info = $this->model_extension_themability4blogger_module_themability_blogger->getBlogComment($this->request->get['themability_blogger_comment_id']);

		$comment_descriptions = $this->model_extension_themability4blogger_module_themability_blogger->getBlogCommentDescriptions($this->request->get['themability_blogger_comment_id']);

		$data['author'] = $comment_info['author'];
		$data['email'] = $comment_info['email'];
		$data['date_added'] = date($this->language->get('date_format_short'), strtotime($comment_info['date_added']));
		$data['comment_descriptions'] = $comment_descriptions;

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4blogger/module/themability_blogger_view_comment', $data));
	}

	public function save(): void {	
		$json = [];		

		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');

		if (!$this->user->hasPermission('modify', 'extension/themability4blogger/module/themability_blogger')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if ((Helper\Utf8\strlen($this->request->post['name']) < 3) || (Helper\Utf8\strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}
	
		if (!$this->request->post['width']) {
			$json['error']['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$json['error']['height'] = $this->language->get('error_height');
		}
		
		if (!$this->request->post['banner_width']) {
			$json['error']['banner_width'] = $this->language->get('error_banner_width');
		}

		if (!$this->request->post['banner_height']) {
			$json['error']['banner_height'] = $this->language->get('error_banner_height');
		}		

		if (!$json) {
			$this->load->model('setting/module');
			if (!isset($this->request->get['module_id'])) {
				$module_id = $this->model_setting_module->addModule('themability4blogger.themability_blogger', $this->request->post);
				$redirect = $this->url->link('extension/themability4blogger/module/themability_blogger', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $module_id, true);
				$json['redirect'] = $redirect;		
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}			
			$json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}

}