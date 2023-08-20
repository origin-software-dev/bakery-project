<?php
namespace Opencart\Catalog\Controller\Extension\Themability4slideshow\Module;
class ThemabilitySlideshow extends \Opencart\System\Engine\Controller {
	public function index($setting) {
		static $module = 0;		

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('extension/themability4slideshow/catalog/view/javascript/slideshow/slideshow.css');	
		$this->document->addScript('extension/themability4slideshow/catalog/view/javascript/slideshow/jssor.slider-28.1.0.min.js');
		
		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;
		$data['slider_height'] = $setting['height'];
        $data['slider_width'] = $setting['width'];

		if (file_exists(DIR_EXTENSION . 'themability4slideshow/catalog/view/template/module/themability_slideshow.twig')) {			
			return $this->load->view('extension/themability4slideshow/module/themability_slideshow', $data);			
		} else {			
			return;
		}
	}
}