<?php
namespace Opencart\Catalog\Controller\Extension\themability4\Common;
class Header extends \Opencart\System\Engine\Controller {
	public function index(): string {

		// Analytics
		$data['analytics'] = [];

		if (!$this->config->get('config_cookie_id') || (isset($this->request->cookie['policy']) && $this->request->cookie['policy'])) {
			$this->load->model('setting/extension');

			$analytics = $this->model_setting_extension->getExtensionsByType('analytics');

			foreach ($analytics as $analytic) {
				if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
					$data['analytics'][] = $this->load->controller('extension/' . $analytic['extension'] . '/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
				}
			}
		}

		$this->load->language('common/header');

		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['title'] = $this->document->getTitle();
		$data['base'] = $this->config->get('config_url');
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		// Hard coding css so they can be replaced via the event's system.
		$data['bootstrap'] = 'catalog/view/stylesheet/bootstrap.css';
		$data['icons'] = 'catalog/view/stylesheet/fonts/fontawesome/css/all.min.css';
		$data['stylesheet'] = 'catalog/view/stylesheet/stylesheet.css';
		$data['themabilitystyle'] = 'extension/themability4/catalog/view/stylesheet/stylesheet.css';
		$data['themabilityfont'] = 'extension/themability4/catalog/view/stylesheet/themability-font.css';

		// Hard coding scripts so they can be replaced via the event's system.
		$data['jquery'] = 'catalog/view/javascript/jquery/jquery-3.6.0.min.js';

		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}		

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['home'] = $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		$data['wishlist'] = $this->url->link('account/wishlist', 'language=' . $this->config->get('config_language') . (isset($this->session->data['customer_token']) ? '&customer_token=' . $this->session->data['customer_token'] : ''));
		$data['logged'] = $this->customer->isLogged();

