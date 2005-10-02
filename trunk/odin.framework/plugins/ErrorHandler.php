<?php
class ErrorHandler {
    public function handle($number, $string, $file, $line, $errcontext) {
        throw new Exception($string);
    }
}
?>
