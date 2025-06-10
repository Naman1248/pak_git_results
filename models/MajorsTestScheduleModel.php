<?php

/**
 * Description of MajorsTestScheduleModel
 *
 * @author SystemAnalyst
 */

namespace models;

class MajorsTestScheduleModel extends SuperModel {

    protected $table = 'majorsTestSchedule';
    protected $pk = 'id';

    public function getScheduleByOfferIdAndMajorId($offerId, $majorId, $cityId, $slotId) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('day, startTime, date, a.slotNo, t.slotId')
                        ->from($this->table . ' a', 'testSchedule t')
                        ->join('a.offerId', 't.offerId')
                        ->join('a.slotNo', 't.slotNo')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('a.cityId', $cityId)
                        ->where('a.slotId', $slotId)
                        ->whereNotNull('day')
                        ->whereNotNull('startTime')
                        ->whereNotNull('date')
                        ->whereNotNull('a.slotNo')
                        ->find();
    }

    public function getScheduleByOfferIdAndMajorIdAndCityId($offerId, $majorId, $cityId) {
//        print_r($offerId . $majorId . $cityId); exit;
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('day, startTime, date, a.slotNo, t.slotId')
                        ->from($this->table . ' a', 'testSchedule t')
                        ->join('a.offerId', 't.offerId')
                        ->join('a.slotNo', 't.slotNo')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('a.cityId', $cityId)
                        ->whereNotNull('day')
                        ->whereNotNull('startTime')
                        ->whereNotNull('date')
                        ->whereNotNull('a.slotNo')
                        ->find();
    }

    public function allMajorsByOfferIdAndSlotWise($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('rnFormat, name, t.slotNo, m.majId, t.date, t.startTime, t.endTime, t.day')
                ->from($this->table . ' mt', 'majors m', 'testSchedule t')
                ->join('m.offerId', 'mt.offerId')
                ->join('m.majId', 'mt.majId')
                ->join('t.offerId', 'mt.offerId')
                ->join('t.slotNo', 'mt.slotNo')
                ->where('m.offerId', $offerId)
                ->orderBy('t.slotNo, m.majId')
                ->findAll();
        if (empty($data)) {
            return $data;
        }
        $oGatSlipModel = new \models\GatSlipModel();
        $majStr = $oGatSlipModel->applicantsWithRnByOfferId($offerId);

        $arr = [];
        foreach ($data as $row) {
            $row['strength'] = $majStr[$row['majId']] ?? 0;
            $arr[$row['slotNo']][] = $row;
        }
        return $arr;
    }

    public function allMajorsPassFailByOfferIdAndSlotWise($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('rnFormat, name, t.slotNo, m.majId, t.date, t.startTime, t.endTime, t.day')
                ->from($this->table . ' mt', 'majors m', 'testSchedule t')
                ->join('m.offerId', 'mt.offerId')
                ->join('m.majId', 'mt.majId')
                ->join('t.offerId', 'mt.offerId')
                ->join('t.slotNo', 'mt.slotNo')
                ->where('m.offerId', $offerId)
                ->orderBy('t.slotNo, m.majId')
                ->findAll();
        if (empty($data)) {
            return $data;
        }
        $oGatSlipModel = new \models\GatSlipModel();
        $majStr = $oGatSlipModel->applicantsWithRnByOfferId($offerId);

        $arr = [];
        foreach ($data as $row) {
            $row['strength'] = $majStr[$row['majId']] ?? 0;
            $arr[$row['slotNo']][] = $row;
        }
        return $arr;
    }

    public function allMajorsByOfferIdsAndSlotWiseMulti($offerId, $slotId, $cityId) {
        $oGatSlipModel = new \models\GatSlipModel();
        $offerIds = $oGatSlipModel->offerIdsBySlotId($slotId);
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('rnFormat, name, t.slotNo, m.majId, t.date, t.startTime, t.endTime, t.day')
                ->from($this->table . ' mt', 'majors m', 'testSchedule t')
                ->join('m.offerId', 'mt.offerId')
                ->join('m.majId', 'mt.majId')
                ->join('t.offerId', 'mt.offerId')
                ->join('t.slotNo', 'mt.slotNo')
                ->join('t.cityId', 'mt.cityId')
                ->whereIN('m.offerId', $offerIds)
                ->whereNotNull('rnFormat')
//                ->where('m.offerId', $offerId)
                ->where('mt.cityId', $cityId)
                ->orderBy('t.slotNo, m.name')
                ->findAll();
//        $oSQLBuilder->printQuery();exit;
        if (empty($data)) {
            return $data;
        }

        $majStr = $oGatSlipModel->applicantsWithRnByOfferIdsMulti($offerIds, $cityId);

        $arr = [];
        foreach ($data as $row) {
            $row['strength'] = $majStr[$row['majId']] ?? 0;
            $arr[$row['slotNo']][] = $row;
        }
        return $arr;
    }

    public function getMajorsByOfferIdWithAllStatistics($offerId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.id, a.majId, name, slotNo, slotId, cityId')
                ->from($this->table . ' a', 'majors m')
                ->join('a.offerId', 'm.offerId')
                ->where('a.cityId', $cityId)
                ->join('a.majId', 'm.majId')
                ->where('a.offerId', $offerId)
                ->orderBy('slotNo, a.majId')
                ->findAll();
        $oGatResultModel = new \models\gatResultModel();
        $oGatSlipModel = new \models\GatSlipModel();

        $oApplicationModel = new \models\ApplicationsModel();
        $rnData = $oGatSlipModel->applicantsWithRnByOfferId($offerId, $cityId);
        $scheduleData = $oGatSlipModel->applicantsWithScheduleByOfferId($offerId, $cityId);
        $venueData = $oGatSlipModel->applicantsWithVenueByOfferId($offerId, $cityId);

        $paidData = $oApplicationModel->paidApplicantsWORNByOfferId($offerId, $cityId);
        $awardData = $oGatResultModel->countApplicantsByOfferId($offerId, $cityId);
//        var_dump($paidData);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']]['Id'] = $row['Id'];
            $arr[$row['majId']]['slotId'] = $row['slotId'];
            $arr[$row['majId']]['cityId'] = $row['cityId'];
            $arr[$row['majId']]['majId'] = $row['majId'];
            $arr[$row['majId']]['name'] = $row['name'];
            $arr[$row['majId']]['slotNo'] = $row['slotNo'];
            $arr[$row['majId']]['rnFormat'] = $row['rnFormat'];
            $arr[$row['majId']]['paidData'] = $paidData[$row['majId']];
            $arr[$row['majId']]['rnData'] = $rnData[$row['majId']];
            $arr[$row['majId']]['scheduleData'] = $scheduleData[$row['majId']];
            $arr[$row['majId']]['venueData'] = $venueData[$row['majId']];
            $arr[$row['majId']]['awardData'] = $awardData[$row['majId']];
        }
