<?php
class LoginAction implements IAction {
    public function perform(IHttpContext $context, $params = null) {
        $request = $context->getRequest();
        if(($username = $request->get('post', 'login')) && ($password = $request->get('post', 'password'))) {
            $user = User::getInstance();
            return $user->authenticate($username, $password);            
        }
    }
}
?>
