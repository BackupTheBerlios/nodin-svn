<?php
interface IHttpRequest {
    public function getMethod();
    public function getPathInfo();
    public function getHeaders();
    public function get($method, $name);
}
?>