		if (!$this->customer->isLogged()) {
			$data['register'] = $this->url->link('account/register', 'language=' . $this->config->get('config_language'));
			$data['login'] = $this->url->link('account/login', 'language=' . $this->config->get('config_language'));
		} else {
			$data['account'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['order'] = $this->url->link('account/order', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['transaction'] = $this->url->link('account/transaction', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['download'] = $this->url->link('account/download', 'language=' . $this->config->get('config_language') . '&customer_token=' . $this->session->data['customer_token']);
			$data['logout'] = $this->url->link('account/logout', 'language=' . $this->config->get('config_language'));
		}

		$data['shopping_cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		$data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));
		$data['contact'] = $this->url->link('information/contact', 'language=' . $this->config->get('config_language'));
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['primarymegamenuid'] = $this->config->get('theme_bakery_primary_megamenu');
		$data['theme_class'] = "bakery";

		$data['language'] = $this->load->controller('common/language');		
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		### Set_Language_Code_Session ###
		$language_id =  $this->config->get('config_language_id');
        $languages = $this->model_localisation_language->getLanguages($language_id);
        $lang_code = "en-gb";
        foreach( $languages as  $language){
            if($language['language_id'] == $language_id){
                $lang_code = $language['code'];
            }
        }
		$this->session->data['language'] = $lang_code;
		### .Set_Language_Code_Session ###

		### themability_megamenu_set_in_header ###
		$module_id = $data['primarymegamenuid'];
		$data['full_width_menu'] = 0;
		if($module_id > 0){
			$themability_megamenu_settings = $this->getModulesByCode('themability4megamenu.themability_megamenu', $module_id);
			if( is_array($themability_megamenu_settings)  && count($themability_megamenu_settings) > 0 && isset($themability_megamenu_settings['setting']) ){
				$bm_settings = json_decode($themability_megamenu_settings['setting'],true);
				if(!isset($bm_settings['moduleid']) || $bm_settings['moduleid'] == ''){
					$bm_settings['moduleid'] = $module_id;
				}
				if( (is_array($bm_settings) && count($bm_settings) > 0) && ($bm_settings['moduleid'] == $module_id && $bm_settings['status'] == 1) ){	
					if (file_exists(DIR_EXTENSION . 'themability4megamenu/catalog/view/template/module/themability_megamenu.twig')) {
		               	$data['menu'] = $this->load->controller('extension/themability4megamenu/module/themability_megamenu',$bm_settings);
		            }
				}
				$data['full_width_menu'] = $bm_settings['full_width'];
			}	
		}		
		### .themability_megamenu_set_in_header ###

		### themability_images ###
		$this->load->model('tool/image');
		if($this->config->has('theme_bakery_breadcrumb_image')) {
			$breadcrumb_image = $this->config->get('theme_bakery_breadcrumb_image');
		}else{
			$breadcrumb_image = 'no_image.png';
		}
		$breadcrumb_image_width = 1920;
		$breadcrumb_image_height = 400;
		$breadcrumb_image_path = $this->model_tool_image->resize($breadcrumb_image, $breadcrumb_image_width, $breadcrumb_image_height);

		if($this->config->has('theme_bakery_loader_image')) {
			$loader_image_path = HTTP_SERVER."image/".$this->config->get('theme_bakery_loader_image');
		}else{
			$no_image = 'no_image.png';
			$loader_image_path = $this->model_tool_image->resize($no_image,150,150);
		}
		### .themability_images ###

		### themability_search ###
		if (file_exists(DIR_EXTENSION . 'themability4search/catalog/view/template/module/themability_search.twig') && $this->config->get('module_themability_search_status')) {
			$data['advanceSearch'] = $this->config->get('module_themability_search_autocomplete');
			$data['search'] = $this->load->controller('extension/themability4search/module/themability_search');		
		} else {
			$data['search'] = $this->load->controller('common/search');
		}
		### .themability_search ###

		### themability_quickview ###
		if (file_exists(DIR_EXTENSION . 'themability4quickview/catalog/view/template/module/themability_quickview/themability_quickview.twig') && $this->config->get('module_themability_quickview_status')) {
			$data['themability_quickview_status'] = 1;		
		} else {
			$data['themability_quickview_status'] = 0;
		}
		### .themability_quickview ###

		### bakery colors ###
	  	$primaryColor = $this->config->has('theme_bakery_primary_color') ? $this->config->get('theme_bakery_primary_color') : '#000000';
	  	$primaryHoverColor = $this->config->has('theme_bakery_primary_hover_color') ? $this->config->get('theme_bakery_primary_hover_color') : '#d20b55';
	  	$secondaryColor = $this->config->has('theme_bakery_secondary_color') ? $this->config->get('theme_bakery_secondary_color') : '#FFFFFF';
	  	$secondaryLightColor = $this->config->has('theme_bakery_secondary_light_color') ? $this->config->get('theme_bakery_secondary_light_color') : '#777777';
	  	$backgroundColor = $this->config->has('theme_bakery_background_color') ? $this->config->get('theme_bakery_background_color') : '#f5f4f4';
	  	$borderColor = $this->config->has('theme_bakery_border_color') ? $this->config->get('theme_bakery_border_color') : '#dddddd';	  
		$breadcrumbImage = "url(".$breadcrumb_image_path.")";
		$loaderImage = "url(".$loader_image_path.")";

		$data['inline_style'] = html_entity_decode('<style>
			:root {
				--primary-color: ' . $primaryColor . ';
				--primary-hover-color: ' . $primaryHoverColor . ';
				--secondary-color: ' . $secondaryColor . ';				
				--secondary-light-color: ' . $secondaryLightColor . ';
				--background-color: ' . $backgroundColor . ';
				--border-color: ' . $borderColor . ';
				--breadcrumb-image: ' . $breadcrumbImage . ';
				--loader-image: ' . $loaderImage . '
			}
		</style>', ENT_QUOTES, 'UTF-8');
		### .bakery colors ###

		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}
			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}		

		$data['headerfixed'] = $this->config->get('theme_bakery_header_fixed');
		return $this->load->view('extension/themability4/common/header', $data);
	}

	public function getModulesByCode($code,$module_id = "") {
		if($module_id > 0){
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' AND `module_id` = '" . $module_id ."' ORDER BY `name` LIMIT 1");
			return $query->row;
		}else{
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");
			return $query->rows;
		}		
	}
}
