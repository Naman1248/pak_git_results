<?php

/**
 * Description of MetaDataModel
 *
 * @author sys-111
 */

namespace models;

class MetaDataModel extends SuperModel{

    protected $table = 'metaData';
    protected $pk = 'id';

    public function byKeyValue ($keyValue){
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('keyId, keyDesc')
                            ->from($this->table)
                            ->where('keyValue', $keyValue)
                            ->where('inActive', '0')
                            ->findAll();
    }
    
    public function nameByKeyValueAndByKeyId ($keyValue, $keyId){
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('keyId, keyDesc')
                            ->from($this->table)
                            ->where('keyValue', $keyValue)
                            ->where('keyId', $keyId)
                            ->where('inActive', '0')
                            ->find();
    }
    public function keyIdbyKeyValueAndByKeyDesc ($keyValue, $keyDesc){
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('keyId')
                            ->from($this->table)
                            ->where('keyValue', $keyValue)
                            ->where('keyDesc', $keyDesc)
                            ->where('inActive', '0')
                            ->find();
    }
    
}
