<?php
class FooBarFilter extends AFilter {
	public function process(IHttpContext $context, $action) {
		$this->processNext($context, $action);
	}
}
?>