//        print_r($arr);
//        exit;
        return $arr;
    }

    public function getMajorsByOfferIdWithAllStatisticsMulti($slotId, $cityId) {
        $oTestScheduleModel = new \models\TestScheduleModel();
        $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($slotId);

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct a.majId, name, slotNo, slotId, cityId, strength')
                ->from($this->table . ' a', 'majors m')
                ->join('a.offerId', 'm.offerId')
                ->join('a.majId', 'm.majId')
                ->whereIN('a.offerId', $offerIds)
                ->where('a.cityId', $cityId)
                ->orderBy('slotNo, m.name')
                ->findAll();
//        echo '<pre>';
//        print_r($data);exit;
        $oGatResultModel = new \models\gatResultModel();
        $oGatSlipModel = new \models\GatSlipModel();

        $oApplicationModel = new \models\ApplicationsModel();
        $rnData = $oGatSlipModel->applicantsWithRnByMultiOfferIds($offerIds, $cityId);
        $scheduleData = $oGatSlipModel->applicantsWithScheduleByMultiOfferIds($offerIds, $cityId);
        $venueData = $oGatSlipModel->applicantsWithVenueByMultiOfferIds($offerIds, $cityId);

        $paidData = $oApplicationModel->paidApplicantsWORNByMultiOfferIds($offerIds, $cityId);
        $awardData = $oGatResultModel->countApplicantsByMultiOfferIds($offerIds, $cityId);
//        var_dump($paidData);exit;
        $arr = [];
        foreach ($data as $row) {
            $loopIndex = $row['majId'];
            if (!empty($arr[$row['majId']]['majId'])) {
                $loopIndex = $loopIndex . '' . $loopIndex;
            }
            $arr[$loopIndex]['majId'] = $row['majId'];
            $arr[$loopIndex]['slotId'] = $row['slotId'];
            $arr[$loopIndex]['cityId'] = $row['cityId'];
            $arr[$loopIndex]['name'] = $row['name'];
            $arr[$loopIndex]['slotNo'] = $row['slotNo'];
            $arr[$loopIndex]['strength'] = $row['strength'];
            $arr[$loopIndex]['rnFormat'] = $row['rnFormat'];
            $arr[$loopIndex]['paidData'] = $paidData[$row['majId']];
            $arr[$loopIndex]['rnData'] = $rnData[$row['majId']];
            $arr[$loopIndex]['scheduleData'] = $scheduleData[$row['majId']];
            $arr[$loopIndex]['venueData'] = $venueData[$row['majId']];
            $arr[$loopIndex]['awardData'] = $awardData[$row['majId']];
        }
//        echo "<pre>";
//        print_r($arr);
//        exit;
        return $arr;
    }

    public function getOfferIdsBySlotIdAndByMajorId($slotId, $majorId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct offerId')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->where('majId', $majorId)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['offerId']] = $row['offerId'];
        }
        return $arr;
    }
}
