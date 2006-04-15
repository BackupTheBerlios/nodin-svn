<?php
/**
 * PluginManager
 * Loads plugins
 */
class PluginManager {
    /**
     * @var array plugins
     */
    private $plugins = array();
    private $pluginsMap = array();
    private $config;
    /**
     * @var PluginManager instancja
     * @static
     */
    private static $instance;
    
    /**
     * Singleton
     * @static
     */
    public static function getInstance() { 
        if(self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance; 
    }
    
    /**
     * Konstruktor
     */
    private function __construct() {
        $this->config = Config::getInstance();
        $this->pluginsMap = $this->config['plugins'];
        
    }

    /**
     * @return plugin
     * @param string plugin name
     */
    public function getPlugin($name) {
        if(isset($this->plugins[$name])) {
            return $this->plugins[$name];        
        }  elseif(isset($this->pluginsMap[$name])) {
            $params = isset($this->pluginsMap[$name]['params']) ? $this->pluginsMap[$name]['params'] : null;
            $class = $this->pluginsMap[$name]['class'];
            $this->plugins[$name] = new $class($params);
            Logger::debug('Loaded Plugin: ' . $class);
        } else {
            throw new PluginNotFoundException();
          }
          return $this->plugins[$name];
    }    
}
?>
