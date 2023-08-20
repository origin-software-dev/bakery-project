<?php
namespace Opencart\Catalog\Controller\Extension\themability4\Startup;
class Bakery extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {
        if ($this->config->get('theme_bakery_status')) {
            $this->event->register('view/*/before', new \Opencart\System\Engine\Action('extension/themability4/startup/bakery|event'));
        }
    }

    public function event(string &$route, array &$args, mixed &$output): void
    {   	
        $override = [
        	'common/header',
        	'common/home',
            'common/currency',
            'common/menu',
            'common/cart',
            'common/footer',
            'common/footer_left',
            'common/footer_right',            
            'common/column_left',
            'common/column_right',
            'checkout/cart_list',
            'product/thumb',
            'product/category',
            'product/product',
            'product/review',
            'product/compare',
            'information/contact',
            'product/manufacturer_info',
            'product/manufacturer_list',
            'product/special',
            'product/search',
            'account/account',
            'extension/opencart/module/featured',
            'extension/opencart/module/latest',
            'extension/opencart/module/special',
            'extension/opencart/module/bestseller',
            'extension/opencart/module/filter',
            'extension/opencart/module/category',
            'extension/opencart/module/banner',
            'extension/opencart/module/account',
            'extension/opencart/module/information',
        ];

        if (in_array($route, $override)) {
        	if ($route == "common/header") {
        		$output = $this->load->controller('extension/themability4/common/header');
            } elseif ($route == "common/cart") {
                $this->load->language('extension/themability4/common/cart');                
                // $args['text_items_header_cart'] = $args['text_items'];
                // $total = 5;
                $args['text_items_header_cart'] = sprintf($this->language->get('text_items_header_cart'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));
                // $args['text_items_header_cart'] = sprintf($this->language->get('text_items_header_cart'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
                $route = 'extension/themability4/' . $route;
            } elseif ($route == "common/footer") {
                $output = $this->load->controller('extension/themability4/common/footer');
            } elseif ($route == "common/footer_left") {
                $output = $this->load->controller('extension/themability4/common/footer_left');
            } elseif ($route == "common/footer_right") {
                $output = $this->load->controller('extension/themability4/common/footer_right');
            } elseif ($route == "product/thumb") {
                $this->load->language('extension/themability4/product/thumb');     
                $route = 'extension/themability4/product/thumb';       
            } elseif ($route == "extension/opencart/module/featured") {
                $route = 'extension/themability4/module/featured';
            } elseif ($route == "extension/opencart/module/latest") {
                $route = 'extension/themability4/module/latest';
            } elseif ($route == "extension/opencart/module/special") {
                $route = 'extension/themability4/module/special';
            } elseif ($route == "extension/opencart/module/bestseller") {
                $route = 'extension/themability4/module/bestseller';
            } elseif ($route == "extension/opencart/module/filter") {
                $route = 'extension/themability4/module/filter';
            } elseif ($route == "extension/opencart/module/category") {
                $output = $this->load->controller('extension/themability4/module/category');
                $route = 'extension/themability4/' . $route;
            } elseif ($route == "extension/opencart/module/banner") {
                $route = 'extension/themability4/module/banner';
            } elseif ($route == "extension/opencart/module/account") {
                $route = 'extension/themability4/module/account';
            } elseif ($route == "extension/opencart/module/information") {
                $route = 'extension/themability4/module/information';
        	} else {
        		$route = 'extension/themability4/' . $route;
        	}
        }
    }
}