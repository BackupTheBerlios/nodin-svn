<?php
abstract class AFilter {
    private $nextFilter;
    public function setNextFilter(AFilter $filter) {
        $this->nextFilter = $filter;
    }
    
    public function processNext() {
        $this->nextFilter->process();
    }
    
    public abstract function process();
}
?>
