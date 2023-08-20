<?php
namespace Opencart\Catalog\Controller\Extension\Themability4newsletter\Module;
class ThemabilityNewsletter extends \Opencart\System\Engine\Controller {
	private $error = [];
	
	public function index($setting): string {
		$this->load->language('extension/themability4newsletter/module/themability_newsletter');
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');
		$this->document->addScript('extension/themability4newsletter/catalog/view/javascript/jquery.cookie.js');
		$this->document->addStyle('extension/themability4newsletter/catalog/view/css/style.css');

		$data['user_subscribe'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter|subscribe');
		$data['user_subscribe_popup'] = $this->url->link('extension/themability4newsletter/module/themability_newsletter|subscribepopup');

		// echo "<pre>";print_r($setting);echo "</pre>";
		$this->load->model('tool/image');
		if(isset($setting['newsletter_image']) && $setting['newsletter_image'] != '') {
			$newsletter_image = $setting['newsletter_image'];
		}else{
			$newsletter_image = 'no_image.png';
		}
		
		$block_width = 50;
		$block_height = 50;
		$popup_image_width = 298;
		$popup_image_height = 271;
		
		if (file_exists(DIR_EXTENSION . 'themability4newsletter/catalog/view/template/module/themability_newsletter.twig')) {
		    if(isset($setting['popup']) && $setting['popup']==1) {	
				$data['newsletter_image'] = $this->model_tool_image->resize($newsletter_image, $popup_image_width, $popup_image_height);
				return $this->load->view('extension/themability4newsletter/module/themability_newsletter_popup',$data);
			}else {
				$data['newsletter_image'] = $this->model_tool_image->resize($newsletter_image, $block_width, $block_height);
				return $this->load->view('extension/themability4newsletter/module/themability_newsletter',$data);
			}
		} else {
			return '';
		}
	}

	public function subscribe() : void {
		$json = [];	
		$this->load->language('extension/themability4newsletter/module/themability_newsletter');
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');

		if (isset($this->request->post['themability_newsletter_usr_email']) and filter_var($this->request->post['themability_newsletter_usr_email'],FILTER_VALIDATE_EMAIL)) {
			if (!$this->model_extension_themability4newsletter_module_themability_newsletter->checkalreadyemail($this->request->post)) {
				$this->model_extension_themability4newsletter_module_themability_newsletter->subscribe($this->request->post);
				$json['success'] = $this->language->get('success_subscribe');
				$json['form_reset'] = "yes";
			}else{	
			$json['error'] = $this->language->get('already_subscribe');
			}
		}else{
			$json['error'] = $this->language->get('error_message');
		}
		
		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));	
	}

	public function subscribepopup() : void {
	  	$this->load->language('extension/themability4newsletter/module/themability_newsletter');
		$this->load->model('extension/themability4newsletter/module/themability_newsletter');
	 
		if (isset($this->request->post['themability_newsletter_usr_popup_email']) and filter_var($this->request->post['themability_newsletter_usr_popup_email'],FILTER_VALIDATE_EMAIL)) {
			$this->request->post['themability_newsletter_usr_email'] = $this->request->post['themability_newsletter_usr_popup_email']; 
			if (!$this->model_extension_themability4newsletter_module_themability_newsletter->checkalreadyemail($this->request->post)) {
				$this->model_extension_themability4newsletter_module_themability_newsletter->subscribe($this->request->post);
				$json['success'] = $this->language->get('success_subscribe');	
				$json['form_reset'] = "yes";			 
			} else {
				$json['error'] = $this->language->get('already_subscribe');
			}
		} else {			
			$json['error'] = $this->language->get('error_message');
		}

		$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));	  
	}

	protected function loadmodule() {
		$this->load->language('extension/themability4newsletter/module/themability_newsletter');
		$data = [];
		if (file_exists(DIR_EXTENSION . 'themability4newsletter/catalog/view/template/module/themability_newsletter.twig')) {
			return $this->load->view('extension/themability4newsletter/module/themability_newsletter', $data);
		}else{
			return;
		}
	}
}
