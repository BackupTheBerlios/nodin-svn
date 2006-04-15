<?php
interface IHttpSession {
    public function get($name);
    public function set($name, $value);
}
?>
