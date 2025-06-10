<?php

/**
 * Description of UGTResultModel
 *
 * @author SystemAnalyst
 */

namespace models;

class UGTResultModel extends SuperModel {

    protected $table = 'ugtResult';
    protected $pk = 'appId';
    private $aggregateUGTFunction = [
        '3' => 'UGTAggregateDisable',
        '7' => 'UGTAggregateHafiz',
        '9' => 'UGTAggregateGeneral',
        '16' => 'UGTAggregateWOTest',
        '17' => 'UGTAggregateWOTest',
        '10' => 'UGTAggregateSpecial',
        '11' => 'UGTAggregateGeneral',
        '13' => 'UGTAggregateGeneral',
        '18' => 'UGTAggregateGeneral',
        '20' => 'UGTAggregateGeneral',
        '36' => 'UGTAggregateGeneral',
        '31' => 'UGTAggregateElectrical',
        '32' => 'UGTAggregateElectrical',
        '37' => 'UGTAggregateElectrical',
        '41' => 'UGTAggregateElectrical',
        '42' => 'UGTAggregateNomination',
        '43' => 'UGTAggregateNomination',
        '44' => 'UGTAggregateGeneral',
        '144' => 'UGTAggregateNomination',
        '45' => 'UGTAggregateNomination',
        '66' => 'UGTAggregateNomination',
        '69' => 'UGTAggregateGeneral',
        '72' => 'UGTAggregateGeneral',
        '172' => 'UGTAggregateNomination',
        '100' => 'UGTAggregateGeneral',
        '78' => 'UGTAggregateGeneral'
    ];
    private $aggregateInterFunction = [
        '1' => 'InterAggregateSpecial',
        '3' => 'InterAggregateSpecial',
        '5' => 'InterAggregateGeneralInterview',
        '6' => 'InterAggregateSpecial',
        '7' => 'InterAggregateHafiz',
        '9' => 'InterAggregateGeneral',
        '11' => 'InterAggregateGeneralInterview',
        '11' => 'InterAggregateGeneralInterview',
        '13' => 'InterAggregateGeneral',
        '15' => 'InterAggregateKinship',
        '16' => 'InterAggregateWOTest',
        '17' => 'InterAggregateWOTest',
        '30' => 'InterAggregateSpecial',
        '33' => 'InterAggregateSpecial',
        '36' => 'InterAggregateGeneralInterview',
        '42' => 'InterAggregateNomination',
        '43' => 'InterAggregateNomination',
        '44' => 'InterAggregateGeneral',
        '45' => 'InterAggregateNomination',
        '46' => 'InterAggregateKinship',
        '47' => 'InterAggregateHafiz',
        '66' => 'InterAggregateNomination',
        '72' => 'InterAggregateGeneral',
        '79' => 'InterAggregateSpecial',
        '90' => 'InterAggregateNomination'
    ];

    private function getAggregateName($baseId) {
        return $this->aggregateUGTFunction[$baseId];
    }

    private function getInterAggregateName($baseId) {
        return $this->aggregateInterFunction[$baseId];
    }

    public function UGTAggregate($offerId, $majId, $baseId, $formNo = null) {
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['offerData'] = $oAdmmissionOfferModel->findByPK($offerId, 'cCode');
        $oUgtResultModel = new \models\UGTResultModel();
        if ($data['offerData']['cCode'] == 81) {
            $oUgtResultModel->UGTAggregateWOTestAndTrial($offerId, $majId, $baseId, $formNo);
        } else {
            $func = $this->getAggregateName($baseId);
            $oUgtResultModel->$func($offerId, $majId, $baseId, $formNo);
        }
    }

    public function InterAggregate($offerId, $majId, $baseId, $formNo = null) {
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['offerData'] = $oAdmmissionOfferModel->findByPK($offerId, 'cCode');
        $oUgtResultModel = new \models\UGTResultModel();
        $func = $this->getInterAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId, $formNo);
//        }
    }

    public function getMeritListByFormNo($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('meritList, srNo, name,userId, appId')
                        ->from($this->table . ' a')
                        ->where('formNo', $formNo)
                        ->find();
    }

    public function getMeritListStatusByForm($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('appId')
                        ->from($this->table . ' a')
                        ->where('formNo', $formNo)
                        ->whereNULL('meritListId')
                        ->whereNULL('srNo')
                        ->whereNULL('meritList')
                        ->where('locMeritList', 'N')
                        ->find();
    }

    public function resetMeritInfo($appId, $locMeritList) {

        return ($this->upsert(['locMeritList' => $locMeritList,
                    'meritList' => NULL,
                    'srNo' => NULL,
                    'meritListId' => NULL
                        ], $appId));
    }

    public function updateAcademicRecord($appId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('t.matricObt', 'e.marksObt')
                ->set('t.matricTotal', 'e.marksTot')
                ->set('t.matricBrd', 'e.brdUni')
                ->set('t.matricRn', 'e.rollNo')
                ->set('t.matricPassYear', 'e.passYear')
                ->set('t.matricExamNature', 'e.examNature')
                ->from($this->table . ' t', ' education e')
                ->join('t.userId', 'e.userId')
                ->where('e.examLevel', 1)
                ->where('e.marksObt', 0, '>')
                ->where('t.isVerified', 'NO')
                ->where('t.appId', $appId)
                ->update();
        return $updateResponse;
    }

    public function updateResultInfo($appId, $resultData) {

        return ($this->upsert(
                        [
                            'rn' => $resultData['rn'],
                            'rollNo' => $resultData['rollNo'],
                            'subject' => $resultData['subject'],
                            'compulsory' => $resultData['compulsory'],
                            'total' => $resultData['total'],
                            'testTotal' => $resultData['testTotal'],
                            'status' => $resultData['status'],
                            'testAgg' => NULL
                        ], $appId['appId']));
    }

    public function getPassResultByUserId($userId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('appId')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('majId', $majId)
                        ->where('status', 'PASS')
                        ->find();
    }

    public function deleteMeritList($id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();
        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $delResponse = $oMeritListInfoModel->deleteByPK($id);
        $updateResponse = $oSqlBuilder->set('meritList', NULL)
                ->set('srNo', NULL)
                ->set('meritListId', NULL)
                ->from($this->table)
                ->where('meritListId', $id)
                ->where('locMeritList', 'N')
                ->update();
//        $oSqlBuilder->printQuery();
//        var_dump($delResponse);
//        var_dump($updateResponse);
//                exit;
        if ($delResponse && $updateResponse) {
            $oSqlBuilder->commit();
        } else {
            $oSqlBuilder->rollback();
        }
    }

    public function lockMeritList($id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();

        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $updateResponseLock = $oMeritListInfoModel->lockMeritListInfoById($id);

        $updateResponse = $oSqlBuilder->set('locMeritList', 'Y')
                ->from($this->table)
                ->where('meritListId', $id)
                ->update();

        if ($updateResponseLock && $updateResponse) {
            $oSqlBuilder->commit();
        } else {
            $oSqlBuilder->rollback();
        }
    }

    public function getUGTResultByRollNo($rn) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('rollNo', $rn)
                        ->find();
    }

    public function unLockMeritList($id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();

        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $updateResponseLock = $oMeritListInfoModel->unLockMeritListInfoById($id);

        $updateResponse = $oSqlBuilder->set('locMeritList', 'N')
                ->from($this->table)
                ->where('meritListId', $id)
                ->update();

        if ($updateResponseLock && $updateResponse) {
            $oSqlBuilder->commit();
        } else {
            $oSqlBuilder->rollback();
        }
    }

