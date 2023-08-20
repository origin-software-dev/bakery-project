<?php
namespace Opencart\Admin\Controller\Extension\Themability4newsletter\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityNewsletter extends \Opencart\System\Engine\Controller {	
	private $error = [];

	public function index(): void {
		$this->load->language('extension/themability4newsletter/module/themability_newsletter');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');	
		$this->load->model('setting/module');
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');

		$this->load->model('localisation/language');
		$data['languages'] = [];
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language){
			if ($language['status']) {
				$data['languages'][] = array(
					'name'  => $language['name'],
					'language_id' => $language['language_id'],
					'code' => $language['code']
				);
			}
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_popup'] = $this->language->get('entry_popup');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4newsletter/module/themability_newsletter', 'user_token=' . $this->session->data['user_token'], true)
			);
		}else{
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4newsletter/module/themability_newsletter', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			);
		}
			
		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter|save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
	

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (!empty($module_info['name'])) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (!empty($module_info['popup'])) {
			$data['popup'] = $module_info['popup'];
		} else {
			$data['popup'] = '';
		}		

		if (!empty($module_info['status'])) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}		
		
		if (!empty($module_info['newsletter_image'])) {
			$data['newsletter_image'] = $module_info['newsletter_image'];
		} else {
			$data['newsletter_image'] = '';
		}

		$this->load->model('tool/image');
		if (!empty($module_info['newsletter_image']) && is_file(DIR_IMAGE . $module_info['newsletter_image'])) {
			$data['newsletter_image_thumb'] = $this->model_tool_image->resize($module_info['newsletter_image'], 100, 100);
		} else {
			$data['newsletter_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['newsletter_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);		

		//Get Subscriber List
		$user_total = $this->model_extension_themability4newsletter_module_themability_newsletter->getTotalUsers();
		
		if (isset($this->request->get['page'])) {			
			$page = $this->request->get['page'];			
		} else {			
			$page = 1;			
		}
		
		$data['page_nav'] = ($page-1) * $this->config->get('config_pagination_admin');
		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['pages'] = array(
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')		
		);
		
		$results = $this->model_extension_themability4newsletter_module_themability_newsletter->getList($data);
		
		$data['users'] = [];
		foreach ($results as $result) {
		 	$action = [];
			if(isset($this->request->get['module_id'])){
				$action[] = array(
					'text' => $this->language->get('text_delete'),
					'href' => $this->url->link('extension/themability4newsletter/module/themability_newsletter|unsubscribe', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $result['id'] . '&module_id=' . $this->request->get['module_id'] . $url, true)
				);
				
				$data['users'][] = array(
					'id' 			=> $result['id'],
					'email'      => $result['email'],
					'selected'   	=> isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected']),
					'action'     	=> $action,
					'start'         => ($page - 1) * 10,
					'limit'         => 10
				);
			}		
		}
		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4newsletter/module/themability_newsletter', $data));
		
	}


	public function install(): void {		
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');		
		$this->load->model('user/user_group');

		//add permission
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'themability_newsletter');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'themability_newsletter');

		$this->model_extension_themability4newsletter_module_themability_newsletter->install();	
	}

	public function uninstall(): void {		
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');	
		$this->load->model('user/user_group');

		// Remove permission
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'themability_newsletter');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'themability_newsletter');

		$this->model_extension_themability4newsletter_module_themability_newsletter->uninstall();
	}	

	public function unsubscribe(): void {
		
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');
			
		$data = '';		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {			
			foreach ($this->request->post['selected'] as $user_id) {
				$this->model_extension_themability4newsletter_module_themability_newsletter->delete($user_id);                
	  		}			
			$this->session->data['success'] = $this->language->get('text_success');			
			$url = '';			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
		} else if( isset($this->request->get['user_id']) ) {
			$data = $this->request->get['user_id'];
			$this->model_extension_themability4newsletter_module_themability_newsletter->delete($data);			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}			
			$url = '';
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->response->redirect($this->url->link('extension/themability4newsletter/module/themability_newsletter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']  . $url, true));
		} else {
			$this->index();
		}		
	}

	protected function validateDelete(): string {
    	if (!$this->user->hasPermission('modify', 'extension/themability4newsletter/module/themability_newsletter')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}		
		return !$this->error;
  	}

	public function save(): void {	
		$json = [];		

		$this->load->language('extension/themability4newsletter/module/themability_newsletter');		
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');

		if (!$this->user->hasPermission('modify', 'extension/themability4newsletter/module/themability_newsletter')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if ((Helper\Utf8\strlen($this->request->post['name']) < 3) || (Helper\Utf8\strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['newsletter_image']) {
			$json['error']['newsletter-image'] = $this->language->get('error_newsletter_image');
		}

		if (!$json) {                     
			$this->load->model('setting/module');
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('themability4newsletter.themability_newsletter', $this->request->post);		
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			$json['success'] = $this->language->get('text_success');
        }		

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}
}
