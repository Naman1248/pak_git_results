<?php

/**
 * Description of classBaseMajor
 *
 * @author SystemAnalyst
 */

namespace models;

class ClassBaseMajorModel extends SuperModel {

    protected $table = 'classBaseMajor';
    protected $pk = 'id';

    public function getBaseByClassIdAndBaseIdAndMajor($cCode, $baseId, $major, $gender, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('name')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('baseId', $baseId)
                        ->where('majId', $major)
                        ->where('gender', $gender)
//                        ->where('active', 'Yes')
                        ->where('parentBaseId', $parentBaseId)
                        ->find();
//        $oSQLBuilder.printQuery();
    }
    public function getidByBaseByClassAndBaseAndMajorAndGender($cCode, $baseId, $major, $gender, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->where('baseId', $baseId)
                        ->where('majId', $major)
                        ->where('gender', $gender)
                        ->where('parentBaseId', $parentBaseId)
                        ->find();
    }

    public function getBasesByClassParentBaseMajorGender($cCode, $major, $gender, $parentBaseId = 0) {
        if (empty($gender)){
            $gender='Male';
        }
        //print_r(debug_backtrace());
        //echo "$cCode, $major, $gender, $parentBaseId";
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('baseId,name')
                ->from($this->table)
                ->where('cCode', $cCode)
                ->where('majId', $major)
                ->where('gender', $gender)
                ->where('active', 'Yes')
                ->where('parentBaseId', $parentBaseId)
                ->whereNotNull('name')
                ->orderBy('name', 'ASC')
                ->findAll();
        //$data->printQuery();
        return $data;
    }

    public function getBasesByClassParentBaseMajorGenderTestStream($cCode, $major, $gender, $offerIds, $userId, $parentBaseId = 0) {

        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('distinct a.baseId, name')
                ->from($this->table . ' a', 'applications ap');
        if ($parentBaseId == 0) {
            $oSQLBuilder->join('a.baseId', 'ap.baseId');
        } else {
            $oSQLBuilder->join('a.baseId', 'ap.childBase');
        }
        $data = $oSQLBuilder->where('a.cCode', $cCode)
                ->where('ap.isPaid', 'Y')
                ->whereIN('ap.offerId', $offerIds)
                ->where('ap.userId', $userId)
                ->where('a.majId', $major)
                ->where('gender', $gender)
                ->where('active', 'Yes')
                ->where('parentBaseId', $parentBaseId)
                ->whereNotNull('name')
                ->findAll();
        return $data;
    }

    public function getBasesByMajorDepartment($cCode, $major, $dId, $parentBaseId = 0) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'super_admin') {
            return $this->getBasesByMajorAdmin($cCode, $major);
        }

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct baseId,name')
                ->from($this->table)
                ->where('cCode', $cCode)
                ->where('majId', $major)
                ->where('dId', $dId)
//                ->where('active', 'Yes')
                ->where('parentBaseId', $parentBaseId)
                ->whereNotNull('name')
                ->findAll();
        return $data;
    }

    public function getBasesByMajorAdmin($cCode, $major, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct baseId,name')
                ->from($this->table)
                ->where('cCode', $cCode)
                ->where('majId', $major)
//                ->where('active', 'Yes')
                ->where('parentBaseId', $parentBaseId)
                ->whereNotNull('name')
                ->findAll();
        return $data;
    }
    public function getBasesByMajorAdmission($cCode, $major, $parentBaseId = 0) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('baseId, gender')
                ->from($this->table)
                ->where('cCode', $cCode)
                ->where('majId', $major)
                ->where('parentBaseId', $parentBaseId)
                ->whereNotNull('name')
                ->findAll();
        
        return $data;
    }
    
    public function getAllBasesByMajorAdmin($cCode, $major) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct gender, baseId, name, parentBaseId')
                ->from($this->table)
                ->where('cCode', $cCode)
                ->where('majId', $major)
                ->where('active', 'Yes')
                ->whereNotNull('name')
                ->orderBy('parentBaseId, baseId, gender')
                ->findAll();
        return $data;
    }

}
