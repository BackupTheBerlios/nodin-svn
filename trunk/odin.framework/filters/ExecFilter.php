<?php
class ExecFilter extends AFilter {
	public function process(IHttpContext $context, ActionWrapper $action) {
		$action->obj = PluginManager::getInstance()->getPlugin('actionFabric')->createAction($action->sName);		
		$action->mResult = $action->obj->perform($context, $action->aParams);
	}
}
?>