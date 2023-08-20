<?php
namespace Opencart\Admin\Controller\Extension\Themability4bannerslider\Module;
use \Opencart\System\Helper AS Helper;
class BannerSlider extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('extension/themability4bannerslider/module/banner_slider');
		$this->document->addStyle('../extension/themability4bannerslider/admin/view/css/style.css');

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

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4bannerslider/module/banner_slider', 'user_token=' . $this->session->data['user_token'])
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4bannerslider/module/banner_slider', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'])
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['save'] = $this->url->link('extension/themability4bannerslider/module/banner_slider|save', 'user_token=' . $this->session->data['user_token']);
		} else {
			$data['save'] = $this->url->link('extension/themability4bannerslider/module/banner_slider|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
		}

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		if (isset($this->request->get['module_id'])) {
			$this->load->model('setting/module');

			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($module_info['name'])) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($module_info['banner_id'])) {
			$data['banner_id'] = $module_info['banner_id'];
		} else {
			$data['banner_id'] = '';
		}

		$this->load->model('design/banner');

		$data['banners'] = $this->model_design_banner->getBanners();

		if (isset($module_info['effect'])) {
			$data['effect'] = $module_info['effect'];
		} else {
			$data['effect'] = '';
		}

		if (isset($module_info['items'])) {
			$data['items'] = $module_info['items'];
		} else {
			$data['items'] = 4;
		}

		if (isset($module_info['laptop_items'])) {
			$data['laptop_items'] = $module_info['laptop_items'];
		} else {
			$data['laptop_items'] = 3;
		}

		if (isset($module_info['tablet_items'])) {
			$data['tablet_items'] = $module_info['tablet_items'];
		} else {
			$data['tablet_items'] = 2;
		}

		if (isset($module_info['mobile_items'])) {
			$data['mobile_items'] = $module_info['mobile_items'];
		} else {
			$data['mobile_items'] = 1;
		}

		if (isset($module_info['loop'])) {
			$data['loop'] = $module_info['loop'];
		} else {
			$data['loop'] = '';
		}

		if (isset($module_info['autoplay'])) {
			$data['autoplay'] = $module_info['autoplay'];
		} else {
			$data['autoplay'] = '';
		}

		if (isset($module_info['navigation'])) {
			$data['navigation'] = $module_info['navigation'];
		} else {
			$data['navigation'] = '';
		}

		if (isset($module_info['pagination'])) {
			$data['pagination'] = $module_info['pagination'];
		} else {
			$data['pagination'] = '';
		}

		if (isset($module_info['interval'])) {
			$data['interval'] = $module_info['interval'];
		} else {
			$data['interval'] = 5000;
		}

		if (isset($module_info['width'])) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($module_info['height'])) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}

		if (isset($module_info['status'])) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['entry_banner_edit'] = sprintf($this->language->get('entry_banner_edit'), $this->url->link('design/banner', 'user_token=' . $this->session->data['user_token'], true));

		$this->response->setOutput($this->load->view('extension/themability4bannerslider/module/banner_slider', $data));
	}

	public function save(): void {
		$this->load->language('extension/themability4bannerslider/module/banner_slider');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/themability4bannerslider/module/banner_slider')) {
			$json['error']['warning'] = $this->language->get('error_permission');
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
				$module_id = $this->model_setting_module->addModule('themability4bannerslider.banner_slider', $this->request->post);
				$redirect = $this->url->link('extension/themability4bannerslider/module/banner_slider', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $module_id, true);
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