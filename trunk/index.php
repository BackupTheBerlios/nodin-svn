<?php
/**
 * Starting and ending point
 * index.php
 */

// --- basic initialization ---
error_reporting(E_ALL);
session_start();
$start = microtime(true);

require_once('odin.framework/autoload.php');
ob_start();

// --- context ---
$request = new HttpRequest();
$response = new HttpResponse();
$session = new HttpSession();
$context = new HttpContext($request, $response, $session);

/**
 * Prints array/object structure in nice form
 * @param array/object to print 
 */
function dump($dump) {
    print('<pre>');
    print_r($dump);
    print('</pre>');
}

// --- starting framework ---
try {
    $odin = new Odin('config.xml', $context);
} catch (Exception $e) {
    dump($e->getMessage());
    dump($e->getTraceAsString());
}
//dump($odin);

// --- printing execution time ---
//Logger::display();
//print microtime(true) - $start . "<br/>\n";
?>
