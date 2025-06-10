<?php

/**
 * Description of BaseClassModel
 *
 * @author SystemAnalyst
 */

namespace models;

class BaseClassModel extends SuperModel {

    protected $table = 'baseClass';
    protected $pk = 'id';

    public function getBaseByClassIdAndBaseId($cCode, $baseId, $parentBaseId = 0) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('name')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('baseId', $baseId)
                        ->where('parentBaseId', $parentBaseId)
                        ->find();
//        $oSQLBuilder->printQuery();
    }
    public function getTestBaseByClassIdAndBaseId($cCode, $baseId, $parentBaseId = 0) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('test')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('baseId', $baseId)
                        ->where('parentBaseId', $parentBaseId)
                        ->find();
//        $oSQLBuilder->printQuery();
    }
    public function getReservedBaseByClassIdAndBaseId($cCode, $baseId, $parentBaseId = 0) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('reserved')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('baseId', $baseId)
                        ->where('parentBaseId', $parentBaseId)
                        ->find();
//        $oSQLBuilder->printQuery();
    }

    public function getBasesByClassIdAndParentBase($cCode, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('baseId, name, cCode')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('parentBaseId', $parentBaseId)
                        ->findAll();
    }
    public function getBasesByClassIdAndParentBaseAndDId($dId, $cCode, $parentBaseId = 0) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'super_admin') {
//            print_r($userRole);exit;
            return $this->getBasesByClassIdAndParentBase($cCode, $parentBaseId = 0);
        }
        
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('baseId,name')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('dId', $dId)
                        ->where('parentBaseId', $parentBaseId)
                        ->findAll();
    }

    public function getBasesByOfferIdAndClassIdAndParentBase($offerId, $cCode, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct b.baseId,name')
                ->from($this->table . ' b', 'applications a')
                ->join('a.cCode', 'b.cCode')
                ->join('a.baseId', 'b.parentBaseId')
                ->join('a.childBase', 'b.baseId')
                ->where('a.offerId', $offerId)
                ->where('parentBaseId', $parentBaseId)
                ->findAll();
//        $oSQLBuilder->printQuery();
    }
    
    public function getBasesByOfferIdAndClassIdAndParentBaseAndDId($offerId, $cCode, $parentBaseId = 0, $dId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct b.baseId,name')
                ->from($this->table . ' b', 'applications a')
                ->join('a.cCode', 'b.cCode')
                ->join('a.baseId', 'b.parentBaseId')
                ->join('a.childBase', 'b.baseId')
                ->where('a.offerId', $offerId)
                ->where('b.dId', $dId)
                ->where('parentBaseId', $parentBaseId)
                ->findAll();
//        $oSQLBuilder->printQuery();
    }

}
