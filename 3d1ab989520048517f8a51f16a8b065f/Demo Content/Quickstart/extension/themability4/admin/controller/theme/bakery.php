<?php
namespace Opencart\Admin\Controller\Extension\Themability4\Theme;
class Bakery extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {

        $this->load->language('extension/themability4/theme/bakery');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme'),
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/themability4/theme/bakery', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id']),
        ];

        $data['save'] = $this->url->link('extension/themability4/theme/bakery|save', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id']);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme');

        if (isset($this->request->get['store_id'])) {
            $this->load->model('setting/setting');

            $setting_info = $this->model_setting_setting->getSetting('theme_bakery', $this->request->get['store_id']);            
        }

        if (isset($setting_info['theme_bakery_status'])) {
            $data['theme_bakery_status'] = $setting_info['theme_bakery_status'];
        } else {
            $data['theme_bakery_status'] = '';
        }
        
        #### colors ####
        if (isset($this->request->post['theme_bakery_primary_color'])) {
            $data['theme_bakery_primary_color'] = $this->request->post['theme_bakery_primary_color'];
        } elseif (isset($setting_info['theme_bakery_primary_color'])) {
            $data['theme_bakery_primary_color'] = $setting_info['theme_bakery_primary_color'];
        } else {
            $data['theme_bakery_primary_color'] = '#000000';
        }
        
        if (isset($this->request->post['theme_bakery_primary_hover_color'])) {
            $data['theme_bakery_primary_hover_color'] = $this->request->post['theme_bakery_primary_hover_color'];
        } elseif (isset($setting_info['theme_bakery_primary_hover_color'])) {
            $data['theme_bakery_primary_hover_color'] = $setting_info['theme_bakery_primary_hover_color'];
        } else {
            $data['theme_bakery_primary_hover_color'] = '#d20b55';
        }

        if (isset($this->request->post['theme_bakery_secondary_color'])) {
            $data['theme_bakery_secondary_color'] = $this->request->post['theme_bakery_secondary_color'];
        } elseif (isset($setting_info['theme_bakery_secondary_color'])) {
            $data['theme_bakery_secondary_color'] = $setting_info['theme_bakery_secondary_color'];
        } else {
            $data['theme_bakery_secondary_color'] = '#FFFFFF';
        }

        if (isset($this->request->post['theme_bakery_secondary_light_color'])) {
            $data['theme_bakery_secondary_light_color'] = $this->request->post['theme_bakery_secondary_light_color'];
        } elseif (isset($setting_info['theme_bakery_secondary_light_color'])) {
            $data['theme_bakery_secondary_light_color'] = $setting_info['theme_bakery_secondary_light_color'];
        } else {
            $data['theme_bakery_secondary_light_color'] = '#777777';
        }

        if (isset($this->request->post['theme_bakery_background_color'])) {
            $data['theme_bakery_background_color'] = $this->request->post['theme_bakery_background_color'];
        } elseif (isset($setting_info['theme_bakery_background_color'])) {
            $data['theme_bakery_background_color'] = $setting_info['theme_bakery_background_color'];
        } else {
            $data['theme_bakery_background_color'] = '#f5f4f4';
        }

        if (isset($this->request->post['theme_bakery_border_color'])) {
            $data['theme_bakery_border_color'] = $this->request->post['theme_bakery_border_color'];
        } elseif (isset($setting_info['theme_bakery_border_color'])) {
            $data['theme_bakery_border_color'] = $setting_info['theme_bakery_border_color'];
        } else {
            $data['theme_bakery_border_color'] = '#dddddd';
        }
        #### .colors ####
        
        #### Images ####
        $this->load->model('tool/image');

        if (isset($this->request->post['theme_bakery_breadcrumb_image'])) {
            $data['theme_bakery_breadcrumb_image'] = $this->request->post['theme_bakery_breadcrumb_image'];
        } elseif (isset($setting_info['theme_bakery_breadcrumb_image'])) {
            $data['theme_bakery_breadcrumb_image'] = $setting_info['theme_bakery_breadcrumb_image'];
        } else {
            $data['theme_bakery_breadcrumb_image'] = '';
        }

		if (!empty($setting_info['theme_bakery_breadcrumb_image']) && is_file(DIR_IMAGE . $setting_info['theme_bakery_breadcrumb_image'])) {
			$data['theme_bakery_breadcrumb_image_thumb'] = $this->model_tool_image->resize($setting_info['theme_bakery_breadcrumb_image'], 100, 100);
		} else {
			$data['theme_bakery_breadcrumb_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['theme_bakery_breadcrumb_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['theme_bakery_loader_image'])) {
            $data['theme_bakery_loader_image'] = $this->request->post['theme_bakery_loader_image'];
        } elseif (isset($setting_info['theme_bakery_loader_image'])) {
            $data['theme_bakery_loader_image'] = $setting_info['theme_bakery_loader_image'];
        } else {
            $data['theme_bakery_loader_image'] = '';
        }

        if (!empty($setting_info['theme_bakery_loader_image']) && is_file(DIR_IMAGE . $setting_info['theme_bakery_loader_image'])) {
            $data['theme_bakery_loader_image_thumb'] = $this->model_tool_image->resize($setting_info['theme_bakery_loader_image'], 100, 100);
        } else {
            $data['theme_bakery_loader_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }
        $data['theme_bakery_loader_image_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        #### .Images ####

        #### sticky header ####
        if (isset($this->request->post['theme_bakery_header_fixed'])) {
            $data['theme_bakery_header_fixed'] = $this->request->post['theme_bakery_header_fixed'];
        } elseif (isset($setting_info['theme_bakery_header_fixed'])) {
            $data['theme_bakery_header_fixed'] = $setting_info['theme_bakery_header_fixed'];
        } else {
            $data['theme_bakery_header_fixed'] = '';
        }
        #### .sticky header ####

        ### themability_extension_status ###      
        $data['blog_enable'] = 0;
        $data['themability_megamenu_enable'] = 0;      
        $data['theme_bakery_primary_megamenu'] = 0;    
        $type="module";
        $results = $this->model_setting_extension->getExtensions($type);
        
        foreach($results as $result){       
            if($result['extension']==="themability4blogger"){
                $data['blog_enable'] = 1;
            }
            if($result['extension']==="themability4megamenu"){
                $data['themability_megamenu_enable'] = 1;
            }
        } 
        ### .themability_extension_status ###

        #### blog page limit ####
        if (isset($this->request->post['theme_bakery_blog_limit'])) {
            $data['theme_bakery_blog_limit'] = $this->request->post['theme_bakery_blog_limit'];
        } elseif (isset($setting_info['theme_bakery_blog_limit'])) {
            $data['theme_bakery_blog_limit'] = $setting_info['theme_bakery_blog_limit'];
        } else {
            $data['theme_bakery_blog_limit'] = 4;
        }
        #### .blog page limit ####

        #### primary megamenu ####       
        if (isset($this->request->post['theme_bakery_primary_megamenu'])) {
            $data['theme_bakery_primary_megamenu'] = $this->request->post['theme_bakery_primary_megamenu'];
        } elseif (isset($setting_info['theme_bakery_primary_megamenu'])) {
            $data['theme_bakery_primary_megamenu'] = $setting_info['theme_bakery_primary_megamenu'];
        } else {
            $data['theme_bakery_primary_megamenu'] = '';
        }   
        #### .primary megamenu ####
        
        #### get_themability_megamenu_list_with_setting ####
        $themability_megamenu_settings = $this->getModulesByCode('themability4megamenu.themability_megamenu');
        $themability_megamenu_list = array();
        foreach($themability_megamenu_settings as $themability_megamenu_setting){
            $module_id = $themability_megamenu_setting['module_id'];
            $mm_setting = (array) json_decode($themability_megamenu_setting['setting']);
            $themability_megamenu_list[] = array(
                'module_id' => $module_id,
                'name' => $mm_setting['name'],
                'status' => $mm_setting['status'],
            );
        }       
        $data['themability_megamenu_list'] = $themability_megamenu_list;
        #### .get_themability_megamenu_list_with_setting ####


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/themability4/theme/bakery', $data));
    }

    public function save(): void
    {
        $this->load->language('extension/themability4/theme/bakery');

        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/themability4/theme/bakery')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['theme_bakery_breadcrumb_image']) {
			$json['error']['theme-bakery-breadcrumb-image'] = $this->language->get('error_theme_bakery_breadcrumb_image');
		}

        if (!$this->request->post['theme_bakery_loader_image']) {
			$json['error']['theme-bakery-loader-image'] = $this->language->get('error_theme_bakery_loader_image');
		}

        if (!$json) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('theme_bakery', $this->request->post, $this->request->get['store_id']);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function install(): void
    {
        if ($this->user->hasPermission('modify', 'extension/themability4/theme/bakery')) {
            $this->load->model('setting/startup');
            $startup_data = [
                'code' => 'theme_bakery',
                'action' => 'catalog/extension/themability4/startup/bakery',
                'status' => 1,
                'sort_order' => 3,
            ];

            $this->model_setting_startup->addStartup($startup_data);

            $startup_data_admin = [
                'code' => 'theme_bakery',
                'action' => 'admin/extension/themability4/startup/bakery',
                'status' => 1,
                'sort_order' => 4,
            ];
            $this->model_setting_startup->addStartup($startup_data_admin);

        }
    }

    public function uninstall(): void
    {
        if ($this->user->hasPermission('modify', 'extension/themability4/theme/bakery')) {
            $this->load->model('setting/startup');
            $this->model_setting_startup->deleteStartupByCode('theme_bakery');
        }
    }

    public function getModulesByCode($code,$module_id = "") { 
        if($module_id > 0){
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' AND `module_id` = '" . $module_id ."' ORDER BY `name`");
        }else{
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");
        }
        return $query->rows;
    }
}
