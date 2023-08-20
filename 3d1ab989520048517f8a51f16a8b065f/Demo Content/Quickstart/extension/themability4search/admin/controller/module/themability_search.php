<?php
namespace Opencart\Admin\Controller\Extension\Themability4search\Module;
class ThemabilitySearch extends \Opencart\System\Engine\Controller {
	private $error = [];
	
	public function index(): void {
		$this->load->language('extension/themability4search/module/themability_search');
		
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/themability4search/module/themability_search', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/themability4search/module/themability_search|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
						
		if (isset($this->request->post['module_themability_search_status'])) {
			$data['module_themability_search_status'] = $this->request->post['module_themability_search_status'];
		} else {
			$data['module_themability_search_status'] = $this->config->get('module_themability_search_status');
		}

		if (isset($this->request->post['module_themability_search_autocomplete'])) {
			$data['module_themability_search_autocomplete'] = $this->request->post['module_themability_search_autocomplete'];
		} else {
			$data['module_themability_search_autocomplete'] = $this->config->get('module_themability_search_autocomplete');
		}

		if (isset($this->request->post['module_themability_search_category'])) {
			$data['module_themability_search_category'] = $this->request->post['module_themability_search_category'];
		} else {
			$data['module_themability_search_category'] = $this->config->get('module_themability_search_category');
		}
		
		// Setting 		
		$_config = new \Opencart\System\Engine\Config();
		$_config->addPath(DIR_EXTENSION . 'themability4search/system/config/');
		$_config->load('themability_search');
		
		$data['setting'] = $_config->get('themability_search_setting');
		
		if (isset($this->request->post['module_themability_search_setting'])) {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->request->post['module_themability_search_setting']);
		} else {
			$data['setting'] = array_replace_recursive((array)$data['setting'], (array)$this->config->get('module_themability_search_setting'));
		}
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4search/module/themability_search', $data));
	}
	
	public function save(): void {				
		if (!$this->user->hasPermission('modify', 'extension/themability4search/module/themability_search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_themability_search', $this->request->post);			
			$data['success'] = $this->language->get('success_save');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function install(): void {
		$this->load->model('setting/setting');
		
		$setting['module_themability_search_status'] = 0;
		$setting['module_themability_search_autocomplete'] = 0;
		$setting['module_themability_search_category'] = 0;
		
		$this->model_setting_setting->editSetting('module_themability_search', $setting);
	}
}
