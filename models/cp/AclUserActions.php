<?php

/**
 * Description of AclUserActions
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class AclUserActions extends \models\SuperModel {

    protected $table = 'aclUserActions';
    protected $pk = 'id';

    public function validate($controller, $action, $userId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id')
                        ->from($this->table)
                        ->where('actionName', $controller . '\\' . $action)
                        ->where('userId', $userId)
                        ->find();
    }

}
