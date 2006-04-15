<?php
class FallbackFilter extends AFilter {
	public function process(IHttpContext $context, ActionWrapper $action) {
		try {
			$this->processNext($context, $action);
		} catch(FallbackException $fe) {
			$config = Config::getInstance();
			$fallbackAction = $config['actions'][$action[0]]['fallback'];
			
			$odin = Odin::getInstance();
			$odin->addAction($fallbackAction);
			Logger::debug('Caught Exception');
			return;
		}
	}
}
?>