//    public function dropMeritListByAppId($offerId, $majorId, $baseId, $formNo) {
//        $oSqlBuilder = $this->getSQLBuilder();
//        $updateResponse = $oSqlBuilder->set('locMeritList', 'N')
//                ->from($this->table)
//                ->where('offerId', $offerId)
//                ->where('baseId', $baseId)
//                ->where('meritListId', $id)
//                ->where('meritListId', $id)
//                ->where('meritListId', $id)
//                ->update();
//        return $updateResponse;
//        }

    public function findByFieldData($fields, $value, $selectFileds = '*') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($selectFileds)
                ->from($this->table . ' u', 'applications a')
                ->join('u.userId', 'a.userId')
                ->join('u.offerId', 'a.offerId')
                ->join('u.majId', 'a.majId')
                ->join('u.baseId', 'a.baseId')
                ->join('u.appId', 'a.appId')
                ->where('u.userId', $value)
                ->where('status', 'PASS')
                ->where('expired', 'No')
                ->whereIN('u.offerId', [194])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->findAll();
//        $oSqlBuilder->printQuery();exit;
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['appId']);
            }
        }
        return $data;
    }

    public function findByFieldData1($fields, $value, $selectFileds = '*') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($selectFileds)
                ->from($this->table . ' u', 'applications a')
                ->join('u.userId', 'a.userId')
                ->join('u.offerId', 'a.offerId')
                ->join('u.majId', 'a.majId')
                ->join('u.baseId', 'a.baseId')
                ->join('u.appId', 'a.appId')
                ->where('u.userId', $value)
                ->whereIN('u.offerId', [137, 138])
                ->where('expired', 'No')
                ->whereNotIN('u.baseId', [1, 3, 16, 17, 30])
                ->findAll();
//        $oSqlBuilder->printQuery();exit;
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['appId']);
            }
        }
        return $data;
    }

    public function findTrialByField($value, $selectFileds = '*') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($selectFileds)
                ->from($this->table . ' u', 'applications a')
                ->join('u.userId', 'a.userId')
                ->join('u.offerId', 'a.offerId')
                ->join('u.majId', 'a.majId')
                ->join('u.baseId', 'a.baseId')
                ->join('u.appId', 'a.appId')
                ->where('u.userId', $value)
                ->where('status', 'PASS')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->findAll();
//        $oSqlBuilder->printQuery();exit;
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['appId']);
            }
        }
        return $data;
    }

