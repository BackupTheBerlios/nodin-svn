<?php
class HttpRequest implements IHttpRequest {
    private $requestParameters = array();
    private $headers;

    public function __construct() {
        $this->requestParameters['get'] = $_GET;
        $this->requestParameters['post'] = $_POST;
        $this->requestParameters['cookie'] = $_COOKIE;
        $this->requestParameters['files'] = $_FILES;
        $this->requestParameters['server'] = $_SERVER;

        unset($_GET);
        unset($_POST);
        unset($_COOKIE);
        unset($_FILES);
        unset($_SERVER);
        unset($_REQUEST);
        $this->stripSlashes($this->requestParameters);
    }
    
   public function getMethod() {
       return $this->requestParameter['server']['REQUEST_METHOD'];
   }
   
    public function getPathInfo() {
        $pathInfo = isset($this->requestParameters['server']['PATH_INFO']) ? $this->requestParameters['server']['PATH_INFO'] : false;
        return $pathInfo;
    }
    
    public function getHeaders() {
        if($this->headers == null) {
            $this->headers = getallheaders();
        }
        return $this->headers;
    }
    
    private function &stripSlashes(&$array) {
        foreach($array as $k => $v){
            if(is_array($v)) {
                $this->stripslashes($array[$k]);
            } else {
                stripslashes($v);
            }
        }
        return $array;
    }
    
    public function get($method, $name) {
        if(isset($this->requestParameters[$method][$name])) {
            return $this->requestParameters[$method][$name];
        }
        return false;
    }
}
?>
