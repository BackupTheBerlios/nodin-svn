<?php
class Authentication {
    private $db;
    private $logged;
    public function __construct (){
        if(isset($_SESSION['odinUserLogged'])) {
            Logger::getLogger('odin')->debug('User zalogowany');
        }
        $this->db = PluginManager::getInstance()->getPlugin('creole')->getC();
    }
    
    public function login($username, $password) {
        $rs = $this->db->executeQuery('SELECT id, name, password FROM LUM_User WHERE name=\'' . $username . '\'');
        while($rs->next()) {
            if(md5($password) == $rs->getString('password')) {
                return array('id' => $rs->getInt('id'), 'name' => $rs->getString('name'));
            } else {
                return false;
            }
        }
    }
}
?>
