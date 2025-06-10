<?php

/**
 * Description of SubjectCombinationModel
 *
 * @author SystemAnalyst
 */

namespace models;

class SubjectCombinationModel extends SuperModel {

    protected $table = 'subjectCombination';
    protected $pk = 'setId';

    public function findByClassAndGroup($cCode, $gCode) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('setId, cCode, gCode, setNo,sub1,sub2,sub3,sub4, stYear, endYear, status')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('gCode', $gCode)
                        ->where('status', 'YES')
                        ->orderBy('setNo')
                        ->findAll();
    }

    public function getSubjectsByClassAndMajorAndSetNo($cCode, $gCode, $setNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('sub1,sub2,sub3,sub4')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('gCode', $gCode)
                        ->where('setNo', $setNo)
                        ->find();
    }

}
