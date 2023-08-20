<?php
namespace Opencart\Catalog\Controller\Extension\Themability4blogger\Module;
use \Opencart\System\Helper AS Helper;
class ThemabilityBlogger extends \Opencart\System\Engine\Controller {	
	public function index($setting):string {
	
		$this->load->language('extension/themability4blogger/module/themability_blogger');
		$this->load->model('extension/themability4blogger/module/themability_blogger');
		$this->load->model('tool/image');
		$this->document->addStyle('extension/themability4blogger/catalog/view/css/style.css');	
		$this->document->addStyle('extension/themability4blogger/catalog/view/css/lightbox.css');	
		$this->document->addScript('extension/themability4blogger/catalog/view/javascript/lightbox-2.6.min.js');
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_read_more'] = $this->language->get('text_read_more');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['entry_comment'] = $this->language->get('entry_comment');
		
		$data['button_all_blogs'] = $this->language->get('button_all_blogs');

		$data['all_blogs'] = $this->url->link('extension/themability4blogger/bloginfo/themability_blogger');

		$data['blogs'] = array();

		$data['limit'] = $this->config->get('limit');		
		
		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		if (!$setting['char_limit']) {
			$setting['char_limit'] = 150;
		}	
				
		foreach ($this->model_extension_themability4blogger_module_themability_blogger->getBlogsByModule($setting['module_id'], $setting['limit']) as $result) {
			$total_comments = $this->model_extension_themability4blogger_module_themability_blogger->getTotalBlogComments($result['themability_blogger_id']);	
			$data['blogs'][] = array(
				'themability_blogger_id'  => $result['themability_blogger_id'],
				'image' 	  =>$this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
				'title'       => html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'),
				'description' => Helper\Utf8\substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['char_limit']) . '...',
				'date_added_date'   => date($this->language->get('date_format_date'), strtotime($result['date_added'])),
				'date_added_month'   => date($this->language->get('date_format_month'), strtotime($result['date_added'])),
				// 'date_added_year'   => date($this->language->get('date_format_year'), strtotime($result['date_added'])),	
				// 'date_added'  => date('F j, Y', strtotime($result['date_added'])),
				'total_comments' => number_format($total_comments),
				'href'        => $this->url->link('extension/themability4blogger/bloginfo/themability_blogger', 'themability_blogger_id=' . $result['themability_blogger_id'])
			);
		}

		if (file_exists(DIR_EXTENSION . 'themability4blogger/catalog/view/template/module/themability_blogger.twig'))  {
            return $this->load->view('extension/themability4blogger/module/themability_blogger',$data);
		} else {
			return '';
		}
	}
}