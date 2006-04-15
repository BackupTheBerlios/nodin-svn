<?php
class LogoutAction implements IAction {
    public function perform(IHttpContext $context, $params = null) {    	
        $user = User::getInstance();
        $user->clear();
        return true;
    }
}
?>
