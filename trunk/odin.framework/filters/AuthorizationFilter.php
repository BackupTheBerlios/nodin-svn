<?php
class AuthorizationFilter extends AFilter {
	public function process(IHttpContext $context, ActionWrapper $action) {
		$this->processNext($context, $action);
	}
}
?>