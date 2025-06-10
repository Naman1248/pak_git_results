<?php

/**
 * Description of UserMArksModel
 *
 * @author SystemAnalyst
 */

namespace models;

class UserMarksModel extends \models\SuperModel {

    protected $table = 'userMarks';
    protected $pk = 'appId';

    public function allByOfferIdAndBaseAndChildbase($offerId, $baseId, $childBase, $fields = 'interviewMarks,testMarks,appId') {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select($fields)
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('baseId', $baseId)
                ->where('childBase', $childBase)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function byOfferIdAndBaseAndMajorIdFormNo($offerId, $majId, $baseId, $formNo,$fields = 'um.userId,name,cnic,formNo,testMarks,appId') {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select($fields)
                ->from($this->table . ' um', 'users u')
                ->join('um.userId', 'u.userId')
                ->where('offerId', $offerId)
                ->where('baseId', $baseId)
                ->where('majId', $majId)
                ->where('formNo', $formNo)
                ->find();
//$oSQLBuilder->printQuery();exit;
        return $data; 
    }

    public function allByOfferIdAndBaseAndChildbaseMarks($offerId, $baseId, $childBase, $fields = 'um.userId,name,cnic,formNo,testMarks,interviewMarks,appId') {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select($fields)
                ->from($this->table . ' um', 'users u')
                ->join('um.userId', 'u.userId')
                ->where('offerId', $offerId)
                ->where('baseId', $baseId);
        $oSQLBuilder->where('childBase', $childBase);
        
        $data = $oSQLBuilder->findAll();

        return $data;
    }

}
