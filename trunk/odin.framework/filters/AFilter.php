<?php
abstract class AFilter {
	private $nextFilter;
	public function setNextFilter(AFilter $filter) {
		$this->nextFilter = $filter;
	}
	
	public function processNext(IHttpContext $context, ActionWrapper $action) {
		Logger::debug("<b>Processing Filter: </b>" . get_class($this));
		$this->nextFilter->process($context, $action);
	}
	
	public abstract function process(IHttpContext $context, ActionWrapper $action);
}
?>