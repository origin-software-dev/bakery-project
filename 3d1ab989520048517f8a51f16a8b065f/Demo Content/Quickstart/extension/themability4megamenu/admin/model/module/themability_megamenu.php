<?php
namespace Opencart\Admin\Model\Extension\Themability4megamenu\Module;
class ThemabilityMegamenu extends \Opencart\System\Engine\Model {

    private $errors = [];

    public function generate_nestable_list($lang_id) {
        $module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE parent_id='0' AND module_id='".$module_id."' ORDER BY rang");
        if (!isset($this->request->get['module_id'])) {
            $action = $this->url->link('extension/themability4megamenu/module/themability_megamenu', 'user_token=' . $this->session->data['user_token'], 'SSL');
        } else {
            $action = $this->url->link('extension/themability4megamenu/module/themability_megamenu', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
        }
        $output = '<div class="cf nestable-lists">';
        $output .= '<div class="dd" id="nestable">';
        $output .= '<ol class="dd-list">';
        foreach ($query->rows as $row) {
            $json = unserialize($row['name']);
            if(isset($json[$lang_id])) {
                $name = $this->skrut($json[$lang_id], 10);
            } else {
                $name = 'Set name';
            }
            if ($row['status']==0)
                $class ='fa fa-square';
            else
                $class ='fa fa-square-o';
            $output .= '<li class="dd-item" data-id="'.$row['id'].'">';
            $output .= '<a data-toggle="tooltip" title="Duplicate" href="'.$action.'&duplicate='.$row['id'].'"  class="fa fa-plus"></a>';
            $output .= '<a data-toggle="tooltip" title="Change Status" href="'.$action.'&changestatus='.$row['id'].'"  class="'.$class.'"></a>';
            $output .= '<a data-toggle="tooltip" title="Delete" href="'.$action.'&delete='.$row['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')" class="fa fa-trash fa-fw"></a><a data-toggle="tooltip" title="edit" href="'.$action.'&edit='.$row['id'].'" class="fa fa-pencil fa-fw"></a>';
            $output .= '<div class="dd-handle">'.$name.' (ID: '.$row['id'].')</div>';
            $output .= $this->menu_showNested($row['id'], $lang_id);
            $output .= '</li>';
        }
        $output .= '</ol>';
        $output .= '</div>';
        $output .= '</div>';
        return $output;
    }

    public function menu_showNested($parentID, $lang_id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE parent_id='".$parentID."' ORDER BY rang");
        if (!isset($this->request->get['module_id'])) {
            $action = $this->url->link('extension/themability4megamenu/module/themability_megamenu', 'user_token=' . $this->session->data['user_token'], 'SSL');
        } else {
            $action = $this->url->link('extension/themability4megamenu/module/themability_megamenu', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
        }
        $output = false;
        if (count($query->rows) > 0) {
            $output .= "<ol class='dd-list'>\n";
            foreach ($query->rows as $row) {
                $output .= "\n";
                $json = unserialize($row['name']);
                if(isset($json[$lang_id])) {
                    $name = $this->skrut($json[$lang_id], 10);
                } else {
                    $name = 'Set name';
                }
                if ($row['status']==0)
                    $class ='fa fa-square';
                else
                    $class ='fa fa-square-o';
                $output .= "<li class='dd-item' data-id='{$row['id']}'>\n";
                $output .= '<a data-toggle="tooltip" title="Duplicate" href="'.$action.'&duplicate='.$row['id'].'"  class="fa fa-plus"></a>';
                $output .= '<a data-toggle="tooltip" title="Change Status" href="'.$action.'&changestatus='.$row['id'].'" class="'.$class.'" ></a>';
                $output .= '<a  data-toggle="tooltip" title="Delete" href="'.$action.'&delete='.$row['id'].'" onclick="return confirm(\'Are you sure you want to delete?\')" class="fa fa-trash fa-fw"></a>';
                $output .= "<a data-toggle='tooltip' title='edit'  href='".$action."&edit=".$row['id']."' class='fa fa-pencil fa-fw'></a><div class='dd-handle'>{$name} (ID: {$row['id']})</div>\n";
                $output .= $this->menu_showNested($row['id'], $lang_id);
                $output .= "</li>\n";
            }
            $output .= "</ol>\n";
        }
        return $output;
    }

    public  function getSubMenu($parentID){
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE parent_id='".$parentID."' ORDER BY rang");
        return $query->rows;
    }

    public function save_rang($parent_id, $id, $rang) {
        $this->db->query("UPDATE " . DB_PREFIX . "themability_mega_menu SET parent_id = '" . $parent_id . "', rang = '" . $rang . "' WHERE id = '" . $id . "'");
    }

    public function addMenu($data) {
        
        $data['parent_id'] = (isset($data['parent_id']) && $data['parent_id']) ? $data['parent_id'] : 0;
        if(isset($data['module_id']) && $data['module_id'])
            $module_id = $data['module_id'];
        else
            $module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "themability_mega_menu SET name = '" . $this->db->escape(serialize($data['name'])) . "',label_item = '".$data['label_item']."',icon_font = '".$data['icon_font']."',class_menu = '".$data['class_menu']."',  description = '" . $this->db->escape(serialize($data['description'])) . "', icon = '" . $data['icon'] . "', parent_id = '". $data['parent_id'] ."', type_link = '" . $data['type_link'] . "', module_id = '" . $module_id . "', link = '" . $data['link'] . "', new_window = '" . $data['new_window'] . "', status = '" . $data['status'] . "', position = '" . $data['position'] . "', submenu_width = '" . $data['submenu_width'] . "', submenu_type = '" . $data['display_submenu'] . "', rang='1000', content_width='" . $data['content_width'] . "', content_type='" . $data['content_type'] . "', content='" . $this->db->escape(serialize($data['content'])) . "'");
        
        return $this->db->getLastId();
    }

    public function saveMenu($data) {
        $module_id = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
        $data['content']['categories']['categories'] = json_decode(html_entity_decode($data['content']['categories']['categories']), true);
        $this->db->query("UPDATE " . DB_PREFIX . "themability_mega_menu SET name = '" . $this->db->escape(serialize($data['name'])) . "', label_item = '".$data['label_item']."',icon_font = '".$data['icon_font']."',class_menu = '".$data['class_menu']."', description = '" . $this->db->escape(serialize($data['description'])) . "', icon = '" . $data['icon'] ."',type_link = '" . $data['type_link'] . "', module_id = '" . $module_id . "', link = '" . $data['link'] ."', new_window = '" . $data['new_window'] ."', status = '" . $data['status'] ."', position = '" . $data['position'] ."', submenu_width = '" . $data['submenu_width'] ."', submenu_type = '" . $data['display_submenu'] ."', content_width = '" . $data['content_width'] ."', content_type = '" . $data['content_type'] ."', content = '" . $this->db->escape(serialize($data['content'])) . "' WHERE id = '" . $data['id'] . "'");
    }

    public function UpdatePosition($data) {
        $this->db->query("UPDATE " . DB_PREFIX . "themability_mega_menu SET  status = '" . $data['status'] ."' WHERE id = '" . $data['id'] . "'");
    }

    public function deleteMenu($id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE id='".$id."'");
        if(count($query->rows) > 0) {
            $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE parent_id='".$id."'");
            if(count($query->rows) > 0) {
                $this->errors[] = "Menu wasn't removed because contains submenu.";
            } else {
                $this->db->query("DELETE FROM " . DB_PREFIX . "themability_mega_menu WHERE id = '" . $id . "'");
                return true;
            }
        } else {
            $this->errors[] = 'This menu does not exist!';
        }
        return false;
    }

    public function getMenu($id) {
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE id='".$id."'");
        if(count($query->rows) > 0) {
            $data = array();
            foreach ($query->rows as $result) {
                $data = array(
                    'name' => unserialize($result['name']),
                    'description' => unserialize($result['description']),
                    'icon' => $result['icon'],
                    'type_link' => $result['type_link'],
                    'link' => $result['link'],
                    'label_item' => $result['label_item'],
                    'icon_font' => $result['icon_font'],
                    'class_menu' => $result['class_menu'],
                    'new_window' => $result['new_window'],
                    'status' => $result['status'],
                    'position' => $result['position'],
                    'submenu_width' => $result['submenu_width'],
                    'display_submenu' => $result['submenu_type'],
                    'content_width' => $result['content_width'],
                    'content_type' => $result['content_type'],
                    'content' => unserialize($result['content'])
                );
            }
            return $data;
        }
        return false;
    }

    public function getCategories($array = array()):string {
        $output = '';      
        if(is_array($array) && !empty($array) && count($array)>0) {
            foreach($array as $row) {                
                $output .= '<li class="dd-item" data-id="'.$row['id'].'" data-name="'.$row['name'].'">';
                $output .= '<a class="fa fa-times"></a>';
                $output .= '<div class="dd-handle">'.$row['name'].'</div>';
                if(isset($row['children'])) {
                    if(!empty($row['children'])) {
                        $output .= $this->getCategoriesChildren($row['children']);
                    }
                }
                $output .= '</li>';
            }
        }
        return $output;
    }

    public function getCategoriesChildren($array = array()) {
        $output = '';
        $output .= '<ol class="dd-list">';
        foreach($array as $row) {
            $output .= '<li class="dd-item" data-id="'.$row['id'].'" data-name="'.$row['name'].'">';
            $output .= '<a class="fa fa-times"></a>';
            $output .= '<div class="dd-handle">'.$row['name'].'</div>';
            if(isset($row['children'])) {
                if(!empty($row['children'])) {
                    $output .= $this->getCategoriesChildren($row['children']);
                }
            }
            $output .= '</li>';
        }
        $output .= '</ol>';
        return $output;
    }

    public function displayError() {
        $errors = '';
        foreach ($this->errors as $error) {
            $errors .= '<div>'.$error.'</div>';
        }
        return $errors;
    }

    public function install($module_id) {
        if($this->is_table_exist(DB_PREFIX . "themability_mega_menu")) {
            $query = $this->db->query("
                CREATE TABLE IF NOT EXISTS `".DB_PREFIX."themability_mega_menu` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `parent_id` int(11) NOT NULL,
                    `rang` int(11) NOT NULL,
                    `icon` varchar(255) NOT NULL DEFAULT '',
                    `name` text,
                    `type_link` int(11),
                    `module_id` int(11),
                    `link` text,
                    `description` text,
                    `new_window` int(11) NOT NULL DEFAULT '0',
                    `status` int(11) NOT NULL DEFAULT '0',
                    `position` int(11) NOT NULL DEFAULT '0',
                    `submenu_width` text,
                    `submenu_type` int(11) NOT NULL DEFAULT '0',
                    `content_width` int(11) NOT NULL DEFAULT '12',
                    `content_type` int(11) NOT NULL DEFAULT '0',
                    `content` text,
                    `label_item` varchar(255) NOT NULL DEFAULT '',
                    `icon_font` varchar(255) NOT NULL DEFAULT '',
                    `class_menu` varchar(255),
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
            ");
            $query = $this->db->query("
                INSERT INTO `".DB_PREFIX."themability_mega_menu` (`id`, `parent_id`, `rang`, `icon`, `name`, `type_link`, `module_id`, `link`, `description`, `new_window`, `status`, `position`, `submenu_width`, `submenu_type`, `content_width`, `content_type`, `content`, `label_item`, `icon_font`, `class_menu`) VALUES (99, 0, 7, 'no_image.png', 'a:2:{i:1;s:11:\"Accessories\";i:3;s:11:\"Accessories\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 1, 0, '250px', 1, 3, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', 'hot', '', ''),
            (100, 0, 0, 'no_image.png', 'a:2:{i:1;s:4:\"Shop\";i:3;s:4:\"Shop\";}', 1, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '900px', 1, 4, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:1:{i:0;a:3:{s:4:\"name\";s:11:\"Bath & Body\";s:2:\"id\";i:33;s:8:\"children\";a:2:{i:0;a:2:{s:4:\"name\";s:24:\"Chanel  >  Blushers \";s:2:\"id\";i:39;}i:1;a:2:{s:4:\"name\";s:27:\"Chanel  >  Makeup Bases\";s:2:\"id\";i:38;}}}}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (102, 0, 18, 'no_image.png', 'a:1:{i:1;s:4:\"Blog\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:74:\"index.php?route=extension/themability4blogger/bloginfo/themability_blogger\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:1:{i:1;s:0:\"\";}', 0, 0, 0, '100%', 0, 4, 2, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:1:{i:1;s:0:\"\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:18:\"image/no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (103, 125, 17, 'no_image.png', 'a:2:{i:1;s:12:\"Manufacturer\";i:3;s:12:\"Manufacturer\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '', 0, 12, 3, 'a:7:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:11:\"manufacture\";a:2:{s:4:\"name\";a:5:{i:0;s:5:\"Apple\";i:1;s:5:\"Canon\";i:2;s:3:\"HTC\";i:3;s:4:\"Palm\";i:4;s:15:\"Hewlett-Packard\";}s:2:\"id\";a:5:{i:0;s:1:\"8\";i:1;s:1:\"9\";i:2;s:1:\"5\";i:3;s:1:\"6\";i:4;s:1:\"7\";}}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (106, 100, 1, 'no_image.png', 'a:2:{i:1;s:18:\"Categories visible\";i:3;s:18:\"Categories visible\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 12, 2, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:4:{i:0;a:3:{s:4:\"name\";s:6:\"Chanel\";s:2:\"id\";i:34;s:8:\"children\";a:7:{i:0;a:2:{s:4:\"name\";s:22:\"Chanel  >  alovera\";s:2:\"id\";i:53;}i:1;a:2:{s:4:\"name\";s:24:\"Chanel  >  Blushers \";s:2:\"id\";i:39;}i:2;a:2:{s:4:\"name\";s:23:\"Chanel  >  Eyeliner\";s:2:\"id\";i:52;}i:3;a:2:{s:4:\"name\";s:24:\"Chanel  >  eyeshadow\";s:2:\"id\";i:54;}i:4;a:2:{s:4:\"name\";s:28:\"Chanel  >  Setting Spray\";s:2:\"id\";i:47;}i:5;a:2:{s:4:\"name\";s:26:\"Chanel  >  highlighter\";s:2:\"id\";i:51;}i:6;a:2:{s:4:\"name\";s:24:\"Chanel  >  Lip color\";s:2:\"id\";i:50;}}}i:1;a:3:{s:4:\"name\";s:9:\"skin care\";s:2:\"id\";i:17;s:8:\"children\";a:7:{i:0;a:2:{s:4:\"name\";s:22:\"Chanel  >  Sachets\";s:2:\"id\";i:40;}i:1;a:2:{s:4:\"name\";s:23:\"Chanel  >  Perfumes\";s:2:\"id\";i:41;}i:2;a:2:{s:4:\"name\";s:26:\"Chanel  >  Nail Polish\";s:2:\"id\";i:56;}i:3;a:2:{s:4:\"name\";s:27:\"Chanel  >  Moisturizing\";s:2:\"id\";i:23;}i:4;a:2:{s:4:\"name\";s:22:\"Chanel  >  Mascara\";s:2:\"id\";i:42;}i:5;a:2:{s:4:\"name\";s:27:\"Chanel  >  makeup fixer\";s:2:\"id\";i:44;}i:6;a:2:{s:4:\"name\";s:27:\"Chanel  >  Makeup Bases\";s:2:\"id\";i:38;}}}i:2;a:3:{s:4:\"name\";s:7:\"make up\";s:2:\"id\";i:20;s:8:\"children\";a:7:{i:0;a:2:{s:4:\"name\";s:19:\"make up  >  Mac\";s:2:\"id\";i:27;}i:1;a:2:{s:4:\"name\";s:18:\"make up  >  PC\";s:2:\"id\";i:26;}i:2;a:2:{s:4:\"name\";s:39:\"Oriflame  >  Creams  >  Powders\";s:2:\"id\";i:62;}i:3;a:2:{s:4:\"name\";s:40:\"Oriflame  >  Macs  >  Eye Makeup\";s:2:\"id\";i:61;}i:4;a:2:{s:4:\"name\";s:40:\"Oriflame  >  Macs  >  Eye Shadow\";s:2:\"id\";i:60;}i:5;a:2:{s:4:\"name\";s:41:\"Oriflame  >  Oils  >  Bubble Bath\";s:2:\"id\";i:67;}i:6;a:2:{s:4:\"name\";s:38:\"Oriflame  >  Oils  >  Shampoos\";s:2:\"id\";i:65;}}}i:3;a:3:{s:4:\"name\";s:8:\"Oriflame\";s:2:\"id\";i:18;s:8:\"children\";a:8:{i:0;a:2:{s:4:\"name\";s:23:\"Oriflame  >  Creams\";s:2:\"id\";i:45;}i:1;a:2:{s:4:\"name\";s:21:\"Oriflame  >  Macs\";s:2:\"id\";i:46;}i:2;a:2:{s:4:\"name\";s:21:\"Oriflame  >  Oils\";s:2:\"id\";i:64;}i:3;a:2:{s:4:\"name\";s:38:\"Oriflame  >  Creams  >  Henkel\";s:2:\"id\";i:63;}i:4;a:2:{s:4:\"name\";s:39:\"Oriflame  >  Creams  >  Lotions\";s:2:\"id\";i:58;}i:5;a:2:{s:4:\"name\";s:38:\"Oriflame  >  Macs  >  Eyeliner\";s:2:\"id\";i:59;}i:6;a:2:{s:4:\"name\";s:40:\"Oriflame  >  Oils  >  Eye Lotion\";s:2:\"id\";i:68;}i:7;a:2:{s:4:\"name\";s:23:\"Chanel  >  Lipstick\";s:2:\"id\";i:37;}}}}s:7:\"columns\";s:1:\"4\";s:7:\"submenu\";s:1:\"2\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', 'hot'),
            (107, 99, 8, 'no_image.png', 'a:2:{i:1;s:10:\"categories\";i:3;s:10:\"categories\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '', 1, 12, 2, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:5:{i:0;a:2:{s:4:\"name\";s:7:\"Alticor\";s:2:\"id\";i:24;}i:1;a:3:{s:4:\"name\";s:9:\"hair care\";s:2:\"id\";i:25;s:8:\"children\";a:5:{i:0;a:2:{s:4:\"name\";s:28:\"hair care  >  bobi brown\";s:2:\"id\";i:32;}i:1;a:2:{s:4:\"name\";s:28:\"hair care  >  Fragrance \";s:2:\"id\";i:31;}i:2;a:2:{s:4:\"name\";s:26:\"hair care  >  L\'Oréal\";s:2:\"id\";i:29;}i:3;a:3:{s:4:\"name\";s:21:\"hair care  >  Mac\";s:2:\"id\";i:28;s:8:\"children\";a:2:{i:0;a:2:{s:4:\"name\";s:38:\"hair care  >  Mac  >  Johnson \";s:2:\"id\";i:36;}i:1;a:2:{s:4:\"name\";s:42:\"hair care  >  Mac  >  Moisturizing\";s:2:\"id\";i:35;}}}i:4;a:2:{s:4:\"name\";s:24:\"hair care  >  revlon\";s:2:\"id\";i:30;}}}i:2;a:2:{s:4:\"name\";s:11:\"Bath & Body\";s:2:\"id\";i:33;}i:3;a:2:{s:4:\"name\";s:7:\"make up\";s:2:\"id\";i:20;}i:4;a:2:{s:4:\"name\";s:8:\"Mary Kay\";s:2:\"id\";i:57;}}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (119, 100, 2, 'no_image.png', 'a:1:{i:1;s:7:\"banners\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:1:{i:1;s:0:\"\";}', 0, 0, 0, '100%', 2, 12, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:1:{i:1;s:483:\"&lt;div class=&quot;row&quot;&gt;\r\n&lt;div class=&quot;col-sm-6&quot;&gt;\r\n&lt;div class=&quot;link&quot;&gt;&lt;img alt=&quot;&quot; class=&quot;img-responsive mw-100&quot; src=&quot;image/catalog/megamenu/1.png&quot; /&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n\r\n&lt;div class=&quot;col-sm-6&quot;&gt;\r\n&lt;div class=&quot;link&quot;&gt;&lt;img alt=&quot;&quot; class=&quot;img-responsive mw-100&quot; src=&quot;image/catalog/megamenu/2.png&quot; /&gt;&lt;/div&gt;\r\n&lt;/div&gt;\r\n&lt;/div&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"0\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (120, 121, 11, 'no_image.png', 'a:2:{i:1;s:15:\"Popular Product\";i:3;s:15:\"Popular Product\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 4, 6, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:1:\"3\";s:4:\"type\";s:7:\"popular\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (121, 0, 10, 'no_image.png', 'a:2:{i:1;s:7:\"Product\";i:3;s:7:\"Product\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '900px', 1, 4, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', 'hot', '', ''),
            (125, 0, 16, 'no_image.png', 'a:2:{i:1;s:5:\"brand\";i:3;s:5:\"brand\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '250px', 1, 4, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:23:\"catalog/profile-pic.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (138, 0, 14, 'no_image.png', 'a:2:{i:1;s:5:\"Pages\";i:3;s:5:\"Pages\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '250px', 1, 4, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"0\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:1:\"4\";s:13:\"limit_level_2\";s:1:\"4\";s:13:\"limit_level_3\";s:1:\"4\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"0\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (141, 0, 3, 'no_image.png', 'a:2:{i:1;s:10:\"Categories\";i:3;s:10:\"Categories\";}', 1, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:2:\"25\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '900px', 1, 4, 0, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', 'offer', '', ''),
            (142, 141, 6, 'no_image.png', 'a:2:{i:1;s:18:\"Categories visible\";i:3;s:0:\"\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '50%', 0, 4, 5, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:19:\"a:1:{i:0;s:2:\"18\";}\";s:13:\"limit_level_1\";s:1:\"8\";s:13:\"limit_level_2\";s:1:\"8\";s:13:\"limit_level_3\";s:1:\"8\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"2\";s:7:\"submenu\";s:1:\"2\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (143, 141, 4, 'no_image.png', 'a:2:{i:1;s:15:\"Special Product\";i:3;s:0:\"\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 4, 6, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:2:\"45\";s:4:\"name\";s:11:\"MacBook Pro\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:1:\"3\";s:4:\"type\";s:7:\"special\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (145, 121, 12, 'no_image.png', 'a:2:{i:1;s:18:\"Bestseller Product\";i:3;s:18:\"Bestseller Product\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 4, 6, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:1:\"3\";s:4:\"type\";s:10:\"bestseller\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (147, 121, 13, 'no_image.png', 'a:2:{i:1;s:14:\"Latest Product\";i:3;s:14:\"Latest Product\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 4, 6, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:0:\"\";s:13:\"limit_level_2\";s:0:\"\";s:13:\"limit_level_3\";s:0:\"\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:1:\"3\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', ''),
            (148, 141, 5, 'no_image.png', 'a:2:{i:1;s:11:\"subcategory\";i:3;s:0:\"\";}', 1, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:2:\"25\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '50%', 0, 4, 5, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:19:\"a:1:{i:0;s:2:\"25\";}\";s:13:\"limit_level_1\";s:1:\"5\";s:13:\"limit_level_2\";s:1:\"5\";s:13:\"limit_level_3\";s:1:\"5\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"2\";s:15:\"submenu_columns\";s:1:\"1\";}}', 'hot', '', ''),
            (151, 138, 15, 'no_image.png', 'a:2:{i:1;s:11:\"page layout\";i:3;s:11:\"page layout\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '100%', 0, 12, 7, 'a:7:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"0\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:17:\"a:1:{i:0;s:0:\"\";}\";s:13:\"limit_level_1\";s:1:\"4\";s:13:\"limit_level_2\";s:1:\"4\";s:13:\"limit_level_3\";s:1:\"4\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"0\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:0:{}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:5:\"route\";a:3:{s:2:\"id\";a:6:{i:0;s:13:\"information_4\";i:1;s:7:\"page_11\";i:2;s:7:\"page_12\";i:3;s:6:\"page_8\";i:4;s:6:\"page_1\";i:5;s:6:\"page_5\";}s:4:\"name\";a:6:{i:0;s:8:\"About Us\";i:1;s:10:\"Contact Us\";i:2;s:8:\"Site Map\";i:3;s:7:\"Special\";i:4;s:10:\"My Account\";i:5;s:13:\"Order History\";}s:4:\"link\";a:6:{i:0;s:44:\"information/information&amp;information_id=4\";i:1;s:19:\"information/contact\";i:2;s:19:\"information/sitemap\";i:3;s:15:\"product/special\";i:4;s:15:\"account/account\";i:5;s:13:\"account/order\";}}}', '', '', ''),
            (153, 99, 9, 'no_image.png', 'a:2:{i:1;s:10:\"categories\";i:3;s:10:\"categories\";}', 0, ".$module_id.", 'a:3:{s:3:\"url\";s:0:\"\";s:8:\"category\";s:0:\"\";s:9:\"route_url\";s:0:\"\";}', 'a:2:{i:1;s:0:\"\";i:3;s:0:\"\";}', 0, 0, 0, '', 1, 12, 5, 'a:6:{s:4:\"html\";a:1:{s:4:\"text\";a:2:{i:1;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";i:3;s:29:\"&lt;p&gt;&lt;br&gt;&lt;/p&gt;\";}}s:7:\"product\";a:2:{s:2:\"id\";s:0:\"\";s:4:\"name\";s:0:\"\";}s:5:\"image\";a:2:{s:4:\"link\";s:12:\"no_image.png\";s:10:\"show_title\";s:1:\"1\";}s:11:\"subcategory\";a:9:{s:8:\"category\";s:19:\"a:1:{i:0;s:2:\"18\";}\";s:13:\"limit_level_1\";s:1:\"5\";s:13:\"limit_level_2\";s:1:\"5\";s:13:\"limit_level_3\";s:1:\"5\";s:10:\"show_title\";s:1:\"1\";s:10:\"show_image\";s:1:\"1\";s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}s:11:\"productlist\";a:4:{s:5:\"limit\";s:0:\"\";s:4:\"type\";s:3:\"new\";s:10:\"show_title\";s:1:\"1\";s:3:\"col\";s:1:\"1\";}s:10:\"categories\";a:4:{s:10:\"categories\";a:4:{i:0;a:3:{s:4:\"name\";s:9:\"hair care\";s:2:\"id\";i:25;s:8:\"children\";a:5:{i:0;a:2:{s:4:\"name\";s:28:\"hair care  >  bobi brown\";s:2:\"id\";i:32;}i:1;a:2:{s:4:\"name\";s:28:\"hair care  >  Fragrance \";s:2:\"id\";i:31;}i:2;a:2:{s:4:\"name\";s:26:\"hair care  >  L\'Oréal\";s:2:\"id\";i:29;}i:3;a:3:{s:4:\"name\";s:21:\"hair care  >  Mac\";s:2:\"id\";i:28;s:8:\"children\";a:2:{i:0;a:2:{s:4:\"name\";s:38:\"hair care  >  Mac  >  Johnson \";s:2:\"id\";i:36;}i:1;a:2:{s:4:\"name\";s:42:\"hair care  >  Mac  >  Moisturizing\";s:2:\"id\";i:35;}}}i:4;a:2:{s:4:\"name\";s:24:\"hair care  >  revlon\";s:2:\"id\";i:30;}}}i:1;a:2:{s:4:\"name\";s:11:\"Bath & Body\";s:2:\"id\";i:33;}i:2;a:2:{s:4:\"name\";s:7:\"make up\";s:2:\"id\";i:20;}i:3;a:2:{s:4:\"name\";s:8:\"Mary Kay\";s:2:\"id\";i:57;}}s:7:\"columns\";s:1:\"1\";s:7:\"submenu\";s:1:\"1\";s:15:\"submenu_columns\";s:1:\"1\";}}', '', '', '')

            ");
        }
        return false;
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "themability_mega_menu`");
    }

    public function skrut($c,$d) {
        if(strlen($c) > $d) {
            $ciag = substr($c,0,$d);
            $ciag .="...";
            return $ciag;
        } else {
            return $c;
        }
    }

    public function is_table_exist($table){
        $query = $this->db->query("SHOW TABLES LIKE '".$table."'");
        if( count($query->rows) <= 0 ) {
            return true;
        }
        return false;
    }
    public function getModuleId() {
        $sql = " SHOW TABLE STATUS LIKE '" . DB_PREFIX . "module'" ;
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function duplicateModule($module_id,$import_module){
        $parent_menu = $this->getMenuByIdModule($import_module);
        if($parent_menu){
            foreach ($parent_menu as $menu) {
                $dane = $this->model_extension_themability4megamenu_module_themability_megamenu->getMenu(intval($menu['id']));
                $dane['module_id'] = $module_id;
                $id_parent_add = $this->model_extension_themability4megamenu_module_themability_megamenu->addMenu($dane);
                $subcategories = $this->model_extension_themability4megamenu_module_themability_megamenu->getSubMenu(intval($menu['id']));
                if($subcategories){
                foreach ($subcategories as $result) {
                    $data = array(
                            'parent_id' => $id_parent_add,
                            'name' => unserialize($result['name']),
                            'description' => unserialize($result['description']),
                            'icon' => $result['icon'],
                            'module_id' => $module_id,
                            'link' => $result['link'],
                            'type_link' => $result['type_link'],
                            'new_window' => $result['new_window'],
                            'status' => $result['status'],
                            'position' => $result['position'],
                            'submenu_width' => $result['submenu_width'],
                            'display_submenu' => $result['submenu_type'],
                            'content_width' => $result['content_width'],
                            'content_type' => $result['content_type'],
                            'content' => unserialize($result['content']),
                            'list_categories' => (isset($result['content']['categories']['categories']) && $result['content']['categories']['categories']) ? $this->model_extension_themability4megamenu_module_themability_megamenu->getCategories(unserialize($result['content']['categories']['categories'])) : ''
                        );
                        $this->model_extension_themability4megamenu_module_themability_megamenu->addMenu($data);
                    }
                }
            }
        }
    }

    public function getMenuByIdModule($module_id){
        $query = $this->db->query("SELECT * FROM ".DB_PREFIX."themability_mega_menu WHERE  module_id = '".$module_id."' AND parent_id=0");
        return $query->rows;
    }
    
    public function duplicateMenu($id_duplicate){
        $dane = $this->model_extension_themability4megamenu_module_themability_megamenu->getMenu(intval($id_duplicate));
        $id_parent_add = $this->model_extension_themability4megamenu_module_themability_megamenu->addMenu($dane);
        $subcategories = $this->model_extension_themability4megamenu_module_themability_megamenu->getSubMenu(intval($id_duplicate));
        if($subcategories){
            foreach ($subcategories as $result) {
                $data = array(
                    'parent_id' => $id_parent_add,
                    'name' => unserialize($result['name']),
                    'description' => unserialize($result['description']),
                    'icon' => $result['icon'],
                    'link' => $result['link'],
                    'type_link' => $result['type_link'],
                    'new_window' => $result['new_window'],
                    'status' => $result['status'],
                    'position' => $result['position'],
                    'submenu_width' => $result['submenu_width'],
                    'display_submenu' => $result['submenu_type'],
                    'content_width' => $result['content_width'],
                    'content_type' => $result['content_type'],
                    'content' => unserialize($result['content']),
                    'list_categories' => (isset($result['content']['categories']['categories']) && $result['content']['categories']['categories']) ? $this->model_extension_themability4megamenu_module_themability_megamenu->getCategories(unserialize($result['content']['categories']['categories'])) : ''
                );
                $this->model_extension_themability4megamenu_module_themability_megamenu->addMenu($data);
            }
        }
        
        return $id_parent_add;
    }
}
?>
