<?php
namespace Opencart\Admin\Controller\Extension\Themability4producttab\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityProductTab extends \Opencart\System\Engine\Controller {
	private $error = [];

	public function index(): void {	

		$this->load->language('extension/themability4producttab/module/themability_product_tab');
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
				'href' => $this->url->link('extension/themability4producttab/module/themability_product_tab', 'user_token=' . $this->session->data['user_token'], true)
			];
		}else{
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/themability4producttab/module/themability_product_tab', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/themability4producttab/module/themability_product_tab|save', 'user_token=' . $this->session->data['user_token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('extension/themability4producttab/module/themability_product_tab|save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		if (isset($this->request->get['module_id'])) {
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
			$data['featured_products'] = $module_info['featured_products'];
		} else {
			$data['featured_products'] = "1";
		}

		if (!empty($module_info)) {
			$data['latest_products'] = $module_info['latest_products'];
		} else {
			$data['latest_products'] = "1";
		}

		if (!empty($module_info)) {
			$data['bestseller_products'] = $module_info['bestseller_products'];
		} else {
			$data['bestseller_products'] = "1";
		}

		if (!empty($module_info)) {
			$data['special_products'] = $module_info['special_products'];
		} else {
			$data['special_products'] = "1";
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('catalog/product');

		$data['products'] = array();

		if (!empty($module_info['product'])) {
			$products = $module_info['product'];
		} else {
			$products = array();
		}

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}

		if (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 8;
		}

		if (!empty($module_info)) {
			$data['items_in_row'] = $module_info['items_in_row'];
		} else {
			$data['items_in_row'] = 4;
		}

		if (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 200;
		}

		if (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/themability4producttab/module/themability_product_tab', $data));
	}


	public function save(): void {
		$json = [];		

		$this->load->language('extension/themability4producttab/module/themability_product_tab');		

		if (!$this->user->hasPermission('modify', 'extension/themability4producttab/module/themability_product_tab')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		if ((Helper\Utf8\strlen($this->request->post['name']) < 3) || (Helper\Utf8\strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}	

		if(!isset($this->request->post['special_products'])){
			$this->request->post['special_products'] = 0;
		}

		if(!isset($this->request->post['latest_products'])){
			$this->request->post['latest_products'] = 0;
		}

		if(!isset($this->request->post['featured_products'])){
			$this->request->post['featured_products'] = 0;
		}

		if(!isset($this->request->post['bestseller_products'])){
			$this->request->post['bestseller_products'] = 0;
		}
		
		if ((is_array($this->request->post['product']) && count($this->request->post['product']) < 1) ||  $this->request->post['product'] == "") {
			$json['error']['product'] = $this->language->get('error_featured_product');
		}

		if (!$this->request->post['limit']) {
			$json['error']['limit'] = $this->language->get('error_limit');
		}

		if (!$this->request->post['items_in_row']) {
			$json['error']['items-in-row'] = $this->language->get('error_items_in_row');
		}

		if (!$this->request->post['width']) {
			$json['error']['width']  = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$json['error']['height']  = $this->language->get('error_height');
		}

		if (!$json) {
			$this->load->model('setting/module');
			if (!isset($this->request->post['module_id'])) {
				$module_id = $this->model_setting_module->addModule('themability4producttab.themability_product_tab', $this->request->post);
				$redirect = $this->url->link('extension/themability4producttab/module/themability_product_tab', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $module_id, true);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
				$redirect = $this->url->link('extension/themability4producttab/module/themability_product_tab', 'user_token=' . $this->session->data['user_token']. '&module_id=' . $this->request->get['module_id'], true);
			}
			$json['success'] = $this->language->get('text_success');
			$json['redirect'] = $redirect;
        }		

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));		
	}
}
