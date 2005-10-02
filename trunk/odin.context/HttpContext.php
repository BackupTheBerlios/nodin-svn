<?php
class HttpContext implements IHttpContext {
    private $request;
    private $response;
    private $session;
    
    public function __construct(IHttpRequest $request, IHttpResponse $response, IHttpSession $session) {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
    }
    
    public function getRequest() {
        return $this->request;
    }
    
    public function getResponse() {
        return $this->response;
    }
    
    public function getSession() {
        return $this->session;
    }
}
?>
