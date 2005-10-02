<pre>
<?php
require_once('odin.framework/autoload.php');

// --- context ---
$request = new HttpRequest();
$response = new HttpResponse();
$session = new HttpSession();
$context = new HttpContext($request, $response, $session);

// --- filter chain ---
$filterChain = new FilterChain();
$filterChain->addFilter('firstTest', new SimpleHandle('TestFilter'));
$filterChain->addFilter('secondTest', new SimpleHandle('SecondTestFilter'));
$filterChain->process();
?>
</pre>
