<?php
class FooFilter extends AFilter {
	public function process(IHttpContext $context, $action) {
		$this->processNext($context, $action);
	}
}
?>