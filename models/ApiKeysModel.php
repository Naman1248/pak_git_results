<?php

/**
 * Description of ApiKeysModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ApiKeysModel extends SuperModel {

    protected $table = 'apiKeys';

    public function validate($appKey, $appId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id')
                ->from($this->table)
                ->where('appId',$appId)
                ->where('appKey',$appKey)
                ->find();
        
    }
}
