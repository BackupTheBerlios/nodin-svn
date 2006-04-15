<?php
class Tree {
    /**
     * Instance of Creole
     */
    private $db;
    
    /**
     * Constructor, initialize Creole
     */
    public function __construct() {
        $this->db = PluginManager::getInstance()->getPlugin('creole')->getC();
    }
    
    /**
     * Adds node to tree
     *
     * @param object_id Id of object in system (this is not db id)
     * @param parent Id of parent - db id
     */
    public function addNode($object_id, $parent) {
        $rs = $this->db->executeQuery('SELECT `id`, `left`, `right`, `depth` FROM tree WHERE `id`="' . $parent . '"');
        if($rs->getRecordCount() == 0) {
            $left = 1;
            $right = 2;
        } else {
            while($rs->next()) {
                $left = $rs->getInt('left');
                $right = $rs->getInt('right');
                $parentId = $rs->getInt('id');
                $depth = $rs->getInt('depth');
            }
        }
        $depth++;
        $sql = 'UPDATE tree SET `right` = `right` +2 WHERE `right` >' . ($right - 1);
        $this->db->executeQuery($sql);
        
        $sql = 'UPDATE tree SET `left` = `left` +2 WHERE `left` > '. ($right - 1);
        $this->db->executeQuery($sql);
        
        $sql = 'INSERT INTO tree SET `left`=' . ($right - 1) .', `right`=' . $right . ', `object_id`="'. $object_id .'", `parent_id`="' . $parentId .'", `depth` = "' . $depth . '";';
        $this->db->executeQuery($sql);
    }
    
    /**
     * Adds root node
     *
     * @param object_id Id of object in system (this is not db id)
     * @return boolean
     */
    public function addRoot($object_id) {
        $rs = $this->db->executeQuery('SELECT * FROM `tree` WHERE `parent_id` ="0"');
        if($rs->getRecordCount() == 0) {
            $this->db->executeQuery('INSERT INTO `tree` ( `id` , `object_id` , `left` , `right` ) VALUES ( \'0\', \'' . $object_id . '\', \'1\', \'2\')');
            return true;
        }
        return false;
    }
    
    /**
     * Removes node
     *
     * @param $id Id of node
     */
    public function deleteNode($id) {
        $rs = $this->db->executeQuery('SELECT `parent_id`, `left` FROM `tree` WHERE `id`="'. $id .'"');
        while($rs->next()) {
            $node = array();
            $node['parent'] = $rs->getInt('parent_id');
            $node['left'] = $rs->getInt('left');
        }
        
        $this->db->executeQuery('DELETE FROM `tree` WHERE `id`="' . $id . '" LIMIT 1');
        $this->rebuild($node['parent'], $node['left'] - 1);
    }
    
    /**
     * Rebuilds tree recursively
     * If something goes wrong, you'll use this
     *
     * @param $parent Id of parent node
     * @param $left Left
     */
    public function rebuild($parent, $left) {
        $right = $left+1;

        $rs = $this->db->executeQuery('SELECT `id` FROM `tree` WHERE `parent_id`="' . $parent . '";');
        while ($rs->next()) {
            $right = $this->rebuild($rs->getInt('id'), $right);
        }
        $this->db->executeQuery('UPDATE tree SET `left`=' . $left . ', `right`=' . $right . ' WHERE `id`="' . $parent . '";');
        return $right+1;
    }
    
    /**
     * Test method
     *
     * @param $root Id of node which is root to display
     */
    function display($root) {
        $rs = $this->db->executeQuery('SELECT `left`, `right` FROM `tree` WHERE `id`="'.$root.'";');
        while($rs->next()) {
            $l = $rs->getInt('left');
            $r = $rs->getInt('right');
        }

        $right = array();

        $rs = $this->db->executeQuery('SELECT `object_id`, `left`, `right` FROM tree WHERE `left` BETWEEN '. $l .' AND ' . $r . ' ORDER BY `left` ASC;');
        while ($rs->next()) {
            if (count($right)>0) {
               while ($right[count($right)-1] < $rs->getInt('right')) {
                   array_pop($right);
               }
           }
           echo str_repeat('&nbsp;&nbsp;&nbsp;',count($right)) . $rs->getString('object_id'). ' Left: ' . $rs->getInt('left') . ' Right: '. $rs->getInt('right') . "<br/>\n";

           $right[] = $rs->getInt('right');
       }
    }
    
    /**
     * Gets object id
     *
     * @param id of node
     * @return object id
     */
    public function getNode($id) {
        $rs = $this->db->executeQuery('SELECT `object_id` FROM `tree` WHERE `id`="'.$id.'";');
        while($rs->next()) {
            return $rs->getInt('object_id');
        }
    }
    
    /**
     * Checks if have childs
     * 
     * @param int id
     * @return booelan
     */
    public function hasChilds($id) {
    	$rs = $this->db->executeQuery('SELECT `left`, `right` FROM tree WHERE object_id=' . $id);
    	while ($rs->next()) {
    		$left = $rs->getInt('left');
    		$right = $rs->getInt('right');
    	}
    	if(($right - $left) == 1) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * Returns nodes where depth equals parameter
     * 
     * @param int depth
     * @return Result
     */
    public function getByDepth($depth) {
    	return $this->db->executeQuery('SELECT `object_id` FROM tree WHERE depth=' . $depth);    	
    }
}
?>
