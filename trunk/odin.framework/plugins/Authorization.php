<?php
class Authorization {
    public function checkPerm(User $user, $actionName, $params = null) {
        $config = Config::getInstance();
        $roles = $config['actions'][$actionName]['roles'];
        $missingRoles = array_diff($roles, $user->getRoles());
        if ((isset($missingRoles[0]) && $missingRoles[0] == '') || count($missingRoles) == 0) {
            return true;
        } else {
            throw new MissingRolesException('Brak ról: ' . implode(' ', $missingRoles));
        }
    }
}
?>
