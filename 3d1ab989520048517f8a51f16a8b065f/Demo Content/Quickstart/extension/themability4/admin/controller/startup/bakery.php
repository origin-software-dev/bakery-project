<?php
namespace Opencart\Admin\Controller\Extension\Themability4\Startup;
class Bakery extends \Opencart\System\Engine\Controller
{
    public function index(): void
    {       
        if ($this->config->get('theme_bakery_status')) {
            $this->event->register('view/*/before', new \Opencart\System\Engine\Action('extension/themability4/startup/bakery|event'));
            $this->event->register('controller/*/before', new \Opencart\System\Engine\Action('extension/themability4/startup/bakery|event_setting'));
        }
    }

    public function event(string &$route, array &$args, mixed &$output): void
    {
        $override = [
            'design/layout_form'            
        ];      
    
        if (in_array($route, $override)) {
        	if ($route == "design/layout_form") {
        		$this->load->language('extension/themability4/design/layout');
                $route = 'extension/themability4/' . $route;
        	} else {
        		$route = 'extension/themability4/' . $route;
        	}
        }
    }

    public function event_setting(string &$route, array &$args): void
    {
        $override = [           
            'setting/setting|theme'
        ];              

        if (in_array($route, $override)) {          
        	if ($route == "setting/setting|theme") {
                $this->load->controller('extension/themability4/setting/setting');                
        	} else {                
        		$route = 'extension/themability4/' . $route;
        	}
        }

    }
}
