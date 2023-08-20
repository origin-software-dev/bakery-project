<?php
namespace Opencart\Admin\Controller\Extension\Themability4testimonial\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityTestimonial extends \Opencart\System\Engine\Controller {	
	private $error = [];

	public function install(): void {
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');		
		$this->load->model('user/user_group');

		// Add Permission
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/themability4testimonial/catalog/themability_testimonial');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/themability4testimonial/catalog/themability_testimonial');

		$this->model_extension_themability4testimonial_module_themability_testimonial->install();
	}

	public function uninstall() : void {
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('user/user_group');

		// Remove permission
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/themability4testimonial/catalog/themability_testimonial');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/themability4testimonial/catalog/themability_testimonial');

		$this->model_extension_themability4testimonial_module_themability_testimonial->uninstall();
	}

	public function index(): void {	
		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('setting/module');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');		
	
		$this->load->model('localisation/language');
		$data['languages'] = array();
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $language){
			if ($language['status']) {
				$data['languages'][] = array(
					'name'  => $language['name'],
					'language_id' => $language['language_id'],
					'code' => $language['code'],
					'image' => $language['image']
				);
			}
		}		

		$data['themability_testimoniallist'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'], true);
		if (isset($this->request->get['module_id'])) {
			$data['themability_testimoniallist'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true);
		}else{
			$data['themability_testimoniallist'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token'], true);
		}

		if (isset($this->request->get['module_id'])) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);			
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 150;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 150;
		}

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 10;
		}

		if (isset($this->request->post['items'])) {
			$data['items'] = $this->request->post['items'];
		} elseif (!empty($module_info)) {
			$data['items'] = $module_info['items'];
		} else {
			$data['items'] = 1;
		}

		if (isset($this->request->post['auto'])) {
			$data['auto'] = $this->request->post['auto'];
		} elseif (!empty($module_info)) {
			$data['auto'] = $module_info['auto'];
		} else {
			$data['auto'] = 1;
		}

		if (isset($this->request->post['speed'])) {
			$data['speed'] = $this->request->post['speed'];
		} elseif (!empty($module_info)) {
			$data['speed'] = $module_info['speed'];
		} else {
			$data['speed'] = 3000;
		}

		if (isset($this->request->post['navigation'])) {
			$data['navigation'] = $this->request->post['navigation'];
		} elseif (!empty($module_info)) {
			$data['navigation'] = $module_info['navigation'];
		} else {
			$data['navigation'] = 1;
		}

		if (isset($this->request->post['pagination'])) {
			$data['pagination'] = $this->request->post['pagination'];
		} elseif (!empty($module_info)) {
			$data['pagination'] = $module_info['pagination'];
		} else {
			$data['pagination'] = 0;
		}

		$this->load->model('tool/image');
		if (isset($module_info['testimonial_background_image'])) {
            $data['testimonial_background_image'] = $module_info['testimonial_background_image'];
        } else {
            $data['testimonial_background_image'] = '';
        }

		if (!empty($module_info['testimonial_background_image']) && is_file(DIR_IMAGE . $module_info['testimonial_background_image'])) {
			$data['testimonial_background_image_thumb'] = $this->model_tool_image->resize($module_info['testimonial_background_image'], 100, 100);
		} else {
			$data['testimonial_background_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['testimonial_background_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($module_info) && isset($module_info['title'])) {
			$data['title'] = $module_info['title'];
		} else {
			$data['title'] = array();
		}	

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'], true)
			];
		}else{
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		if (isset($this->request->get['module_id'])) {			
			$data['action'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}else{
			$data['action'] = $this->url->link('extension/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4testimonial/module/themability_testimonial', $data));
	}

	public function list(): void {
		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->response->setOutput($this->getList());
	}	

	public function getList(): string {	

			
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');	
		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));		

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
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

		$data['themability_testimonials'] = array();

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start'	=> ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'	=> $this->config->get('config_pagination_admin')
		];

		$themability_testimonial_total = $this->model_extension_themability4testimonial_module_themability_testimonial->getTotalThemabilityTestimonials($filter_data);
		$results = $this->model_extension_themability4testimonial_module_themability_testimonial->getThemabilityTestimonials($filter_data);

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'], true)
			);
		}else{
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			);
		}
	
		$data['insert'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|delete', 'user_token=' . $this->session->data['user_token'] . $url);

		if(isset($this->request->get['module_id'])){
			$module_id_url = '&module_id=' . $this->request->get['module_id'];
		}else{
			$module_id_url = '';
		}

		foreach ($results as $result) {
			$action = array();

			if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
				$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}


			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial|update', 'user_token=' . $this->session->data['user_token'] . '&themability_testimonial_id=' . $result['themability_testimonial_id'].$module_id_url, true)
			);
			$data['themability_testimonials'][] = array(
				'themability_testimonial_id' => $result['themability_testimonial_id'],
				'image'      	 => $image,
				'customer_name'  => $result['customer_name'],
				'sort_order'     => $result['sort_order'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'       => isset($this->request->post['selected']) && in_array($result['themability_testimonial_id'], $this->request->post['selected']),
				'action'         => $action
			);
		}

		$data['heading_title'] = $this->language->get('heading_title_item');

		$data['entry_name'] = $this->language->get('entry_name');

		$data['text_list'] = $this->language->get('text_list_item');
		$data['text_enabled'] = $this->language->get('text_enabled_item');
		$data['text_disabled'] = $this->language->get('text_disabled_item');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_name'] = $this->language->get('column_name_item');
		$data['column_sort_order'] = $this->language->get('column_sort_order_item');
		$data['column_status'] = $this->language->get('column_status_item');
		$data['column_action'] = $this->language->get('column_action_item');

		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_delete'] = $this->language->get('button_delete');

		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		} else {
			$url .= '';
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

	
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $themability_testimonial_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($themability_testimonial_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($themability_testimonial_total - $this->config->get('config_pagination_admin'))) ? $themability_testimonial_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $themability_testimonial_total, ceil($themability_testimonial_total / $this->config->get('config_pagination_admin')));

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

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		return $this->load->view('extension/themability4testimonial/module/themability_testimonial_list', $data);
	
	}

	public function insert() {
		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');		
		$this->getForm();
	}

	public function insert_testimonial():void {
		$json = [];		

		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');		

		if (!$this->user->hasPermission('modify', 'extension/themability4testimonial/module/themability_testimonial')) {
			$json['error'] = $this->language->get('error_permission');
		}		

		foreach ($this->request->post['themability_testimonial_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen($value['customer_name']) < 1) || (Helper\Utf8\strlen($value['customer_name']) > 255)) {
				$json['error']['customer-name-'.$language_id] = $this->language->get('error_name_item');
			}

			if (Helper\Utf8\strlen($value['content']) < 3) {
				$json['error']['customer-content-'.$language_id]= $this->language->get('error_description_item');				
			}			
		}
		if (!$json) {
			$this->load->model('setting/module');	

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['module_id'])) {
				$url .= '&module_id=' . $this->request->get['module_id'];
			}	
			
			$themability_testimonial_id = $this->model_extension_themability4testimonial_module_themability_testimonial->addThemabilityTestimonial($this->request->post);
			
			$url = '';
			if ($themability_testimonial_id) {
				$url .= '&themability_testimonial_id=' . $themability_testimonial_id;
			}

			$redirect = $this->url->link('extension/themability4testimonial/module/themability_testimonial|update', 'user_token=' . $this->session->data['user_token']. $url, true);
			$json['redirect'] = $redirect;

			$json['success'] = $this->language->get('text_success');
		}else{
			$json['error']['warning']= $this->language->get('error_warning_item');				

		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));			
	}

	public function update() {
		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');
		$this->getForm();
	}


	public function delete(): void {
		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/themability4testimonial/module/themability_testimonial')) {
			$json['warning']= $this->language->get('error_permission');
		}

		if (!$json) {
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $themability_testimonial_id) {
					$this->model_extension_themability4testimonial_module_themability_testimonial->deleteThemabilityTestimonial($themability_testimonial_id);
				}		
				$json['success'] = $this->language->get('text_success');				
				$json['redirect'] =  $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token'], true);
			}else{
				$json['warning'] = $this->language->get('error_warning');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
	
	public function getForm() {		
		$this->document->setTitle($this->language->get('heading_title_item'));

		$data['heading_title'] = $this->language->get('heading_title_item');

		$data['text_form'] = !isset($this->request->get['themability_testimonial_id']) ? $this->language->get('text_add_item') : $this->language->get('text_edit_item');
		$data['text_enabled'] = $this->language->get('text_enabled_item');
		$data['text_disabled'] = $this->language->get('text_disabled_item');

		$data['entry_image'] = $this->language->get('entry_image_item');
		$data['entry_name'] = $this->language->get('entry_name_item');
		$data['entry_description'] = $this->language->get('entry_description_item');
		$data['entry_status'] = $this->language->get('entry_status_item');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order_item');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['customer_name'])) {
			$data['error_name'] = $this->error['customer_name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['content'])) {
			$data['error_description'] = $this->error['content'];
		} else {
			$data['error_description'] = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_list'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token'], true)
			);
		}else{
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_list'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token'], true)
			);
		}else{
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			);
		}


		if (!isset($this->request->get['themability_testimonial_id'])) {
			$data['action'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|insert_testimonial', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|update_testimonial', 'user_token=' . $this->session->data['user_token'] . '&themability_testimonial_id=' . $this->request->get['themability_testimonial_id'], true);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['cancel'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token'], true);
		}else{
			$data['cancel'] = $this->url->link('extension/themability4testimonial/module/themability_testimonial|list', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true);
		}	


		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['themability_testimonial_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$themability_testimonial_info = $this->model_extension_themability4testimonial_module_themability_testimonial->getThemabilityTestimonial($this->request->get['themability_testimonial_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['themability_testimonial_description'])) {
			$data['themability_testimonial_description'] = $this->request->post['themability_testimonial_description'];
		} elseif (isset($this->request->get['themability_testimonial_id'])) {			
			$data['themability_testimonial_description'] = $this->model_extension_themability4testimonial_module_themability_testimonial->getThemabilityTestimonialDescriptions($this->request->get['themability_testimonial_id']);
		} else {
			$data['themability_testimonial_description'] = array();
		}

		if (isset($this->request->post['sort_order'])) {			
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (isset($themability_testimonial_info) && !empty($themability_testimonial_info)) {			
			$data['sort_order'] = $themability_testimonial_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else if (isset($themability_testimonial_info)&& !empty($themability_testimonial_info)) {
			$data['status'] = $themability_testimonial_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif ( isset($themability_testimonial_info) && !empty($themability_testimonial_info)) {
			$data['image'] = $themability_testimonial_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($themability_testimonial_info) && is_file(DIR_IMAGE . $themability_testimonial_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($themability_testimonial_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		$this->response->setOutput($this->load->view('extension/themability4testimonial/module/themability_testimonial_form', $data));
	}	

	public function update_testimonial(): void {
		$json = [];		

		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');

		if (!$this->user->hasPermission('modify', 'extension/themability4testimonial/module/themability_testimonial')) {
			$json['error'] = $this->language->get('error_permission');
		}		

		foreach ($this->request->post['themability_testimonial_description'] as $language_id => $value) {
			if ((Helper\Utf8\strlen($value['customer_name']) < 1) || (Helper\Utf8\strlen($value['customer_name']) > 255)) {
				$json['error']['customer-name-'.$language_id] = $this->language->get('error_name_item');
			}
			if (Helper\Utf8\strlen($value['content']) < 3) {
				$json['error']['customer-content-'.$language_id]= $this->language->get('error_description_item');				
			}			
		}


		if (!$json) {
			$this->load->model('setting/module');

			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['module_id'])) {
				$url .= '&module_id=' . $this->request->get['module_id'];
			}
			if (isset($this->request->get['themability_testimonial_id'])) {
				$url .= '&themability_testimonial_id=' . $this->request->get['themability_testimonial_id'];
			}

			$this->model_extension_themability4testimonial_module_themability_testimonial->editThemabilityTestimonial($this->request->get['themability_testimonial_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_item');		

			$json['success'] = $this->language->get('text_success_item');
        }else{
			$json['error']['warning']= $this->language->get('error_warning_item');
		}

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}

	public function save(): void {
		$json = [];		

		$this->load->language('extension/themability4testimonial/module/themability_testimonial');
		$this->load->model('extension/themability4testimonial/module/themability_testimonial');

		if (!$this->user->hasPermission('modify', 'extension/themability4testimonial/module/themability_testimonial')) {
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

		if (!$this->request->post['items']) {
			$json['error']['items'] = $this->language->get('error_items');
		}
		if (!$this->request->post['limit']) {
			$json['error']['limit'] = $this->language->get('error_limit');
		}
		if (!$this->request->post['speed']) {
			$json['error']['speed'] = $this->language->get('error_speed');
		}

		if (!$this->request->post['testimonial_background_image']) {
			$json['error']['testimonial-background-image'] = $this->language->get('error_testimonial_background_image');
		}

		if (!$json) {
			$this->load->model('setting/module');
			if (!isset($this->request->get['module_id'])) {
				$module_id = $this->model_setting_module->addModule('themability4testimonial.themability_testimonial', $this->request->post);
				$redirect = $this->url->link('extension/themability4testimonial/module/themability_testimonial', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $module_id, true);
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
?>