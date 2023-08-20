<?php
namespace Opencart\Catalog\Controller\Extension\Themability4bannerslider\Module;
class BannerSlider extends \Opencart\System\Engine\Controller {
	public function index(array $setting): string {
		static $banner_slider_module = 0;

		$this->document->addStyle('extension/themability4bannerslider/catalog/view/css/style.css');	
		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$data['banner_slider'] = [];

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
				$data['banner_slider'][] = [
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height'])
				];
			}
		}

		if ($data['banner_slider']) {
			$data['banner_slider_module'] = $banner_slider_module++;

			$data['effect'] = $setting['effect'];
			$data['loop'] = $setting['loop'];
			$data['autoplay'] = $setting['autoplay'];
			$data['navigation'] = $setting['navigation'];
			$data['pagination'] = $setting['pagination'];
			$data['banner_slider_items'] = $setting['items'];
			$data['banner_slider_laptop_items'] = $setting['laptop_items'];
			$data['banner_slider_tablet_items'] = $setting['tablet_items'];
			$data['banner_slider_mobile_items'] = $setting['mobile_items'];
			$data['interval'] = $setting['interval'];
			$data['banner_slider_width'] = $setting['width'];
			$data['banner_slider_height'] = $setting['height'];

			return $this->load->view('extension/themability4bannerslider/module/banner_slider', $data);
		} else {
			return '';
		}
	}
}
