<?php
namespace Opencart\Catalog\Controller\Extension\Themability4megamenu\Module;
class ThemabilityMegamenu extends \Opencart\System\Engine\Controller {

    public function index($setting){
        $this->load->model('extension/themability4megamenu/module/themability_megamenu');
        $this->document->addStyle('extension/themability4megamenu/catalog/view/css/themability_megamenu.css');       
        $this->document->addScript('extension/themability4megamenu/catalog/view/javascript/themability_megamenu.js');

		$module_id = (isset($setting['moduleid']) && $setting['moduleid']) ? $setting['moduleid'] : 0;
        $data['menu'] = $this->model_extension_themability4megamenu_module_themability_megamenu->getMenu($module_id);

        //dev
        $this->load->language('extension/themability4megamenu/module/themability_megamenu');
        $data['text_more_category']             = $this->language->get('text_more_category');
        $data['text_close_category']            = $this->language->get('text_close_category');

		foreach($data['menu'] as &$menu){
			if(isset($menu['link']) && $menu['link']){
				$menu['link'] = trim($menu['link']);
				$link = (isset($menu['link']) && ($menu['link'])) ? unserialize($menu['link']) : array();
				$menu['route'] = '';
				$menu['path'] = '';
				if($link){
					if(isset($menu['type_link']) && $menu['type_link'] == 1){
						$menu['link'] = $this->url->link('product/category', 'path=' . $link['category']);
						$menu['route'] = 'product/category';
						$menu['path']	= $link['category'];
					}elseif(isset($menu['type_link']) && $menu['type_link'] == 2){
                        $menu['link'] = $this->url->link($link['route_url']); ## route_url ##
					}else
						$menu['link'] = $link['url'];
				}
				else
					$menu['link'] = '';
			}	
		}
        $lang_id = $this->config->get('config_language_id');
		if($setting['show_itemver'] == ""){
			$setting['show_itemver'] = 5;
		}
        $data['ustawienia'] = array(
            'orientation' => $setting['orientation'],
            'search_bar' => $setting['search_bar'],
            'navigation_text' => $setting['navigation_text'],
            'full_width' => $setting['full_width'],
            'home_item' => $setting['home_item'],
            'home_text' => $setting['home_text'],
            'animation' => $setting['animation'],
            'show_itemver' => $setting['show_itemver'],
            'animation_time' => $setting['animation_time'],
            'moduleid' => $setting['moduleid'],
			'disp_title_module' => isset($setting['disp_title_module']) ? $setting['disp_title_module'] : ''
        );
        
        $data['navigation_text'] = 'Navigation';
        if(isset($setting['navigation_text'][$lang_id])) {
            if(!empty($setting['navigation_text'][$lang_id])) {
                $data['navigation_text'] = $setting['navigation_text'][$lang_id];
            }
        }
        if(isset($setting['head_name'][$lang_id])) {
            if(!empty($setting['head_name'][$lang_id])) {
                $data['head_name'] = $setting['head_name'][$lang_id];
            }
        }		
        $data['home_text'] = 'Home';
        if(isset($setting['home_text'][$lang_id])) {
            if(!empty($setting['home_text'][$lang_id])) {
                $data['home_text'] = $setting['home_text'][$lang_id];
            }
        }
        $data['home'] = $this->url->link('common/home');
        $data['lang_id'] = $this->config->get('config_language_id');

        $http = $_SERVER["HTTPS"]  ? 'https://' : 'http://';
        $data['actual_link'] = $http."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (isset($_GET['route']))
            $data['route']  = $_GET['route'];
        else
            $data['route']  = '';

        if (isset($_GET['path']))
            $data['path']   = $_GET['path'];
        else
            $data['path']   = '';
		
        // Search
        $this->language->load('common/header');

        // caching
        $use_cache = (int)$setting['use_cache'];
        $cache_time = (int)$setting['cache_time'];        

        $folder_cache = DIR_CACHE.'themability/Megamenu/';

        if(!file_exists($folder_cache))
            mkdir ($folder_cache, 0777, true);
        if (!class_exists('Cache_Lite'))
            require_once (DIR_EXTENSION . 'themability4megamenu/system/library/Cache_Lite/Lite.php');
       
        $options = [];
        $options = [
            'cacheDir' => $folder_cache,
            'lifeTime' => $cache_time
        ];     
         
        $Cache_Lite = new \Opencart\System\Library\Controller\Extension\Themability4megamenu\Cache_Lite\Cache_Lite($options);

        if ($use_cache){
            $this_hash = md5(serialize($setting).$this->config->get('config_language_id').$this->session->data['currency']);
            $_data = $Cache_Lite->get($this_hash);
            if (!$_data) {
                $_data = $this->load->view('extension/themability4megamenu/module/themability_megamenu', $data);
                $Cache_Lite->save($_data);
                return $_data;
            } else {
                return $_data;
            }
        }else{
            if(file_exists($folder_cache)){
                $Cache_Lite->_cleanDir($folder_cache);
            }
            if (file_exists(DIR_EXTENSION . 'themability4megamenu/catalog/view/template/module/themability_megamenu.twig')) {         
                return $this->load->view('extension/themability4megamenu/module/themability_megamenu', $data);
            }else{              
                return;
            }
        }
    }
}
?>