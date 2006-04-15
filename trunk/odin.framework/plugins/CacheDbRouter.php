<?php
class CacheDbRouter {
    private static $instance;
    private $config;
    private $request;

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new CacheDbRouter();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->config = Config::getInstance();
    }

    public function getActions() {
        $request = $this->config['request'];
        foreach($request as $k => $v) {
            $match = str_replace('/', '\/', $k);
            $match = str_replace('*', '(.*)', $match);
            $match = str_replace('?', '([^\/])', $match);
            $match = '/' . $match . '/';
            $path = $this->getPath();
            if(preg_match($match, $path, $matches) > 0) {
                unset($matches[0]);
                $params = array_merge($matches);
                if(count($v['actions']) > 1) {
                    foreach($v['actions'] as $key => $action) {
                        $actions[] = $action;
                    }
                } else {
                    $actions[] = $v['actions'][0];
                }
                $this->request = $request[$k];
                break;
            }
        }
        return array('actions' => $actions, 'params' => $params);
    }

    private function getPath() {
        $path = HttpContext::getInstance()->getRequest()->getPathInfo();
        if(!$path) {
            return '';
        }
        $path = substr($path, 1);
        return $path;
    }

    public function getRequest() {
        return $this->request;
    }
}
?>
