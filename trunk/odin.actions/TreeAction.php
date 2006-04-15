<?php
class TreeAction implements IAction {
	public function perform(IHttpContext $context, $params = null) {		
		$tree = new Tree();
		$db = PluginManager::getInstance()->getPlugin('creole')->getC();
		if($params[0] == null) {
			$rs = $db->executeQuery(
			'SELECT t.object_id, o.* FROM tree t, objects o WHERE t.object_id = o.id AND depth = 2'
			);
			$ret = array();
			while($rs->next()) {
				$ret[] = $rs->getRow();
			}			
			return array('result' => $ret, 'type' => 'cat');
		} else {
			if($tree->hasChilds($params[0])) {
				$rs = $db->executeQuery('SELECT o.*
								     FROM objects o
									 LEFT JOIN tree t ON ( t.object_id = o.id )
									 WHERE t.parent_id =' . $params[0]);
				$ret = array();
				while($rs->next()) {
					$ret[] = $rs->getRow();
				}			
				return array('result' => $ret, 'type' => 'titles');
			} else {
				$rs = $db->executeQuery('SELECT o.*, u.FirstName, u.LastName
								     FROM objects o
									 LEFT JOIN tree t ON ( t.object_id = o.id )
									 LEFT JOIN lum_user u ON (o.user_id = u.id)
									 WHERE t.object_id =' . $params[0]);
				$ret = array();
				while($rs->next()) {
					$ret = $rs->getRow();
				}
				return array('result' => $ret, 'type' => 'text');
			}
			
		}		
	}
}
?>