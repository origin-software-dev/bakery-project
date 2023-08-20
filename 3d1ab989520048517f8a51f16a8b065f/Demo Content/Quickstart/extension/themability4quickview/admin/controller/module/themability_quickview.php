<?php
namespace Opencart\Admin\Controller\Extension\Themability4quickview\Module;
class ThemabilityQuickview extends \Opencart\System\Engine\Controller {	
	private $error = [];

	public function index1(): void {
		$this->load->language('extension/themability4quickview/module/themability_quickview');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');	
		$this->load->model('setting/module');
	}	

	public function index(): void {
		$this->load->language('extension/themability4quickview/module/themability_quickview');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/themability4quickview/module/themability_quickview', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/themability4quickview/module/themability_quickview|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_themability_quickview_status'] = $this->config->get('module_themability_quickview_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4quickview/module/themability_quickview', $data));
	}

	public function save(): void {
		$this->load->language('extension/themability4quickview/module/themability_quickview');

		if (!$this->user->hasPermission('modify', 'extension/themability4quickview/module/themability_quickview')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('module_themability_quickview', $this->request->post);			
			$data['success'] = $this->language->get('text_success');
		}
		
		$data['error'] = $this->error;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	public function install(): void {
		$this->load->model('user/user_group');
		$this->load->model('setting/setting');	

		//add permission
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'themability_quickview');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'themability_quickview');

		$setting['module_themability_search_status'] = 0;
		$this->model_setting_setting->editSetting('module_themability_quickview', $setting);

	}

	public function uninstall(): void {		
		$this->load->model('user/user_group');

		$setting['module_themability_search_status'] = 0;
		$this->model_setting_setting->editSetting('module_themability_quickview', $setting);

		// Remove permission
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'themability_quickview');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'themability_quickview');
	}
}
