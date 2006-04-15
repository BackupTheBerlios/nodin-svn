<?php
define('IN_ODIN', 1);

/**
 * Main Odin class
 * @author Piotr 'bela_666' Belina
 * @version $Id$
 */
class Odin {
	/**
	 * Instance of Config
	 *
	 * @access private
	 * @var Config	 
	 */
    private $config;
    
    /**
     * Instance of PluginManager
     * 
     * @access private
     * @var PluginManager
     */
    private $pluginManager;
    
    /**
     * Filename of configuration file
     * 
     * @access private
     * @var string
     */
    private $sConfig;

    /**
     * Instance of User
     *
     * @access private
     * @var User
     */
    private $user;
    
    /**
     * Instance of IContext
     *
     * @access private
     * @var IContext
     */
    private $context;
    
    /**
     * Information about actions to perform
     *
     * @access private
     * @var array
     */
    private $data;
    
    /**
     * Instance of Odin
     *
     * @access private
     * @staticvar  
     * @var Odin
     */
    private static $instance;
    
    /**
     * Singleton
     *     
     * @return Odin
     * @static 
     */
    public static function getInstance() {
    	if(self::$instance != null) {
    		return self::$instance;
    	}
    }
    
    /**
     * Constructor
     * Whole dirty job
     * 
     * @param string Config file localization
     * @param IHttpContext Context
     * @access public
     */
    public function __construct($config, IHttpContext $context) {
    	self::$instance = $this;        
        //set_error_handler(array(new SimpleErrorHandler(), 'handle'));        
        $this->context = $context;
        $this->sConfig = $config;
        $this->initialize($context);
        $this->data = $this->pluginManager->getPlugin('router')->getData($context);        
        $result = $this->run();                
        ob_start();        
        $this->display($result);        
        $this->stop();                    
        ob_end_flush();
    }
    
    /**
     * Adds action to quene
     *
     * @access private
     * @param string Action name
     */
    public function addAction($actionName) {
    	array_unshift($this->data['actions'], $actionName);
    }
    
    /**
     * Initialization of framework
     * Creates needed classes
     * 
     * @access private
     * @param IHttpContext Context
     */
    private function initialize(IHttpContext $context) {
        new Logger();
        $this->user = User::getInstance($context);
        Logger::debug('Odin Init');
        $this->config = Config::getInstance($this->sConfig);
        SystemI18N::init();
        $this->pluginManager = PluginManager::getInstance();        
    }
    
    /**
     * Stops framework and sends session data to user
     * 
     * @access private
     */
    private function stop() {
        Logger::debug('End');
        $this->user->close();
    }
    
    /**
     * Executes all actions
     * 
     * @access private
     * @return array Data to display
     */
    private function run() {
        $result = array();
        while(count($this->data['actions']) > 0) {        	
        	$action = array_shift($this->data['actions']);        	        	        
        	$result[$action] = $this->perform(array($action, $this->data['params']), $this->data['filters'][$action]);
        }
        return $result;        
    }

    /**
     * Performs action
     * 
     * @access private
     */
    private function perform($action, $filters) {		
        $fc = new FilterChain();
        $fc->addFilters($filters);
        
        $aw = new ActionWrapper($action[0], $action[1]);        
        $fc->process($this->context, $aw);
        
        return $aw->mResult;
    }
    
    /**
     * Sends data to ViewManager
     * 
     * @access private
     * @param array Result
     */
    private function display($result) {
        $request = $this->pluginManager->getPlugin('router')->getRequest();
        $view = $request['view'];
        $vm = new ViewManager($view, $result, $this->user);
    }    
}
?>
