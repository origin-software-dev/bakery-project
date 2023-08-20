<?php
namespace Opencart\Admin\Controller\Extension\Themability4slideshow\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilitySlideshow extends \Opencart\System\Engine\Controller {
	private $error = [];

	public function index(): void {
		
		$this->load->language('extension/themability4slideshow/module/themability_slideshow');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/module');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
				'href' => $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token'], true)
			];
		}else{
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			];
		}
			
		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/themability4slideshow/module/themability_slideshow|save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/themability4slideshow/module/themability_slideshow|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');
	

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['banner_id'])) {
			$data['banner_id'] = $this->request->post['banner_id'];
		} elseif (!empty($module_info)) {
			$data['banner_id'] = $module_info['banner_id'];
		} else {
			$data['banner_id'] = '';
		}

		$this->load->model('design/banner');

		$data['banners'] = $this->model_design_banner->getBanners();

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4slideshow/module/themability_slideshow', $data));

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/slideshow')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}

	public function save(): void {
		$json = [];

		$this->load->language('extension/themability4slideshow/module/themability_slideshow');
		
		if (!$this->user->hasPermission('modify', 'extension/themability4slideshow/module/themability_slideshow')) {
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

		if (!$json) {
			$this->load->model('setting/module');
			if (!isset($this->request->get['module_id'])) {
				$module_id = $this->model_setting_module->addModule('themability4slideshow.themability_slideshow', $this->request->post);
				$redirect = $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $module_id, true);
				$json['redirect'] = $redirect;
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
				// $redirect = $this->url->link('extension/themability4slideshow/module/themability_slideshow', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true);
			}
			$json['success'] = $this->language->get('text_success');
        }		

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}
}
