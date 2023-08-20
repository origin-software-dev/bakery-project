<?php	
namespace Opencart\Catalog\Controller\Extension\themability4quickview\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityQuickview extends \Opencart\System\Engine\Controller {
	public function index() {
		$json = [];

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}		

        $data = $this->fetchProduct($product_id);

        if(!$json) {
	        if($data) {
	            $json['html'] = $this->load->view('extension/themability4quickview/module/themability_quickview/themability_quickview', $data);
	            $json['success'] = true;
	        } else {
	            $json['success'] = false;
	            $json['html'] = "There are no products to list.";
	        }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function fetchProduct($product_id):array {

    	$this->load->language('product/product');
    	$this->load->model('catalog/product');

    	$product_info = $this->model_catalog_product->getProduct($product_id);

    	if ($product_info) {

    		$data['heading_title'] = $product_info['name'];

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', 'language=' . $this->config->get('config_language')), $this->url->link('account/register', 'language=' . $this->config->get('config_language')));

			$this->load->model('catalog/review');

			$data['text_reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));

			$data['config_file_max_size'] = ((int)$this->config->get('config_file_max_size') * 1024 * 1024);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer|info', 'language=' . $this->config->get('config_language') . '&manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			$data['short_description'] = Helper\Utf8\substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0,400, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..';

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$data['rating'] = (int)$product_info['rating'];
			$data['review_status'] = (int)$this->config->get('config_review_status');

			$data['review'] = $this->load->controller('product/review');

			$data['add_to_wishlist'] = $this->url->link('account/wishlist|add', 'language=' . $this->config->get('config_language'));
			$data['add_to_compare'] = $this->url->link('product/compare|add', 'language=' . $this->config->get('config_language'));
			$data['upload'] = $this->url->link('tool/upload', 'language=' . $this->config->get('config_language'));

			$this->load->model('tool/image');			

			if (is_file(DIR_IMAGE . html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'))) {
				$data['popup'] = $this->model_tool_image->resize(html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if (is_file(DIR_IMAGE . html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'))) {
				$data['thumb'] = $this->model_tool_image->resize(html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}			

			$data['images'] = [];

			$results = $this->model_catalog_product->getImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = [
					'popup' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
					'thumb' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
				];
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getDiscounts($this->request->get['product_id']);

			$data['discounts'] = [];

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				foreach ($discounts as $discount) {
					$data['discounts'][] = [
						'quantity' => $discount['quantity'],
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
					];
				}
			}

			$data['options'] = [];

			foreach ($this->model_catalog_product->getOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = [];

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						if (is_file(DIR_IMAGE . html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8'))) {
								$image = $this->model_tool_image->resize(html_entity_decode($option_value['image'], ENT_QUOTES, 'UTF-8'), 50, 50);
						} else {
							$image = '';
						}

						$product_option_value_data[] = [
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $image,
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						];
					}
				}

				$data['options'][] = [
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				];
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}		

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getAttributes($this->request->get['product_id']);

			$data['tags'] = [];

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = [
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					];
				}
			}

    	} else{
    		$data = false;
    	}
    	return $data;
    }

    public function Viewcontainer() {
        $data['themability_quickview_status'] = 1;
        return $this->load->view('extension/themability4quickview/module/themability_quickview/themability_quickviewcontainer', $data);
    }

    public function insertcontainer() {
        $this->response->setOutput($this->Viewcontainer());
    }

}