<?php
/**
 * Config parser class
 *  
 */
class ConfigParser {
    private $aConfig;
    private $sConfig;
    private $sNotParsedConfig;
    private $intermediate;    

    public function __construct($sConfig = null) {
        $this->sConfig = $sConfig;        
    }

// --- parsing ------------------------------------------------------------

    public function parse() {
    	if(!Cache::isCached($this->sConfig, $this->sConfig)) {
            $this->sNotParsedConfig = file_get_contents($this->sConfig);
            $this->intermediate = simplexml_load_string($this->sNotParsedConfig);
            $this->parseActions();
        	$this->parseRequest();
        	$this->parsePlugins();
        	$this->parseFilters();
            Cache::writeCache($sConfig, serialize($this->aConfig));
            Logger::debug('Config: zapisano cache');
        } else {
            $this->aConfig = unserialize(Cache::get($this->sConfig));
            Logger::debug('Config: odczytano cache');            
        }
        return $this->aConfig;        
    }
    
    private function parseActions() {
        $config = $this->intermediate;
        $tmp = array();
        foreach($config->actions->action as $action) {
        	// checkin action->filters
        	$filters = array();
            if(isset($action->filters)) {
            	foreach($action->filters->filter as $filter) {
            		$filters[] = (string)$filter['name'];
				}
            }
        	
            // checkin action->name
            if(isset($action->name)) {
                $name = (string)$action->name;
                unset($action->name);
                if(empty($name)) {
                    throw new ParameterEmptyException('config->actions->action->name is empty');
                }
            } else {
                throw new ParameterNotIssetException('config->actions->action->name is not isset');
            }
            
            // checking action->type
            if(isset($action->type)) {
                $type = (string)$action->type;
                if(empty($type)) {
                    throw new ParameterEmptyException('config->actions->'. $name . '->type is empty');
                }
            } else {
                throw new ParameterNotIssetException('config->actions->'. $name . '->type is not isset');
            }
            
            // checking action->roles
            if(isset($action->roles)) {
                $roles = (string)$action->roles;
                if($roles == '') {
                    $roles = array();
                } else {
                    $roles = explode(' ', $roles);
                }
            } else {
                throw new ParameterNotIssetException('config->actions->'. $name . '->roles is not isset');
            }            
            $tmp[$name] = array('type' => $type, 'roles' => $roles, 'filters' => $filters);
            isset($action->fallback) ? $tmp[$name]['fallback'] = (string)$action->fallback : null;            
        }
        $this->aConfig['actions'] = $tmp;
    }
    
    private function parseRequest() {
        $config = $this->intermediate;
        $tmp = array();
        foreach($config->request as $key => $request) {
            $actions = array();            
            foreach($request->action as $action) {            
                $actions[] = (string)$action->name;                
            }

            $filters = array();
            if(isset($request->filters)) {            
            	foreach($request->filters->filter as $filter) {
            		$filters[] = (string)$filter['name'];
            	}
            }
            
            $view = array('name' => (string)$request->view->name, 'params' => array('template' => (string)$request->view->params->template));
            $tmp[(string)$request['match']] = array('actions' => $actions, 'view' => $view, 'filters' => $filters);            
        }
        $this->aConfig['request'] = $tmp;
    }
    
    private function parsePlugins() {
        $config = $this->intermediate;
        $tmp = array();
        foreach($config->plugins[0] as $k => $plugin) {
            $tmp[(string)$k]['class'] = (string)$plugin['class'];
            foreach($plugin->children() as $ck => $param) {
                $tmp[$k]['params'][$ck] = (string)$param;
            }
        }
        $this->aConfig['plugins'] = $tmp;
    }
    
    private function parseViews() {
        $config = $this->intermediate;
        $tmp = array();
        foreach($config->views[0] as $k => $view) {
            $tmp[$k] = (string)$view;
        }
        $this->aConfig['views'] = $tmp;
    }
    
    private function parseFilters() {
		$config = $this->intermediate;
		$tmp = array();
		if(isset($config->filters)) {			
			foreach($config->filters->filter as $filter) {				
				$tmp[] = (string)$filter['name'];
			}
		}
		$actions = $this->aConfig['actions'];
		foreach($actions as &$action) {
			$action['filters'] = array_merge($tmp, $action['filters']);			
		}						
		$this->aConfig['actions'] = $actions;		
	}    
}
?>
