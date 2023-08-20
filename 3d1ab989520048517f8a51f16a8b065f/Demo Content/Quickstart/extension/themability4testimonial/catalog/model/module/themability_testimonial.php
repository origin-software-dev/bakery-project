<?php

namespace Opencart\Catalog\Model\Extension\Themability4testimonial\Module;
class ThemabilityTestimonial extends \Opencart\System\Engine\Model {

    public function getThemabilityTestimonial($themability_testimonial_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "themability_testimonial t LEFT JOIN " . DB_PREFIX . "themability_testimonial_description td ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE t.themability_testimonial_id = '" . (int)$themability_testimonial_id . "' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND t.status = '1'");
        
        return $query->row;
    }

    public function getThemabilityTestimonials($start = 0, $limit = 10) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX ."themability_testimonial_description td LEFT JOIN " . DB_PREFIX . "themability_testimonial t ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE t.status = '1' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY t.sort_order ASC LIMIT " . (int)$start . "," . (int)$limit);
        return $query->rows;
    }
    public function getRandomThemabilityTestimonial(){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX ."themability_testimonial t LEFT JOIN " . DB_PREFIX . "themability_testimonial_description td ON (t.themability_testimonial_id = td.themability_testimonial_id) WHERE  t.status = '1' AND td.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY RAND() LIMIT 1");
        return $query->row;
    }

    public function getTotalThemabilityTestimonials() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "themability_testimonial AS t WHERE t.status = '1'");
        return $query->row['total'];
    }  

}
?>