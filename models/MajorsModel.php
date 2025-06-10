<?php

/**
 * Description of MajorsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class MajorsModel extends SuperModel {

    protected $table = 'majors';
    protected $pk = 'Id';

    public function getMajorByOfferIdClassIdAndMajorId($offerId, $cCode, $majorId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('name,dues,duesInWord,endDate')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cCode', $cCode)
                        ->where('majId', $majorId)
                        ->find();
//        return $data['name'];
    }

    public function getAmountByMajorIdAndOfferId($offerId, $majorId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('dues, endDate')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majorId)
                        ->find();
//        return $data['name'];
    }
public function getClassByYearLMSClassIdAndMajorId($year, $LMSClass, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('cCode')
                ->from($this->table)
                ->where('year', $year)
                ->where('mapcCode', $LMSClass)
                ->where('majId', $majId)
                ->find();
        return $data;
    }
    public function getMajorByYearClassIdAndMajorId($year, $cCode, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('name')
                ->from($this->table)
                ->where('year', $year)
                ->where('cCode', $cCode)
                ->where('majId', $majId)
                ->find();
//        print_r($data); exit;
        return $data;
    }

    public function getMajorNameByOfferIdClassIdAndMajorId($offerId, $cCode, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('name')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cCode', $cCode)
                ->where('majId', $majId)
                ->find();
//        print_r($data); exit;
        return $data;
    }

    public function getMajorNameByOfferIdAndMajorId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('name')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->find();
        return $data['name'];
    }
    public function getMultiMajorPrintitleByOfferIdsAndMajorId($offerIds, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('printTitle')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majId)
                ->whereNotNull('printTitle')
                ->find();
        return $data['printTitle'];
    }
    public function getMajorEligibilityByOfferIdAndMajorId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('eligibility')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->find();
        return $data['eligibility'];
    }
    public function getMajorPrintitleByOfferIdAndMajorId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('printTitle')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->find();
        return $data['printTitle'];
    }
    public function getRNRangeAndPerPageByOfferIdAndMajorId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('startRn, endRn, perPage')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->find();
        return $data;
    }

    public function getRollNoFormatByOfferIdAndMajorId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('rnFormat')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->find();
        return $data['rnFormat'];
    }

    public function getMajorByOfferIdClassId($offerId, $cCode, $userId = null) {

        if (!empty($userId)) {
            $ouserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
            $extensionData = $ouserAdmissionOfferModel->exist($userId, $offerId);
        }
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('majId,name')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cCode', $cCode);
        if (empty($extensionData)) {
            $oSQLBuilder->where('active', 'YES');
        }
        $oSQLBuilder->orderBy('name', 'ASC');
        return $oSQLBuilder->findAll();
//        return $data['name'];
    }

    public function getAllMajorByOfferIdClassId($offerId, $cCode) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('majId,name')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cCode', $cCode)
                        ->findAll();
//        return $data['name'];
    }

    public function getMajorsByOfferId($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('Id, majId, name, rnFormat, cCode')
                ->from($this->table)
                ->where('offerId', $offerId);
        return $oSQLBuilder->findAll();
    }
    
    public function getMajorsByOfferIdForTestSchedule($offerId, $slotNo=null) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('Id, majId, name, rnFormat, slotNo, date, day, startTime, slotNo, endTime')
                ->from($this->table)
                ->where('offerId', $offerId);
        if ($slotNo != null) {
            $oSQLBuilder->where('slotNo', $slotNo);
        }                
                $oSQLBuilder->orderBy('slotno', 'DESC');
        return $oSQLBuilder->findAll();
    }
    
    public function getSlotDetailByOfferIdAndSlotNo($offerId, $slotNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct date, day, startTime, endTime')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->find();
    }

    public function getMajorsByOfferIdWithAllStatistics($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('Id, majId, name, rnFormat')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->findAll();
        $oGatSlipModel = new \models\GatSlipModel();
        $oApplicationModel = new \models\ApplicationsModel();
        $rnData = $oGatSlipModel->applicantsWithRnByOfferId($offerId);
        $scheduleData = $oGatSlipModel->applicantsWithScheduleByOfferId($offerId);

        $paidData = $oApplicationModel->paidApplicantsWORNByOfferId($offerId);
//        var_dump($paidData);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']]['Id'] = $row['Id'];
            $arr[$row['majId']]['majId'] = $row['majId'];
            $arr[$row['majId']]['name'] = $row['name'];
            $arr[$row['majId']]['rnFormat'] = $row['rnFormat'];
            $arr[$row['majId']]['paidData'] = $paidData[$row['majId']];
            $arr[$row['majId']]['rnData'] = $rnData[$row['majId']];
            $arr[$row['majId']]['scheduleData'] = $scheduleData[$row['majId']];
        }
//        print_r($arr);
//        exit;
        return $arr;
    }

    public function getMajorsByOfferIdWithRNFormat($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('Id, majId, name, rnFormat')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->whereNotNull('rnFormat');
        return $oSQLBuilder->findAll();
    }

    public function getMajorByOfferIdClassIdAndDId($offerId, $cCode, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        if ($userRole == 'super_admin' || $userRole == 'base_admin') {
            return $this->getAllMajorByOfferIdClassId($offerId, $cCode);
        }
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('majId,name')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cCode', $cCode)
                        ->where('dId', $dId)
                        ->findAll();
//        return $data['name'];
    }

//
//  TEST SCHEDULE FUNCTIONS
//
//    public function getScheduleByOfferIdAndMajorId($offerId, $majorId){
//        
//        $oSQLBuilder = $this->getSQLBuilder();
//        return $oSQLBuilder->select('day, startTime, date, slotNo')
//                        ->from($this->table)
//                        ->where('offerId', $offerId)
//                        ->where('majId', $majorId)
//                        ->whereNotNull('day')
//                        ->whereNotNull('startTime')
//                        ->whereNotNull('date')
//                        ->whereNotNull('slotNo')
//                        ->find();
//    }
    
//    public function byOfferId($offerId){
//        $oSQLBuilder = $this->getSQLBuilder();
//        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotNo')
//                        ->from($this->table)
//                        ->where('offerId', $offerId)
//                        ->whereNotNull('slotNo')
//                        ->whereNotNull('day')
//                        ->whereNotNull('date')
//                        ->whereNotNull('startTime')
//                        ->orderBy('slotNo')
//                        ->findAll();
//    }
//    public function getMajorsbyOfferIdAndSlotNo($offerId, $slotNo){
//        $oSQLBuilder = $this->getSQLBuilder();
//        return $oSQLBuilder->select('majId')
//                        ->from($this->table)
//                        ->where('offerId', $offerId)
//                        ->where('slotNo', $slotNo)
//                        ->findAll();
//    }    
// END TEST SCHEDULE FUNCTIONS    
}
