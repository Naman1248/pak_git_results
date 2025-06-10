<?php

/**
 * Description of TestScheduleModel
 *
 * @author SystemAnalyst
 */

namespace models;

class TestScheduleModel extends SuperModel {

    protected $table = 'testSchedule';
    protected $pk = 'id';

    public function getScheduleByOfferIdAndCityId($offerId, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('day, startTime, date, slotNo, slotId, reportTime, endTime')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cityId', $cityId)
                        ->whereNotNull('day')
                        ->whereNotNull('startTime')
                        ->whereNotNull('date')
                        ->whereNotNull('slotNo')
                        ->findAll();
    }

//    public function byOfferId($offerId){
//        $oSQLBuilder = $this->getSQLBuilder();
//        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotNo')
//                        ->from($this->table)
//                        ->where('offerId', $offerId)
//                        ->orderBy('slotNo')
//                        ->findAll();
//    }
    public function byOfferId($offerId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cityId', $cityId)
                        ->orderBy('slotId')
                        ->findAll();
    }

    public function byOfferIdAndCityId($offerId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('cityId', $cityId)
                        ->orderBy('slotId')
                        ->findAll();
    }

    public function getMajorsbyOfferIdAndSlotNo($offerId, $slotNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('majId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotNo', $slotNo)
                        ->findAll();
    }

    public function byOfferIdSlots($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('date, day, startTime, endTime, reportslotNo')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->orderBy('slotNo')
                        ->findAll();
    }

    public function dateAndTimeByofferIdAndSlotNo($offerId, $slotNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('date, day, startTime, endTime, reportTime, slotId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotNo', $slotNo)
                        ->find();
    }

    public function dateAndTimeBySlotId($slotId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct date, day, startTime, endTime, reportTime, slotId')
                        ->from($this->table)
                        ->where('slotId', $slotId)
                        ->find();
    }

    public function statisticsByOfferIdAndSlots($offerId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct date, day, startTime, endTime, slotNo, slotId, cityId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cityId', $cityId)
                ->whereNotNull('slotNo')
                ->orderBy('slotNo')
                ->findAll();

        $oGatSlipModel = new \models\GatSlipModel();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['slotNo']]['slotId'] = $row['slotId'];
            $arr[$row['slotNo']]['slotNo'] = $row['slotNo'];
            $arr[$row['slotNo']]['cityId'] = $row['cityId'];
            $arr[$row['slotNo']]['date'] = $row['date'];
            $arr[$row['slotNo']]['day'] = $row['day'];
            $arr[$row['slotNo']]['startTime'] = $row['startTime'];
            $arr[$row['slotNo']]['endTime'] = $row['endTime'];
            $arr[$row['slotNo']]['appData'] = $oGatSlipModel->countApplicantsBySlotId($row['slotId'], $cityId);
//            $arr[$row['slotNo']]['appData'] = $appData[$row['slotNo']];
//            $arr[$row['slotNo']]['venueData'] = $venueData[$row['slotNo']];
            $arr[$row['slotNo']]['venueData'] = $oGatSlipModel->countApplicantsBySlotIdWithVenue($row['slotId'], $cityId);
        }
//        echo '<pre>';
//        print_r($arr);
//        exit;
        return $arr;
    }

    public function getOfferIdsBySlotId($slotId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct offerId')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['offerId']] = $row['offerId'];
        }
        return $arr;
    }

    public function getDetailBySlotId($offerId, $slotId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->find();
        return $data;
    }

    public function getDetailBySlotIdAndByCityId($offerId, $slotId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->find();
        return $data;
    }

}
