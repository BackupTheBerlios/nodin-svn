<?php function __autoload($name) {
static $map = array(
'ExecFilter' => 'odin.framework/filters/ExecFilter.php',
'FilterChain' => 'odin.framework/main/FilterChain.php',
'AFilter' => 'odin.framework/interfaces/AFilter.php',
'FakeHandle' => 'odin.handle/FakeHandle.php',
'IHandle' => 'odin.handle/IHandle.php',
'SimpleHandle' => 'odin.handle/SimpleHandle.php',
'HttpContext' => 'odin.context/HttpContext.php',
'HttpRequest' => 'odin.context/HttpRequest.php',
'HttpResponse' => 'odin.context/HttpResponse.php',
'HttpSession' => 'odin.context/HttpSession.php',
'IHttpContext' => 'odin.context/interfaces/IHttpContext.php',
'IHttpRequest' => 'odin.context/interfaces/IHttpRequest.php',
'IHttpResponse' => 'odin.context/interfaces/IHttpResponse.php',
'IHttpSession' => 'odin.context/interfaces/IHttpSession.php',
);
require_once($map[$name]);
}
?>