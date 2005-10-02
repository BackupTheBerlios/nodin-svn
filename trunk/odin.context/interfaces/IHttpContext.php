<?php
interface IHttpContext {
    public function getRequest();
    public function getResponse();
    public function getSession();
}
?>
