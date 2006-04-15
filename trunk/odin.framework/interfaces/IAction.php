<?php
interface IAction {
    public function perform(IHttpContext $context, $params = null);
}
?>
