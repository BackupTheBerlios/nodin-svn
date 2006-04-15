<?php
/**
 * Config class
 *
 */
class Config implements ArrayAccess {
	private static $instance;
	public static function getInstance($sConfig = null) {
        if(self::$instance == null) {
            self::$instance = new self($sConfig);
        }
        return self::$instance;
    }
    
    private function __construct($sConfig = null) {
        $cp = new ConfigParser($sConfig);
        $this->aConfig = $cp->parse();
    }

// --- ArrayAccess --------------------------------------------------------
	
    public function offsetExists($offset) {
        return isset($this->aConfig[$offset]);
    }    
    
    public function offsetGet($offset) {
        return $this->aConfig[$offset];
    }
    
    public function offsetSet($offset, $set) {
        return false;
    }
    
    public function offsetUnset($offset) {
        return false;
    }
}
?>