//    public function resetMeritList($meritListId) {
//        
//    }

    public function findByOfferIdAndByMajorIdByInterviewDate($offerIds, $majId, $interviewDate, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('a.interviewDate', $interviewDate)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function smsTrialByBaseId($baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->where('status', 'PASS')
                ->where('a.baseId', $baseId)
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByBaseIdByTrialDate($offerId, $baseId, $trialDate, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.trialDate, a.trialTime, a.trialVenue,a.matricObt,a.interObt') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a. offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.trialDate', $trialDate)
                ->where('status', 'PASS')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorId($offerIds, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [1, 3, 16, 17])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdAndBaseId($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->where('a.baseId', $baseId)
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdOverAll($offerId, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
//                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [16, 17, 42, 43, 45, 66])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.appId', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdInterviewPlan($offerIds, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo, a.rn,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo,a.shiftApplication') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->whereNotNull('a.interviewDate')
                ->whereNotNull('a.interviewTime')
                ->whereNotNull('a.interviewVenue')
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [16, 17, 42, 43, 45, 66])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdAndBaseIdInterviewPlan($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo, a.rn,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->whereNotNull('a.interviewDate')
                ->whereNotNull('a.interviewTime')
                ->whereNotNull('a.interviewVenue')
                ->where('status', 'PASS')
                ->where('a.baseId', $baseId)
                ->whereNotIN('a.baseId', [16, 17, 42, 43, 45, 66])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdWOInterviewPlan($offerIds, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo, a.rn, a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo, a.shiftApplication') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->whereNull('a.interviewDate')
                ->whereNull('a.interviewTime')
                ->whereNull('a.interviewVenue')
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [1, 16, 17, 42, 43, 45, 66])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdAndBaseIdWOInterviewPlan($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo, a.rn, a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.contactNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->whereNull('a.interviewDate')
                ->whereNull('a.interviewTime')
                ->whereNull('a.interviewVenue')
                ->where('status', 'PASS')
                ->where('a.baseId', $baseId)
                ->whereNotIN('a.baseId', [1, 16, 17, 42, 43, 45, 66])
//                ->whereNotIN('a.baseId', [16, 17, 42, 43, 44, 45, 66, 72])
                ->orderBy('a.rn', 'ASC')
                ->findAll();
//    $oSqlBuilder->printQuery();exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function meritListByOfferIdAndByMajorIdAndBaseId($offerId, $majId, $baseId, $totalApp, $fields = 'u.name userName,u.fatherName, u.cnic, u.ph1, a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,totAgg') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.isVerified', 'YES')
                ->where('a.totAgg', 0, '>')
//                ->where('a.interviewResult', 1)
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.totAgg', 'DESC')
                ->limit($totalApp)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function viewDataByOfferIdAndByMajorId($offerId, $majId, $fields = 'u.name userName,u.fatherName, u.cnic, u.ph1, a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,totAgg, status, compulsory, subject, total, testTotal, rollNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->whereNotIN('a.baseId', [1, 3, 16, 17, 33, 42, 43, 44, 66, 72])
                ->orderBy('a.userId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function profilePicturesByOfferIdAndByMajorIdAndBaseIdAndMeritList($offerId, $majId, $baseId, $meritList, $fields = 'u.picBucket, u.picture, u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,totAgg') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->where('a.isVerified', 'YES')
                ->where('a.totAgg', 0, '>')
                ->where('status', 'PASS')
                ->orderBy('a.srNo', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function clearData($string) {
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }

    public function meritListDetailApi($offerId, $majId, $baseId, $meritList, $year, $mapcCode) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('a.userId, u.area, formNo, a.name, a.gender, a.fatherName, a.fatherCNIC, u.cnic, a.dob, contactNo, ph2, provinceId, tehsilId, districtId, u.email,a.add1, add2, majId,baseId,cCode,meritList,srNo,setNo, u.religion, u.countryID, fatherEmail,fatherOccp, fatherQual, fatherOffice, fatherIncome, fatherStatus, motherName,motherNic, ph3, motherEmail, motherQual, motherOccp, motherOffice, motherIncome, motherStatus,guardName, guardAddr, guardNic, guardEmail, shift, matricObt, matricTotal, matricBrd, matricRn, matricPassYear, matricExamNature, interObt, interTotal, interBrd, interRn, interPassYear, interExamNature, interExamClass, bsHonsObt, bsHonsTot, honsUni, honsRn, honsPassYear, honsExamNature, meritListCtgry, childBaseId, childBaseName, totAgg')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->whereNotNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.srNo', 'ASC')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $row['name'] = $this->clearData($row['name']);
            $row['fatherName'] = $this->clearData($row['fatherName']);
            $row['motherName'] = $this->clearData($row['motherName']);
            $row['guardName'] = $this->clearData($row['guardName']);
            $row['cnic'] = $this->clearData($row['cnic']);
            $row['fatherNic'] = $this->clearData($row['fatherNic']);
            $row['motherNic'] = $this->clearData($row['motherNic']);
            $row['guardNic'] = $this->clearData($row['guardNic']);
            $row['contactNo'] = $this->clearData($row['contactNo']);
            $row['motherContact'] = $this->clearData($row['ph3']);
            $row['guardContact'] = $this->clearData($row['ph4']);
            $row['add1'] = $this->clearData($row['add1']);
            $row['fatherOffice'] = $this->clearData($row['fatherOffice']);
            $row['motherOffice'] = $this->clearData($row['motherOffice']);
            $row['guardAddr'] = $this->clearData($row['guardAddr']);
            $row['year'] = $year;
            $row['cCode'] = $mapcCode;
            $arr[] = $row;
        }
        return $arr;
    }

    public function meritListDetail($offerId, $majId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->whereNotNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.srNo', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }
    
    public function meritListDetailForInterviewPanel($offerId, $majId, $baseId, $meritList, $startSrNo, $endSrNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->where('a.srNo', $startSrNo, '>=')
                ->where('a.srNo', $endSrNo, '<=')
                ->whereNotNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.srNo', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function meritListCSV($offerId, $majId, $baseId, $meritList, $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, a.name,a.gender,a.fatherName, a.cnic, a.dob, a.contactNo, a.email, a.add1, a.appId,a.offerId,a.majId,a.cCode,a.baseId,a.className,a.majorName,a.baseName, a.meritList, a.srNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->whereNotNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.srNo', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function getDivision($percentage) {
        if ($percentage >= 60) {
            return 1;
        }
        if ($percentage >= 45 && $percentage < 60) {
            return 2;
        }
        if ($percentage >= 33 && $percentage < 45) {
            return 3;
        }
    }

    public function getAggregateByDivision($division) {
        if ($division == 1) {
            return 15;
        }
        if ($division == 2) {
            return 10;
        }
        if ($division == 3) {
            return 5;
        }
    }

    public function getAggregateByCGPA($cgpa) {
        if ($cgpa >= 3 && $cgpa <= 4) {
            return 15;
        }
        if ($cgpa >= 2.50 && $cgpa <= 2.99) {
            return 10;
        }
    }

    public function calculatePerentage($obt, $tot, $per) {
        if ($tot == 0) {
            return 0;
        }
        return round(($obt / $tot * $per), 2);
    }

    public function calculateMSAggregate($offerId, $majId, $baseId, $appId = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
//                ->where('a.locMeritList', 0)
                ->where('a.interviewObt', 0, '>');
        if ($appId != null) {
            $oSqlBuilder->where('a.appId', $appId);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $params['matricPercent'] = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricDiv'] = $this->getDivision($params['matricPercent']);
            $params['matricAgg'] = $this->getAggregateByDivision($params['matricDiv']);
            $params['interPercent'] = $this->calculatePerentage($row['interObt'], $row['interTotal'], 100);
            $params['interDiv'] = $this->getDivision($params['interPercent']);
            $params['interAgg'] = $this->getAggregateByDivision($params['interDiv']);
            $params['bsHonsAgg'] = $this->getAggregateByCGPA($row['bsHonsObt']);
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $row['bsHonsAgg'] + $row['baMasterAgg'] + $row['testAgg'] + $row['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function calculateMSAggregatebyAppId($appId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.appId', $appId)
                ->where('status', 'PASS')
                ->findAll();

        foreach ($data as $row) {
            $params['matricPercent'] = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricDiv'] = $this->getDivision($params['matricPercent']);
            $params['matricAgg'] = $this->getAggregateByDivision($params['matricDiv']);
            $params['interPercent'] = $this->calculatePerentage($row['interObt'], $row['interTotal'], 100);
            $params['interDiv'] = $this->getDivision($params['interPercent']);
            $params['interAgg'] = $this->getAggregateByDivision($params['interDiv']);
            $params['interviewAgg'] = $row['interviewObt'];
            $params['testAgg'] = $this->calculatePerentage($row['total'], $row['testTotal'], 15);
            if ($row['bsHonsTot'] > 5) {
                $bsHonsPercent = $this->calculatePerentage($row['bsHonsObt'], $row['bsHonsTot'], 100);
                $bsHonsDiv = $this->getDivision($bsHonsPercent);
                $params['bsHonsAgg'] = $this->getAggregateByDivision($bsHonsDiv);
            } else {
                $params['bsHonsAgg'] = $this->getAggregateByCGPA($row['bsHonsObt']);
            }
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['bsHonsAgg'] + $row['baMasterAgg'] + $params['testAgg'] + $params['interviewAgg'];
            return $this->upsert($params, $row['appId']);
        }
    }

    public function calculatePHDAggregatebyAppId($appId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.locMeritList', 'N')
//                ->where('a.interviewObt', 0, '>')
                ->where('a.appId', $appId)
                ->where('status', 'PASS')
                ->findAll();

        foreach ($data as $row) {
//            $params['matricPercent'] = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
//            $params['matricDiv'] = $this->getDivision($params['matricPercent']);
//            $params['matricAgg'] = $this->getAggregateByDivision($params['matricDiv']);
//            $params['interPercent'] = $this->calculatePerentage($row['interObt'], $row['interTotal'], 100);
//            $params['interDiv'] = $this->getDivision($params['interPercent']);
//            $params['interAgg'] = $this->getAggregateByDivision($params['interDiv']);
//            $params['interviewAgg'] = $row['interviewObt'];
//            $params['testAgg'] = $this->calculatePerentage($row['total'], $row['testTotal'], 15);
//            
//            if ($row['bsHonsTot'] > 5) {
//                $bsHonsPercent = $this->calculatePerentage($row['bsHonsObt'], $row['bsHonsTot'], 100);
//                $bsHonsDiv = $this->getDivision($bsHonsPercent);
//                $params['bsHonsAgg'] = $this->getAggregateByDivision($bsHonsDiv);
//            } else {
//                $params['bsHonsAgg'] = $this->getAggregateByCGPA($row['bsHonsObt']);
//            }
//            if ($row['msTot'] > 5) {
//                $msPercent = $this->calculatePerentage($row['msObt'], $row['msTot'], 100);
//                $msDiv = $this->getDivision($msPercent);
//                $params['msAgg'] = $this->getAggregateByDivision($msDiv);
//            } else {
//                $params['msAgg'] = $this->getAggregateByCGPA($row['msObt']);
//            }
//            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['bsHonsAgg'] + $row['baMasterAgg'] + $params['testAgg'] + $params['interviewAgg'] + $params['msAgg'];
            $params['totAgg'] = $row['percentile'];
            return $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateElectrical($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.interviewResult', 1)
                ->where('a.total', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
//       $oSqlBuilder->printQuery();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 40);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $testAgg = $this->calculatePerentage($row['total'], $row['testTotal'], 30);
            $params['testAgg'] = !empty($testAgg) ? $testAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['testAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateGeneral($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
//                ->where('a.interviewObt', 0, '>')
                ->where('a.interviewResult', 1)
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.total', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
//       $oSqlBuilder->printQuery();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 40);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $testAgg = $this->calculatePerentage($row['total'], $row['testTotal'], 30);
            $params['testAgg'] = !empty($testAgg) ? $testAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['testAgg'] + $params['interviewAgg'];
//            print_r($params);exit;
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateWOTest($offerId, $majId, $baseId, $formNo = null) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 50);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateGeneralInterview($offerId, $majId, $baseId, $formNo = null) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.total', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 50);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['testAgg'] = $row['total'];
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['testAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateKinship($offerId, $majId, $baseId, $formNo = null) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.total', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.kinRelationTotal', 0, '>')
                ->where('a.kinInterviewObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 50);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['testAgg'] = $row['total'];
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['testAgg'] + $params['interviewAgg'] + $row['kinRelationTotal'] + $row['kinInterviewObt'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateGeneral($offerId, $majId, $baseId, $formNo = null) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.total', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 50);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['testAgg'] = $row['total'];
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['testAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateWOTest($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
//                ->where('a.interviewObt', 0, '>')
                ->where('a.interviewResult', 1)
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 40);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['trialObt'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateWOTestAndTrial($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
//                ->where('a.interviewObt', 0, '>');
                ->where('a.interviewResult', 1);
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 40);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateHafiz($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
//                ->where('a.interviewObt', 0, '>')
                ->where('a.interviewResult', 1)
                ->where('a.total', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.trialTotal', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'] + $row['trialObt'], $row['interTotal'], 40);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $testAgg = $this->calculatePerentage($row['total'], $row['testTotal'], 30);
            $params['testAgg'] = !empty($testAgg) ? $testAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['testAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateHafiz($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interviewObt', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.total', 0, '>')
                ->where('a.trialTotal', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'] + $row['trialObt'], $row['matricTotal'], 50);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['testAgg'] = $row['total'];
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['testAgg'] + $params['interviewAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateSpecial($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateSpecial($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.locMeritList', 'N');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 70);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['interviewAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateDisable($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.interviewResult', 1)
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 10);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 70);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['interviewAgg'] = $row['interviewObt'];
            $params['totAgg'] = $params['matricAgg'] + $params['interAgg'] + $params['interviewAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateNomination($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.matricObt', 0, '>')
                ->where('a.locMeritList', 'N');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateNomination($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.matricObt', 0, '>')
                ->where('a.interObt', 0, '>')
                ->where('a.locMeritList', 'N');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->findAll();
        foreach ($data as $row) {
            $interAgg = $this->calculatePerentage($row['interObt'], $row['interTotal'], 100);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['totAgg'] = $params['interAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function isMeritListExist($offerId, $majId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('meritList')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->find();
        return $data;
    }
    public function isMeritListExistByYear($year, $cCode, $majId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('meritList')
                ->from($this->table . ' a')
                ->where('a.year', $year)
                ->where('a.cCode', $cCode)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->find();
        return $data;
    }

    public function isMeritListExistForInter($offerId, $majId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('meritList')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->find();
        return $data;
    }

    public function reservedMeritListByOfferIdAndByMajorIdAndBaseId($offerId, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.totAgg', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function testPassApplicantsByOfferId($offerId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(appId) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('status', 'PASS')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function interviewPassApplicantsByOfferId($offerId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(appId) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('interviewObt', 9, '>')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function meritListSelectedApplicantsByOfferId($offerId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(appId) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('locMeritList', 'Y')
                ->whereNotNull('meritList')
                ->whereNotNull('meritListId')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdInterviewMarks($offerId, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.interviewTot,a.interviewObt,a.interviewAgg') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('ap.isPaid', 'Y')
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdWOInterviewMarks($offerId, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.rn, a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.interviewTot,a.interviewObt,a.interviewAgg') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('ap.isPaid', 'Y')
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNull('interviewObt')
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdAndBaseIdWOInterviewMarks($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.interviewTot,a.interviewObt,a.interviewAgg, a.matricObt, a.rn, a.rollNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('ap.isPaid', 'Y')
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNull('interviewObt')
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdWithInterviewMarks($offerId, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.rn, a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.interviewTot,a.interviewObt,a.interviewAgg') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('ap.isPaid', 'Y')
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNotNull('interviewObt')
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->whereNull('meritList')
                ->whereNull('srNo')
                ->where('isVerified', 'NO')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByMajorIdAndBaseIdWithInterviewMarks($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.interviewTot,a.interviewObt,a.interviewAgg, a.matricObt, a.rn, a.rollNo') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('ap.isPaid', 'Y')
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNotNull('interviewObt')
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function specialInterviewMarks($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewTot,a.interviewObt,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue, a.childBaseName') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.trialTotal', 0, '>')
                ->where('status', 'PASS')
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfo($offerId, $majId, $baseId, $isVerified = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
//                ->where('a.interviewObt', 0, '>')
                ->where('interviewResult', 1);
//        if ($offerId == 118) {
//            $oSqlBuilder->where('interviewResult', 1);
//        }
        if ($baseId == 3 || $baseId == 7 || $baseId == 41 || $baseId == 36) {
            $oSqlBuilder->where('a.trialObt', 0, '>');
        }
        if ($isVerified != null) {
            $oSqlBuilder->where('a.isVerified', $isVerified);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }


    public function marksInfoPHD($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoInter($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.interviewObt', 0, '>')
                ->whereNotIN('baseId', [16, 17, 42, 43, 44, 45, 66, 72]);
        if ($baseId == 3 || $baseId == 7 || $baseId == 41) {
            $oSqlBuilder->where('a.trialObt', 0, '>');
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.testAgg', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoNomination($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoSpecial($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.interviewResult', 1)
//                ->where('a.interviewObt', 0, '>')
                ->where('a.trialObt', 0, '>')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->where('status', 'PASS')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoDesc($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->where('a.interviewObt', 0, '>')
                ->where('a.matricObt', 0, '>')
                ->where('a.totAgg', 0, '>')
                ->orderBy('a.totAgg', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoUGT($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->where('isVerified', 'YES')
                ->where('a.interviewObt', 0, '>')
                ->where('a.totAgg', 0, '>')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function aggregateInfo($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->where('a.totAgg', 0, '>')
                ->orderBy('a.totAgg', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByBaseId($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue, a.childBaseName, a.kinRelationTotal,a.kinInterviewTotal, a.kinInterviewObt') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->orderBy('a.majId, a.formNo', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdsAndByMajorIdAndBaseIdWOInterviewSchedule($offerIds, $majorId, $baseId, $totalApp, $fields = 'u.name userName,u.fatherName, u.cnic,a.rn, a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue, a.childBaseName, a.kinRelationTotal,a.kinInterviewTotal, a.kinInterviewObt') {
        $oSqlBuilder = $this->getSQLBuilder();
//        $data = $oSqlBuilder->select($fields)
        $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majorId)
                ->where('a.baseId', $baseId)
                ->whereNull('interviewDate')
                ->whereNull('interviewTime')
                ->whereNull('interviewVenue')
                ->where('status', 'PASS')
                ->orderBy('a.rn', 'ASC')
                ->limit($totalApp)
                ->findAll();
        $oSqlBuilder->printQuery();
        exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function specialInterviewPlan($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue, a.childBaseName') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.trialTotal', 0, '>')
                ->where('status', 'PASS')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    private function userResultByTestStream($testStreamOfferIds, $offerId, $majId, $userId) {

        $oGatResultModel = new \models\gatResultModel();

        if ($offerId == 167 || $offerId == 168) {
            $offerIds = explode(",", $testStreamOfferIds);
            $data = $oGatResultModel->getTestStreamResultByUserIdAndOfferIds($offerIds, $majId, $userId);
        } elseif ($offerId == 118) {
//        elseif (!empty($testStreamOfferIds )) {
            $offerIds = explode(",", $testStreamOfferIds);
            $oApplicationsModel = new \models\ApplicationsModel();

            $testStreamMajId = $oApplicationsModel->findTestStreambyUserIdAndOfferIds($userId, $offerIds);
            $data = $oGatResultModel->getTestStreamResultByUserIdAndOfferIds($offerIds, $testStreamMajId, $userId);
        } else {
            $data = $oGatResultModel->getResultByOfferIdByUserIdByMajorId($offerId, $majId, $userId);
        }

        return $data;
    }

    private function userEducation($userId) {
        $oEducationModel = new \models\EducationModel();
        $userEducation = $oEducationModel->byUserIdWithoutNA($userId);
        $arr = [];
        $examsLevel = ["1" => ["marksTot" => "matricTotal", "marksObt" => "matricObt", "brdUni" => "matricBrd", "rollNo" => "matricRn", "passYear" => "matricPassYear", "examNature" => "matricExamNature", "examClass" => "matricExamClass"],
            "2" => ["marksTot" => "interTotal", "marksObt" => "interObt", "brdUni" => "interBrd", "rollNo" => "interRn", "passYear" => "interPassYear", "examNature" => "interExamNature", "examClass" => "interExamClass"],
            "4" => ["marksTot" => "bsHonsTot", "marksObt" => "bsHonsObt", "brdUni" => "honsUni", "rollNo" => "honsRn", "passYear" => "honsPassYear", "examNature" => "honsExamNature", "examClass" => "honsExamClass"],
        ];
        foreach ($userEducation as $row) {
            $fieldMarksTot = $examsLevel[$row['examLevel']]['marksTot'];
            if ($row['marksTot'] == 'NA') {
                $row['marksTot'] = 0;
            }
            $arr[$fieldMarksTot] = $row['marksTot'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['marksObt'];
            if ($row['marksObt'] == 'NA') {
                $row['marksObt'] = 0;
            }
            $arr[$fieldMarksObt] = $row['marksObt'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['brdUni'];
            $arr[$fieldMarksObt] = $row['brdUni'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['rollNo'];
            $arr[$fieldMarksObt] = $row['rollNo'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['passYear'];
            $arr[$fieldMarksObt] = $row['passYear'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['examNature'];
            $arr[$fieldMarksObt] = $row['examNature'];
            $fieldMarksObt = $examsLevel[$row['examLevel']['examClass']];
            $arr[$fieldMarksObt] = $row['examClass'];
        }

        return $arr;
    }

    public function transferApplicantsByOfferIdAndBaseIdAndMajorId($offerId, $majorId, $baseId, $testBase, $id) {

        $oApplicationsModel = new \models\ApplicationsModel();

        $appData = $oApplicationsModel->allPaidByOfferIdAndMajorAndBase($offerId, $majorId, $baseId);

        if (empty($appData)) {
            return "No Applicant to Transfer.";
        }

        $tot = 0;
        foreach ($appData as $row) {
            $educationData = $this->userEducation($row['userId']);
//            print_r($educationData);exit;
            if ($testBase == 'Y') {
                $resultData = $this->userResultByTestStream($row['testStreamOfferIds'], $offerId, $majorId, $row['userId']);
            }
            if ($testBase == 'N') {
                $resultData['status'] = 'Pass';
                $resultData['rn'] = $row['formNo'];
            }

            $this->insert(
                    [
                        'religion' => $row['religion'],
                        'appId' => $row['appId'],
                        'userId' => $row['userId'],
                        'formNo' => $row['formNo'],
                        'name' => $row['userName'],
                        'gender' => $row['gender'],
                        'fatherName' => $row['fatherName'],
                        'fatherContact' => $row['ph2'],
                        'fatherCNIC' => $row['fatherNic'],
                        'cnic' => $row['cnic'],
                        'dob' => $row['dob'],
                        'contactNo' => $row['ph1'],
                        'email' => $row['email'],
                        'add1' => $row['add1'],
                        'class' => $row['className'],
                        'major' => $row['name'],
                        'baseName' => $row['baseName'],
                        'paid' => $row['isPaid'],
                        'offerId' => $row['offerId'],
                        'majId' => $row['majId'],
                        'setNo' => $row['setNo'],
                        'baseId' => $row['baseId'],
                        'cCode' => $row['cCode'],
                        'shift' => $row['shift'],
                        'shiftApplication' => $row['shiftApplication'],
                        'rn' => $resultData['rn'],
                        'rollNo' => $resultData['rollNo'] ?? NULL,
                        'compulsory' => $resultData['compulsory'] ?? NULL,
                        'subject' => $resultData['subject'] ?? NULL,
                        'total' => $resultData['total'] ?? NULL,
                        'testTotal' => $resultData['testTotal'] ?? NULL,
                        'status' => $resultData['status'],
                        'childBaseId' => $row['childBase'],
                        'matricTotal' => $educationData['matricTotal'],
                        'matricObt' => $educationData['matricObt'],
                        'matricBrd' => $educationData['matricBrd'],
                        'matricRn' => $educationData['matricRn'],
                        'matricPassYear' => $educationData['matricPassYear'],
                        'matricExamNature' => $educationData['matricExamNature'],
                        'interTotal' => $educationData['interTotal'] ?? NULL,
                        'interObt' => $educationData['interObt'] ?? NULL,
                        'interBrd' => $educationData['interBrd'] ?? NULL,
                        'interRn' => $educationData['interRn'] ?? NULL,
                        'interPassYear' => $educationData['interPassYear'] ?? NULL,
                        'interExamNature' => $educationData['interExamNature'] ?? NULL,
                        'interExamClass' => $educationData['interExamClass'] ?? NULL,
                        'bsHonsTot' => $educationData['bsHonsTot'] ?? NULL,
                        'bsHonsObt' => $educationData['bsHonsObt'] ?? NULL,
                        'honsUni' => $educationData['honsUni'] ?? NULL,
                        'honsRn' => $educationData['honsRn'] ?? NULL,
                        'honsPassYear' => $educationData['honsPassYear'] ?? NULL,
                        'honsExamNature' => $educationData['honsExamNature'] ?? NULL
                    ]
            );
            $out = $this->findByPK($row['appId'], 'appId');
            if ($out) {
                $oApplicationsModel->upsert(['transfer' => 1], $row['appId']);
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return $tot . ' Applicants Transferred Successfully.';
    }

    public function transferApplicantsByOfferIdAndMajorId($offerId, $majorId, $id) {

        $oApplicationsModel = new \models\ApplicationsModel();

        $appData = $oApplicationsModel->allPaidByOfferIdAndMajor($offerId, $majorId);

        if (empty($appData)) {
            return "No Applicant to Transfer.";
        }

        $tot = 0;
        foreach ($appData as $row) {
            $educationData = $this->userEducation($row['userId']);
            $resultData = $this->userResultByTestStream($row['testStreamOfferIds'], $offerId, $majorId, $row['userId']);
            $postArr = [
                "religion" => $row['religion'],
                "appId" => $row['appId'],
                "userId" => $row['userId'],
                "formNo" => $row['formNo'],
                "name" => $row['userName'],
                "gender" => $row['gender'],
                "fatherName" => $row['fatherName'],
                "fatherContact" => $row['ph2'],
                "fatherCNIC" => $row['fatherNic'],
                "cnic" => $row['cnic'],
                "dob" => $row['dob'],
                "contactNo" => $row['ph1'],
                "email" => $row['email'],
                "add1" => $row['add1'],
                "class" => $row['className'],
                "major" => $row['name'],
                "baseName" => $row['baseName'],
                "paid" => $row['isPaid'],
                "offerId" => $row['offerId'],
                "majId" => $row['majId'],
                "setNo" => $row['setNo'],
                "baseId" => $row['baseId'],
                "cCode" => $row['cCode'],
                "shift" => $row['shift'],
                "shiftApplication" => $row['shiftApplication'],
                "rn" => $resultData['rn'],
                "rollNo" => $resultData['rollNo'],
                "compulsory" => $resultData['compulsory'],
                "subject" => $resultData['subject'],
                "total" => $resultData['total'],
                "testTotal" => $resultData['testTotal'],
                "status" => $resultData['status'],
                "childBaseId" => $row['childBase'],
                "matricTotal" => $educationData['matricTotal'],
                "matricObt" => $educationData['matricObt'],
                "matricBrd" => $educationData['matricBrd'],
                "matricRn" => $educationData['matricRn'],
                "matricPassYear" => $educationData['matricPassYear'],
                "matricExamNature" => $educationData['matricExamNature'],
                "interTotal" => $educationData['interTotal'] ?? NULL,
                "interObt" => $educationData['interObt'] ?? NULL,
                "interBrd" => $educationData['interBrd'] ?? NULL,
                "interRn" => $educationData['interRn'] ?? NULL,
                "interPassYear" => $educationData['interPassYear'] ?? NULL,
                "interExamNature" => $educationData['interExamNature'] ?? NULL,
                "interExamClass" => $educationData['interExamClass'] ?? NULL,
                "bsHonsTot" => $educationData['bsHonsTot'] ?? NULL,
                "bsHonsObt" => $educationData['bsHonsObt'] ?? NULL,
                "honsUni" => $educationData['honsUni'] ?? NULL,
                "honsRn" => $educationData['honsRn'] ?? NULL,
                "honsPassYear" => $educationData['honsPassYear'] ?? NULL,
                "honsExamNature" => $educationData['honsExamNature'] ?? NULL
            ];
            if ($row['test'] == 'NO') {

                $postArr['rn'] = $row['formNo'];
                $postArr['status'] = 'Pass';
            }

            $this->insert($postArr);

            $out = $this->findByPK($row['appId'], 'appId');
            if ($out) {
                $oApplicationsModel->upsert(['transfer' => 1], $row['appId']);
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return $tot . ' Applicants Transferred Successfully.';
    }
    public function transferApplicantsByOfferIdAndMajorIdAndBaseId($offerId, $majorId, $baseId, $id) {

        $oApplicationsModel = new \models\ApplicationsModel();

        $appData = $oApplicationsModel->allPaidByOfferIdAndMajor($offerId, $majorId);

        if (empty($appData)) {
            return "No Applicant to Transfer.";
        }

        $tot = 0;
        foreach ($appData as $row) {
            $educationData = $this->userEducation($row['userId']);
            $resultData = $this->userResultByTestStream($row['testStreamOfferIds'], $offerId, $majorId, $row['userId']);
            $postArr = [
                "religion" => $row['religion'],
                "appId" => $row['appId'],
                "userId" => $row['userId'],
                "formNo" => $row['formNo'],
                "name" => $row['userName'],
                "gender" => $row['gender'],
                "fatherName" => $row['fatherName'],
                "fatherContact" => $row['ph2'],
                "fatherCNIC" => $row['fatherNic'],
                "cnic" => $row['cnic'],
                "dob" => $row['dob'],
                "contactNo" => $row['ph1'],
                "email" => $row['email'],
                "add1" => $row['add1'],
                "class" => $row['className'],
                "major" => $row['name'],
                "baseName" => $row['baseName'],
                "paid" => $row['isPaid'],
                "offerId" => $row['offerId'],
                "majId" => $row['majId'],
                "setNo" => $row['setNo'],
                "baseId" => $row['baseId'],
                "cCode" => $row['cCode'],
                "shift" => $row['shift'],
                "shiftApplication" => $row['shiftApplication'],
                "rn" => $resultData['rn'],
                "rollNo" => $resultData['rollNo'],
                "compulsory" => $resultData['compulsory'],
                "subject" => $resultData['subject'],
                "total" => $resultData['total'],
                "testTotal" => $resultData['testTotal'],
                "status" => $resultData['status'],
                "childBaseId" => $row['childBase'],
                "matricTotal" => $educationData['matricTotal'],
                "matricObt" => $educationData['matricObt'],
                "matricBrd" => $educationData['matricBrd'],
                "matricRn" => $educationData['matricRn'],
                "matricPassYear" => $educationData['matricPassYear'],
                "matricExamNature" => $educationData['matricExamNature'],
                "interTotal" => $educationData['interTotal'] ?? NULL,
                "interObt" => $educationData['interObt'] ?? NULL,
                "interBrd" => $educationData['interBrd'] ?? NULL,
                "interRn" => $educationData['interRn'] ?? NULL,
                "interPassYear" => $educationData['interPassYear'] ?? NULL,
                "interExamNature" => $educationData['interExamNature'] ?? NULL,
                "interExamClass" => $educationData['interExamClass'] ?? NULL,
                "bsHonsTot" => $educationData['bsHonsTot'] ?? NULL,
                "bsHonsObt" => $educationData['bsHonsObt'] ?? NULL,
                "honsUni" => $educationData['honsUni'] ?? NULL,
                "honsRn" => $educationData['honsRn'] ?? NULL,
                "honsPassYear" => $educationData['honsPassYear'] ?? NULL,
                "honsExamNature" => $educationData['honsExamNature'] ?? NULL
            ];
            if ($row['test'] == 'NO') {

                $postArr['rn'] = $row['formNo'];
                $postArr['status'] = 'Pass';
            }

            $this->insert($postArr);

            $out = $this->findByPK($row['appId'], 'appId');
            if ($out) {
                $oApplicationsModel->upsert(['transfer' => 1], $row['appId']);
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return $tot . ' Applicants Transferred Successfully.';
    }

    public function transferApplicantsByOfferIdAndBaseId($offerId, $baseId, $id) {

        $oApplicationsModel = new \models\ApplicationsModel();

        $appData = $oApplicationsModel->allPaidByOfferIdAndBaseId($offerId, $baseId);

        if (empty($appData)) {
            return "No Applicant to Transfer.";
        }

        $tot = 0;
        foreach ($appData as $row) {
            $educationData = $this->userEducation($row['userId']);
//            $resultData = $this->userResultByTestStream($row['testStreamOfferIds'], $offerId, $majorId, $row['userId']);
            $postArr = [
                "religion" => $row['religion'],
                "appId" => $row['appId'],
                "userId" => $row['userId'],
                "formNo" => $row['formNo'],
                "name" => $row['userName'],
                "gender" => $row['gender'],
                "fatherName" => $row['fatherName'],
                "fatherContact" => $row['ph2'],
                "fatherCNIC" => $row['fatherNic'],
                "cnic" => $row['cnic'],
                "dob" => $row['dob'],
                "contactNo" => $row['ph1'],
                "email" => $row['email'],
                "add1" => $row['add1'],
                "class" => $row['className'],
                "major" => $row['name'],
                "baseName" => $row['baseName'],
                "paid" => $row['isPaid'],
                "offerId" => $row['offerId'],
                "majId" => $row['majId'],
                "setNo" => $row['setNo'],
                "baseId" => $row['baseId'],
                "cCode" => $row['cCode'],
                "shift" => $row['shift'],
                "shiftApplication" => $row['shiftApplication'],
                "childBaseId" => $row['childBase'],
                "matricTotal" => $educationData['matricTotal'],
                "matricObt" => $educationData['matricObt'],
                "matricBrd" => $educationData['matricBrd'],
                "matricRn" => $educationData['matricRn'],
                "matricPassYear" => $educationData['matricPassYear'],
                "matricExamNature" => $educationData['matricExamNature'],
                "interTotal" => $educationData['interTotal'] ?? NULL,
                "interObt" => $educationData['interObt'] ?? NULL,
                "interBrd" => $educationData['interBrd'] ?? NULL,
                "interRn" => $educationData['interRn'] ?? NULL,
                "interPassYear" => $educationData['interPassYear'] ?? NULL,
                "interExamNature" => $educationData['interExamNature'] ?? NULL,
                "interExamClass" => $educationData['interExamClass'] ?? NULL,
                "bsHonsTot" => $educationData['bsHonsTot'] ?? NULL,
                "bsHonsObt" => $educationData['bsHonsObt'] ?? NULL,
                "honsUni" => $educationData['honsUni'] ?? NULL,
                "honsRn" => $educationData['honsRn'] ?? NULL,
                "honsPassYear" => $educationData['honsPassYear'] ?? NULL,
                "honsExamNature" => $educationData['honsExamNature'] ?? NULL
            ];
            if ($row['test'] == 'NO') {
                $postArr['rn'] = $row['formNo'];
                $postArr['status'] = 'Pass';
            }
            if ($row['depttInterview'] == 0) {
                $postArr['interviewResult'] = 1;
            }

            $this->insert($postArr);

            $out = $this->findByPK($row['appId'], 'appId');
            if ($out) {
                $oApplicationsModel->upsert(['transfer' => 1], $row['appId']);
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return $tot . ' Applicants Transferred Successfully.';
    }

    public function findByOfferIdAndByBaseIdTrialResult($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue,a.trialTotal,a.trialObt,a.kinRelationTotal,a.kinInterviewTotal, a.kinInterviewObt') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function kinshipResult($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue,a.trialTotal,a.trialObt,a.kinRelationTotal,a.kinInterviewTotal, a.kinInterviewObt,k.frMarks,k.mrMarks,k.gfMarks,k.gmMarks,k.brMarks,k.sisMarks') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u', 'kinship k')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.userId', 'k.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->orderBy('a.majId', 'ASC')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndByBaseIdTrialInfo($offerId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue,a.trialDate,a.trialTime,a.trialVenue,a.childBaseId, a.childBaseName') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
//        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->whereNotNull('trialDate')
                ->whereNotNull('trialTime')
                ->whereNotNull('trialVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function findInterviewDatesByOfferIdAndByMajorId($offerId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('distinct interviewDate')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->whereNotIN('baseId', [17, 16])
                ->whereNotNull('interviewDate')
                ->findAll();
        return $data;
    }

    public function findInterviewVenuesByOfferIdAndByMajorIdAndDate($offerId, $majId, $interviewDate) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('distinct interviewVenue')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->where('interviewDate', $interviewDate)
                ->whereNotIN('baseId', [17, 16])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->findAll();
        return $data;
    }

    public function findTrialDatesByOfferIdAndByBaseId($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('distinct trialDate')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('baseId', $baseId)
                ->whereNotNull('trialDate')
                ->findAll();
        return $data;
    }

    public function interviewInfoByOfferIdAndByMajorId($offerIds, $majId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u', 'education e')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.userId', 'e.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->where('examLevel', 1)
                ->whereNotIN('a.baseId', [1, 3, 17, 16])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function interviewInfoByOfferIdAndByMajorIdAndBaseId($offerIds, $majId, $baseId, $fields = 'u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u', 'education e')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.userId', 'e.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->where('examLevel', 1)
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function interviewInfoByOfferIdAndByMajorIdAndByBaseId($offerIds, $majId, $baseId, $fields = 'ap.version, u.name userName,u.fatherName, u.cnic,a.rollNo,a.appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName,a.status,a.interviewDate, a.interviewTime, a.interviewVenue') {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($fields)
                ->from($this->table . ' a', 'applications ap', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u', 'education e')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('a.userId', 'ap.userId')
                ->join('a.userId', 'e.userId')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('b.parentBaseId', 0)
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('status', 'PASS')
                ->where('examLevel', 1)
                ->whereNotIN('a.baseId', [1, 3, 17, 16])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksDetail($offerId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('a.*')
                ->from($this->table . ' a', 'applications ap')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.majId', 'ap.majId')
                ->join('a.baseId', 'ap.baseId')
                ->join('a.appId', 'ap.appId')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->whereNotIN('a.baseId', [17, 16])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
//        $oSqlBuilder->printQuery();
//        exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function getByFormNo($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('formNo', $formNo)
                        ->find();
    }

    public function applicantsByInterviewVenue($interviewVenue, $interviewDate) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('userId, rn, formNo, rollNo, name, fatherName, interviewTime, total, matricObt, interObt')
                        ->from($this->table)
                        ->where('interviewVenue', $interviewVenue)
                        ->whereIN('offerId', [151, 152])
                        ->whereIN('majId', [52,147,148])
                        ->whereNotNull('rn')
                        ->where('interviewDate', $interviewDate)
                        ->whereNotNull('interviewTime')
                        ->where('status', 'Pass')
                        ->where('expired', 'No')
                        ->orderBy('rn', 'ASC')
                        ->findAll();
    }

    public function allInterviewVenues() {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct userId) cnt, interviewDate, interviewVenue')
                ->from($this->table)
                ->whereIN('offerId', [151, 152])
                ->whereIN('majId', [52, 147, 148])
                ->whereNotNull('interviewDate')
                ->whereNotNull('interviewTime')
                ->whereNotNull('interviewVenue')
                ->groupBy('interviewDate, interviewVenue')
                ->orderBy('interviewDate, interviewVenue', 'ASC')
                ->findAll();
        return $data;
    }

}
