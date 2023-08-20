<?php
namespace Opencart\Catalog\Controller\Extension\Themability4testimonial\Module;
class ThemabilityTestimonial extends \Opencart\System\Engine\Controller {

    public function index($setting):string {
        
        $this->load->model('extension/themability4testimonial/module/themability_testimonial');
        $this->load->language('extension/themability4testimonial/module/themability_testimonial');

        $this->document->addStyle('extension/themability4testimonial/catalog/view/css/style.css');
        $this->load->model('tool/image');

        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');        

        $lang_code = "en-gb";
        if(!isset($this->session->data['language']) || (isset($this->session->data['language']) && $this->session->data['language'] == "")){
            $language_id =  $this->config->get('config_language_id');
            $languages = $this->model_localisation_language->getLanguages($language_id);
            
            foreach( $languages as  $language){
                if($language['language_id'] == $language_id){
                    $lang_code = $language['code'];
                }
            }
            $this->session->data['language'] = $lang_code;
        }

        if(isset($setting['title']) && $setting['title']) {
            $data['title'] = $setting['title'][$lang_code]['title'];
        } else {
            $data['title'] = $this->language->get('heading_title');
        }
    
        $themability_testimonial_total = $this->model_extension_themability4testimonial_module_themability_testimonial->getTotalThemabilityTestimonials();

        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if($limit > (int) $themability_testimonial_total) {
            $limit = (int) $themability_testimonial_total;
        }

        if (isset($setting['items'])) {
            $items = $setting['items'];
        } else {
            $items = 4;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 3000;
        }

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        ### testimonial_background_image ###
		$this->load->model('tool/image');
		if(isset($setting['testimonial_background_image']) && $setting['testimonial_background_image']) {
            $testimonial_background_image = $setting['testimonial_background_image'];			
		}else{
            $testimonial_background_image = 'no_image.png';
		}
		$testimonial_background_image_width = 1920;
		$testimonial_background_image_height = 550;
		$testimonial_background_image_path = $this->model_tool_image->resize($testimonial_background_image, $testimonial_background_image_width, $testimonial_background_image_height);
        $data['testimonial_background_image'] = $testimonial_background_image_path;
		### .testimonial_background_image ###

        $data['limit'] = $limit;
        $data['themability_testimonials'] = array();

        $results = $this->model_extension_themability4testimonial_module_themability_testimonial->getThemabilityTestimonials(0, $limit);
        
        foreach($results as $result){

            if($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
            }else{
                $image = $this->model_tool_image->resize('no_image.png', $setting['width'], $setting['height']);            
            }

            $data['themability_testimonials'][] = array(
                'customer_name'    	=> $result['customer_name'],
                'image'             => $image,
                'content'   		=> html_entity_decode($result['content'], ENT_QUOTES, 'UTF-8') . "\n",					
            );
        }        

        $data['slide'] = array(
            'auto' => $auto,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );
		
		$data['text_readmore'] = $this->language->get('text_readmore');       

        if (file_exists(DIR_EXTENSION . 'themability4testimonial/catalog/view/template/module/themability_testimonial.twig'))  {
            return $this->load->view('extension/themability4testimonial/module/themability_testimonial',$data);
		} else {
			return '';
		}
        
    }

}