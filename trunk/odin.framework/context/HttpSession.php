<?php
class HttpSession implements IHttpSession {
    private static $instance;

    public function __construct() {
    }
    
    public function get($name) {
        if(isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return false;
    }
    
    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }
}
?>
