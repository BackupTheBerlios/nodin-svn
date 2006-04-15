<?php
class ContentManager {
    public static function createContent($class, $content) {
        $db = PluginManager::getInstance()->getPlugin('creole')->getC();
        $db->executeQuery('INSERT INTO content (class, content) VALUES (\'' . $class . '\', \'' . $content . '\')');
    }
    
    public static function getContentById($id) {
        $db = PluginManager::getInstance()->getPlugin('creole')->getC();
        if($id == -1) {
            $rs = $db->executeQuery('SELECT `object_id` FROM tree WHERE `left`="1"');
            while($rs->next()) {
                $id = $rs->getInt('object_id');
            }
        } else {
            preg_match('/\/(.*)/', $id, $matches);
            $id = $matches[1];
        }
        $rs = $db->executeQuery('SELECT o.*, c.class_name FROM objects o, classes c WHERE o.id="' . $id . '" AND o.class = c.id');
        return $rs;
    }
}
?>