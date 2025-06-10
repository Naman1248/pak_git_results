<?php

/**
 * Description of AdmissionOfferModel
 *
 * @author SystemAnalyst
 */

namespace models;

class AdmissionOfferModel extends SuperModel {

    protected $table = 'admissionOffer';
    protected $pk = 'offerId';

    public function getOpeningsOfTheYear($admissionYear) {
        $startDate = date("Y-m-d H:i:s", strtotime($admissionYear . "-01-01 00:00:00"));
        return $this->findByQuery('SELECT offerId,cCode,className,bankName,accNo,endDate FROM ' . $this->table . ' WHERE '
                        . ' startDate>=?', [$startDate]);
    }

    public function getCurrentOpenings() {
        $curDate = date("Y-m-d H:i:s");
        return $this->findByQuery('SELECT offerId,className,endDate FROM ' . $this->table . ' WHERE '
                        . '? BETWEEN startDate AND endDate ', [$curDate]);
    }

    public function getOfferedProgram() {
        $curDate = date("Y-m-d H:i:s");
        return $this->findByQuery('SELECT majors.offerId, studyLevel, className, startDate, majors.endDate, name FROM ' . $this->table . ' , majors WHERE majors.offerId = ' . $this->table . '.offerId AND '
                        . '? BETWEEN startDate AND admissionOffer.endDate order by studyLevel, majors.cCode', [$curDate]);
    }

    public function getOfferedProgramByMajors() {
        $curDate = date("Y-m-d H:i:s");
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('m.offerId, studyLevel, className, startDate, m.endDate, name, campus')
                ->from($this->table . ' a', 'majors m')
                ->join('a.offerId', 'm.offerId')
                ->where('m.endDate', $curDate, '>=')
                ->orderBy('m.endDate', 'ASC');
        return $oSQLBuilder->findAll();
    }
    public function getOfferedTestProgramByYear($admissionYear) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('offerId, className, compTotal, subTotal, testTotal, testPassPer, testCity')
                ->from($this->table)
                ->where('year', $admissionYear)
                ->where('test', 'YES')
                ->orderBy('offerId', 'DESC');
        return $oSQLBuilder->findAll();
    }
    public function getActiveOfferedProgramByYear($admissionYear) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('offerId,cCode,className,bankName,accNo,endDate')
                ->from($this->table)
                ->where('year', $admissionYear)
                ->where('active', 1)
                ->orderBy('offerId', 'DESC');
        return $oSQLBuilder->findAll();
    }

    public function getClassesByDeptt($dId, $admissionYear) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'vc_admin') {
            return $this->getActiveOfferedProgramByYear($admissionYear);
        }
        if ($userRole == 'super_admin' || $userRole == 'base_admin') {
            return $this->getOpeningsOfTheYear($admissionYear);
        }
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('distinct a.offerId,a.cCode,className,bankName,accNo,a.endDate')
                ->from($this->table . ' a', 'majors m')
                ->join('a.cCode', 'm.cCode')
                ->join('a.year', 'm.year')
                ->where('a.year', $admissionYear)
                ->where('dId', $dId);
        return $oSQLBuilder->findAll();
    }
    public function getTestClassesByDeptt($dId, $admissionYear) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'super_admin' || $userRole == 'base_admin') {
            return $this->getOpeningsOfTheYear($admissionYear);
        }
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('distinct a.offerId,a.cCode,className,bankName,accNo,a.endDate')
                ->from($this->table . ' a', 'majors m')
                ->join('a.cCode', 'm.cCode')
                ->join('a.year', 'm.year')
                ->where('a.year', $admissionYear)
                ->where('a.test', 'YES')
                ->where('dId', $dId);
        return $oSQLBuilder->findAll();
    }

    public function getGATOpenning($dId, $admissionYear) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select(' a.offerId,a.cCode,className,bankName,accNo,a.endDate')
                ->from($this->table . ' a')
                ->where('a.year', $admissionYear);
//                ->where('a.cCode', 100);
        return $oSQLBuilder->findAll();
    }
    public function getChildOfferIds($parentOfferId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('offerId')
                ->from($this->table . ' a')
                ->where('parentOfferId', $parentOfferId)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['offerId']] = $row['offerId'];
        }
        return $arr;
    }
    public function getMeritListOfferIds($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('offerId')
                ->from($this->table . ' a')
                ->where('meritList', $offerId)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['offerId']] = $row['offerId'];
        }
        return $arr;
    }

    public function getClassesByBase($dId, $admissionYear) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'super_admin') {
            return $this->getOpeningsOfTheYear($admissionYear);
        }
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('distinct a.offerId,a.cCode,className,bankName,accNo,a.endDate')
                ->from($this->table . ' a', 'baseClass b')
                ->join('a.cCode', 'b.cCode')
                ->where('a.year', $admissionYear)
                ->where('dId', $dId);
        return $oSQLBuilder->findAll();
    }

    public function getPreReqByOfferId($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('preReq,preReq1')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->find();
    }

    public function getOfferIdByYearAndClass($year, $cCode) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('offerId')
                        ->from($this->table)
                        ->where('year', $year)
                        ->where('cCode', $cCode)
                        ->orderBy('offerId', 'DESC')
                        ->find();
    }
    public function getOfferIdByYearAndClassAndAttemptNo($year, $cCode, $attemptNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('offerId')
                        ->from($this->table)
                        ->where('year', $year)
                        ->where('cCode', $cCode)
                        ->where('attemptNo', $attemptNo)
                        ->find();
    }

}
