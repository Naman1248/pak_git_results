<?php

/**
 * Description of GatSlipModel
 *
 * @author SystemAnalyst
 */

namespace models;

class GatSlipModel extends SuperModel {

    protected $table = 'gatSlip';
    protected $pk = 'id';

    public function findByField($field, $value, $selectFileds = '*') {

        $userData = $this->state()->get('userInfo');
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select($selectFileds)
                ->from($this->table)
                ->where($field, $value)
                ->where('expired', 'NO')
                ->findAll();

        if (!empty($data)) {
            foreach ($data as $row) {
                if ($userData['userId'] == $row['userId']) {
                    $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['id']);
                }
            }
        }
        return $data;
    }

    public function findByFormNo($formNo) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, rn')
                ->from($this->table)
                ->where('formNo', $formNo)
                ->findAll();

        return $data;
    }

    public function isRNAlreadyExistByOfferIdAndMajorIdAndCityId($offerIds, $majId, $cityId, $rn) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
                ->where('rn', $rn)
                ->find();

        return $data;
    }

    public function rnByOfferIdAndMajorIdWOSchedule($offerId, $majorId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id, userId, rn')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majorId)
                        ->whereNotNull('rn')
                        ->whereNull('date')
                        ->whereNull('time')
                        ->findAll();
    }

    public function applicantsByOfferIdAndSlotNoAndRoomId($offerId, $slotId, $roomId, $cityId, $roomFor) {
        $offerIds = $this->offerIdsBySlotId($slotId);
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('userId, majId, rn, formNo, rollNo, name, fatherName')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotId', $slotId)
                ->where('roomId', $roomId)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('venue');
//                       
        if ($roomFor == 'SINGLE') {
            $oSqlBuilder->orderBy('majId, rn', 'ASC');
        } else {
            $oSqlBuilder->orderBy('userId, rn', 'ASC');
        }
        return $oSqlBuilder->findAll();
    }

//    public function applicantsByOfferIdAndSlotNoAndRoomId($offerId, $slotId, $roomId, $cityId) {
//        $offerIds = $this->offerIdsBySlotId($slotId);
//        $oSqlBuilder = $this->getSQLBuilder();
//        return $oSqlBuilder->select('userId, majId, rn, formNo, rollNo, name, fatherName')
//                        ->from($this->table)
//                        ->whereIN('offerId', $offerIds)
//                        ->where('slotId', $slotId)
//                        ->where('roomId', $roomId)
//                        ->where('cityId', $cityId)
//                        ->whereNotNull('rn')
//                        ->whereNotNull('date')
//                        ->whereNotNull('time')
//                        ->whereNotNull('venue')
//                        ->orderBy('majId, rn', 'ASC')
////                        ->orderBy('userId, rn', 'ASC')
//                        ->findAll();
//    }

    public function applicantsByOfferIdAndSlotNo($offerIds, $slotNo, $cityId, $params = []) {
        $oSqlBuilder = $this->getSQLBuilder();
        $paging = false;
        $fields = 'rn, name, fatherName, venue, roomId, majId, rollNo, formNo';
        if (!empty($params['count'])) {
            $fields = 'count(*) total';
        } elseif (!empty($params['paging'])) {
            $paging = true;
        }
        $oSqlBuilder->select($fields)
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('venue')
                ->orderBy('roomId, majId, rn', 'ASC');
        if ($paging) {
            $oSqlBuilder->limit($params['start'], $params['offset']);
        }
        return $oSqlBuilder->findAll();
    }

    public function applicantsByOfferIdAndSlotNoForExcel($offerIds, $slotNo, $cityId, $fields) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select($fields)
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('venue')
                ->orderBy('roomId, majId, rn', 'ASC');
        return $oSqlBuilder->findAll();
    }

    public function applicantsByOfferIdAndMajorId($offerIds, $majorId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id, formNo, rn, rollNo, name, fatherName')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('majId', $majorId)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->whereNotNull('venue')
//                        ->where('rn', 2500, '>')
                        ->orderBy('rn', 'ASC')
                        ->findAll();
    }
    public function applicantsByOfferIdAndMajorIdByRnRange($offerIds, $majorId, $cityId, $startRn, $endRn) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id, formNo, rn, rollNo, name, fatherName')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('majId', $majorId)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->whereNotNull('venue')
                        ->where('rn', $startRn -1, '>')
                        ->where('rn', $endRn + 1, '<')
                        ->orderBy('rn', 'ASC')
                        ->findAll();
    }

    public function applicantsByOfferIdAndMajorIdForAward($offerId, $majorId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id, userId, formNo, rn, rollNo, name, fatherName, cnic, contactNo, email, major, date, offerId, slotId, slotNo')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majorId)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->whereNotNull('venue')
                        ->where('award', 'NO')
                        ->orderBy('rn', 'ASC')
                        ->findAll();
    }

    public function majorsByOfferIdAndSlotNoAndRoomId($offerId, $slotId, $roomId, $cityId) {
        $offerIds = $this->offerIdsBySlotId($slotId);
        $oSqlBuilder = $this->getSQLBuilder();
        $major = $oSqlBuilder->select('GROUP_CONCAT(distinct major) major')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotId', $slotId)
                ->where('roomId', $roomId)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('venue')
                ->find();

        return $major;
    }

    public function assignTestSchedule($offerId, $majorId, $id) {

        $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
        $testScheduleData = $oMajorsTestScheduleModel->getScheduleByOfferIdAndMajorId($offerId, $majorId);

        if (empty($testScheduleData)) {
            return "Test Schedule is not avaialable for this Major......";
        }

        $total = $this->WOTestSchedulebyOfferIdAndMajorId($offerId, $majorId);
        if ($total > 0) {

            $updateSchedule = $this->updateTestSchedule($testScheduleData, $offerId, $majorId, $id);
            return 'Test Schedule Assigned Successfully to ' . $total . ' Applicants.';
        } else {

            return "No Applicant to Assign Test Schedule......";
        }
    }

    public function assignTestScheduleMulti($offerId, $slotId, $majorId, $cityId, $id) {

        $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
        $testScheduleData = $oMajorsTestScheduleModel->getScheduleByOfferIdAndMajorId($offerId, $majorId, $cityId, $slotId);

        if (empty($testScheduleData)) {
            return "Test Schedule is not avaialable for this Major......";
        }
//        $offerIds = $this->offerIdsBySlotId($slotId);
        $offerIds = $this->offerIdsForAMajorBySlotId($slotId, $majorId);
        $total = $this->WOTestSchedulebyOfferIdsAndMajorIdMulti($offerIds, $majorId, $cityId, $slotId);

        if ($total > 0) {

            $updateSchedule = $this->updateTestScheduleMulti($testScheduleData, $offerIds, $majorId, $cityId, $slotId, $id);
            return 'Test Schedule Assigned Successfully to ' . $total . ' Applicants.';
        } else {

            return "No Applicant to Assign Test Schedule......";
        }
    }

    private function updateTestSchedule($data, $offerId, $majorId, $id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('date', $data['date'])
                ->set('day', $data['day'])
                ->set('time', $data['startTime'])
                ->set('slotNo', $data['slotNo'])
                ->set('scheduleUpdatedBy', $id)
                ->set('scheduleUpdatedOn', date("Y-m-d H:i:s"))
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->whereNotNull('rn')
                ->whereNull('date')
                ->whereNull('time')
                ->update();
        return $updateResponse;
    }

    private function updateTestScheduleMulti($data, $offerIds, $majorId, $cityId, $slotId, $id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('date', $data['date'])
                ->set('day', $data['day'])
                ->set('time', $data['startTime'])
                ->set('scheduleUpdatedBy', $id)
                ->set('scheduleUpdatedOn', date("Y-m-d H:i:s"))
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->where('slotId', $slotId)
                ->whereNotNull('rn')
                ->whereNull('date')
                ->whereNull('time')
                ->update();
        return $updateResponse;
    }

    private function updateTestScheduleMultiCS($data, $offerIds, $majorId, $cityId, $slotId, $id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('a.date', $data['date'])
                ->set('a.day', $data['day'])
                ->set('a.time', $data['startTime'])
                ->set('a.scheduleUpdatedBy', $id)
                ->set('a.scheduleUpdatedOn', date("Y-m-d H:i:s"))
                ->from($this->table . ' a', 'education e')
                ->join('a.userId', 'e.userId')
                ->whereIN('offerId', $offerIds)
                ->where('a.majId', $majorId)
                ->where('a.cityId', $cityId)
                ->where('a.slotId', $slotId)
                ->where('examLevel', 2)
                ->whereNotIn('examClass', 'FSc (Pre Medical)')
                ->whereNotNull('a.rn')
                ->whereNull('a.date')
                ->whereNull('a.time')
                ->update();
        return $updateResponse;
    }

    public function resetTestSchedule($offerId, $majorId, $id) {

        $tot = $this->WithTestSchedulebyOfferIdAndMajorId($offerId, $majorId);
        if ($tot > 0) {
            $oSqlBuilder = $this->getSQLBuilder();
            $updateResponse = $oSqlBuilder->set('date', NULL)
                    ->set('time', NULL)
                    ->set('slotNo', NULL)
                    ->set('scheduleUpdatedBy', $id)
                    ->set('scheduleUpdatedOn', date("Y-m-d H:i:s"))
                    ->from($this->table)
                    ->where('offerId', $offerId)
                    ->where('majId', $majorId)
                    ->where('expired', 'YES')
                    ->update();
            return 'Test Schedule Reset Successfully to ' . $tot . ' Applicants.';
        } else {
            return 'No Applicant Found to Reset Test Schedule.';
        }
    }

    public function resetTestScheduleMulti($offerId, $slotId, $majorId, $cityId, $id) {
        $offerIds = $this->offerIdsBySlotId($slotId);
        $tot = $this->WithTestSchedulebyOfferIdAndMajorIdMulti($offerIds, $majorId, $slotId, $cityId);
        if ($tot > 0) {
            $oSqlBuilder = $this->getSQLBuilder();
            $updateResponse = $oSqlBuilder->set('date', NULL)
                    ->set('time', NULL)
                    ->set('scheduleUpdatedBy', $id)
                    ->set('scheduleUpdatedOn', date("Y-m-d H:i:s"))
                    ->from($this->table)
                    ->whereIN('offerId', $offerIds)
                    ->where('majId', $majorId)
                    ->where('slotId', $slotId)
                    ->where('cityId', $cityId)
                    ->where('expired', 'YES')
                    ->update();
            return 'Test Schedule Reset Successfully to ' . $tot . ' Applicants.';
        } else {
            return 'No Applicant Found to Reset Test Schedule.';
        }
    }

    public function releaseSlipsByOfferIdBySlotNo($offerId, $slotId, $slotNo, $cityId, $expired, $releasedAction, $id) {
        $oTestScheduleModel = new \models\TestScheduleModel();
        $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($slotId);
        $tot = $this->totalExpiredStatusByOfferIdAndSlotNo($offerIds, $slotNo, $cityId, $expired);
        if ($tot > 0) {
            $oSqlBuilder = $this->getSQLBuilder();
            $updateResponse = $oSqlBuilder->set('expired', $expired)
                    ->set('releasedBy', $id)
                    ->set('releasedOn', date("Y-m-d H:i:s"))
                    ->set('releasedAction', $releasedAction)
                    ->from($this->table)
                    ->whereIN('offerId', $offerIds)
                    ->where('slotNo', $slotNo)
                    ->where('cityId', $cityId)
                    ->whereNotNull('date')
                    ->whereNotNull('time')
                    ->whereNotNull('slotNo')
                    ->whereNotNull('rn')
                    ->whereNotNull('venue')
                    ->update();
            return 'Roll No. Slips Updated Successfully to ' . $tot . ' Applicants.';
        } else {
            return 'No Roll No. Slips Found to Update.';
        }
    }

    public function assignMajorWiseRollNo($offerId, $majorId, $id) {
        $rollNoFormat = $this->rollNumberFormatByOfferIdAndMajorId($offerId, $majorId);
        if (empty($rollNoFormat)) {
            return "Roll number format not enetered.";
        }
        $oApplicationsModel = new \models\ApplicationsModel();
        $appData = $oApplicationsModel->allPaidByClassAndMajor($offerId, $majorId);
        if (empty($appData)) {
            return "No Applicant to Assign Roll Number." . $appData;
        }
        $maxRn = $this->maxRnByOfferIdAndMajorId($offerId, $majorId);

        $rn = 1 + $maxRn;
        $tot = 0;
        foreach ($appData as $row) {

            $out = $this->insert(
                    [
                        'userId' => $row['userId'],
                        'formNo' => $row['formNo'],
                        'rn' => $rn,
                        'rollNo' => $rn . $rollNoFormat,
                        'name' => $row['userName'],
                        'fatherName' => $row['fatherName'],
                        'cnic' => $row['cnic'],
                        'majId' => $majorId,
                        'major' => $row['name'],
                        'offerId' => $offerId,
                        'rnUpdatedBy' => $id,
                        'rnUpdatedOn' => date("Y-m-d H:i:s")
                    ]
            );
            if ($out) {
                $oApplicationsModel->upsert(['rn' => $rn], $row['appId']);
                $rn = $rn + 1;
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return 'Roll Number Assigned Successfully to ' . $tot . ' applicants.';
    }

    public function offerIdsBySlotId($slotId) {
        $oTestScheduleModel = new \models\TestScheduleModel();
        $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($slotId);
        return $offerIds;
    }

    public function offerIdsForAMajorBySlotId($slotId, $majorId) {
        $oMajorsTestScheduleModel = new \models\MajorsTestScheduleModel();
        $offerIds = $oMajorsTestScheduleModel->getOfferIdsBySlotIdAndByMajorId($slotId, $majorId);
        return $offerIds;
    }

    public function applicantsCountBySlot($offerIds, $majorId, $cityId, $slotId, $slotNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) tot')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->where('slotId', $slotId)
                ->where('slotNo', $slotNo)
                ->find();

        return $data['tot'];
    }

    public function assignMajorWiseRollNoMulti($offerId, $slotId, $slotNo, $majorId, $cityId, $strength, $id) {
        $rollNoFormat = $this->rollNumberFormatByOfferIdAndMajorId($offerId, $majorId);
        if (empty($rollNoFormat)) {
            return "Roll number format not enetered.";
        } else {
            if ($cityId != 1) {
                $oMetaDataModel = new \models\MetaDataModel();
                $cityName = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $cityId);
                $rollNoFormat = $rollNoFormat . '-' . substr($cityName['keyDesc'], 0, 1);
            }
        }
//        $offerIds = $this->offerIdsBySlotId($slotId);
        $offerIds = $this->offerIdsForAMajorBySlotId($slotId, $majorId);

        $oApplicationsModel = new \models\ApplicationsModel();
//        if ($majorId == 52 && $cityId == 1) {
//            $appData = $oApplicationsModel->allPaidByMultiOfferIdsAndMajorCS($offerIds, $majorId, $cityId);
//        } else {
//            $appData = $oApplicationsModel->allPaidByMultiOfferIdsAndMajor($offerIds, $majorId, $cityId);
//        }
        $appData = $oApplicationsModel->allPaidByMultiOfferIdsAndMajor($offerIds, $majorId, $cityId);
        if (empty($appData)) {
            return "No Applicant to Assign Roll Number.";
        }
        $tot = 0;
        if ($strength > 0) {
            $tot = $this->applicantsCountBySlot($offerIds, $majorId, $cityId, $slotId, $slotNo);
        }
//        echo $strength;
//        echo "<br>";
//        echo $tot;
//        exit;
        $inserts = 0;
        $maxRn = $this->maxRnBySlotIdAndMajorId($offerIds, $majorId, $cityId);

        $rn = 1 + $maxRn;
        foreach ($appData as $row) {
            if ($strength > 0 && $tot >= $strength) {
                break;
            }
            $out = $this->insert(
                    [
                        'userId' => $row['userId'],
                        'formNo' => $row['formNo'],
                        'rn' => $rn,
                        'rollNo' => $rn . $rollNoFormat,
                        'name' => $row['userName'],
                        'fatherName' => $row['fatherName'],
                        'contactNo' => $row['contactNo'],
                        'email' => $row['email'],
                        'cnic' => $row['cnic'],
                        'cityId' => $cityId,
                        'majId' => $majorId,
                        'major' => $row['name'],
                        'baseId' => $row['baseId'],
                        'base' => $row['baseName'],
                        'slotId' => $slotId,
                        'slotNo' => $slotNo,
                        'offerId' => $row['offerId'],
                        'rnUpdatedBy' => $id,
                        'rnUpdatedOn' => date("Y-m-d H:i:s")
                    ]
            );
            if ($out) {
                $oApplicationsModel->upsert(['rn' => $rn], $row['appId']);
                $rn = $rn + 1;
                $tot++;
                $inserts++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return 'Roll Number Assigned Successfully to ' . $inserts . ' applicants.';
    }

    public function assignRollNoByFormNo($cityId, $formNo, $rn, $id) {

        $oApplicationsModel = new \models\ApplicationsModel();
        $appData = $oApplicationsModel->paidApplicantByFormNo($formNo);
        if (empty($appData)) {
            return "No Applicant to Assign Roll Number.";
        }

        $slipData = $this->findByFormNo($formNo);
        if (!empty($slipData)) {
            return "This Form Already Exist for Roll Number Slip.";
        }

        $oMajorTestSchedule = new \models\MajorsTestScheduleModel();
        $slotData = $oMajorTestSchedule->getScheduleByOfferIdAndMajorIdAndCityId($appData['offerId'], $appData['majId'], $cityId);
        $offerIds = $this->offerIdsBySlotId($slotData['slotId']);

        $rnData = $this->isRNAlreadyExistByOfferIdAndMajorIdAndCityId($offerIds, $appData['majId'], $cityId, $rn);
        if (!empty($rnData)) {
            return "This Roll Number Alread Exist.";
        }

        $rollNoFormat = $this->rollNumberFormatByOfferIdAndMajorId($appData['offerId'], $appData['majId']);
        if (empty($rollNoFormat)) {
            return "Roll number format not enetered.";
        } else {
            if ($cityId != 1) {
                $oMetaDataModel = new \models\MetaDataModel();
                $cityName = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $cityId);
                $rollNoFormat = $rollNoFormat . '-' . substr($cityName['keyDesc'], 0, 1);
            }
        }

        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $venueData = $oRoomsAllocationModel->getVacantRoomsbyOfferIdAndSlotNo($offerIds, $slotData['slotNo'], $cityId, 'SINGLE');
        if (empty($venueData)) {
            return "No Room Avaialable to Assign.";
        }

        $out = $this->insert(
                [
                    'userId' => $appData['userId'],
                    'formNo' => $appData['formNo'],
                    'rn' => $rn,
                    'rollNo' => $rn . $rollNoFormat,
                    'name' => $appData['userName'],
                    'fatherName' => $appData['fatherName'],
                    'cnic' => $appData['cnic'],
                    'cityId' => $cityId,
                    'majId' => $appData['majId'],
                    'major' => $appData['name'],
                    'baseId' => $appData['baseId'],
                    'base' => $appData['baseName'],
                    'offerId' => $appData['offerId'],
                    'rnUpdatedBy' => $id,
                    'expired' => 'NO',
                    'rnUpdatedOn' => date("Y-m-d H:i:s"),
                    'slotId' => $slotData['slotId'],
                    'slotNo' => $slotData['slotNo'],
                    'date' => $slotData['date'],
                    'day' => $slotData['day'],
                    'time' => $slotData['startTime'],
                    'roomId' => $venueData[0]['roomId'],
                    'venue' => $venueData[0]['venue'],
                    'scheduleUpdatedBy' => $id,
                    'scheduleUpdatedOn' => date("Y-m-d H:i:s")
                ]
        );

        if ($out) {
            $oApplicationsModel->upsert(['rn' => $rn], $appData['appId']);
            $updateRoomsAllocation = $oRoomsAllocationModel->updateAllottedRoom($venueData[0]['id'], 1 + $venueData[0]['allotted'], $venueData[0]['diff'] - 1);
        } else {
            $this->deleteByPK($out);
        }

        return 'Roll Number Assigned Successfully';
    }

    public function deleteMajorWiseRollNo($offerId, $majorId, $id) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();

        $oApplicationsModel = new \models\ApplicationsModel();
        $total = $oApplicationsModel->totalRNByOfferIdAndMajorId($offerId, $majorId);

        if (empty($total)) {
            return "No Applicant to Reset Roll Number.";
        }

        $updateResponseRN = $oApplicationsModel->resetRNByOfferIdAndMajorId($offerId, $majorId);

        $deleteResponse = $oSqlBuilder->select('')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->where('expired', 'YES')
                ->delete();

        if ($updateResponseRN && $deleteResponse) {
            $oSqlBuilder->commit();
            return 'Roll Number Reset Successfully for ' . $total . ' Applicants.';
        } else {
            $oSqlBuilder->rollback();
            return 'Nothing to Reset OR Some Internal Error.';
        }
    }

    public function deleteMajorWiseRollNoMulti($slotId, $majorId, $cityId) {

//        $offerIds = $this->offerIdsBySlotId($slotId);
        offerIdsForAMajorBySlotId($slotId, $majorId);
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();

        $oApplicationsModel = new \models\ApplicationsModel();
        $total = $oApplicationsModel->totalRNByMultiOfferIdsAndMajorId($offerIds, $majorId, $slotId, $cityId);
        if (empty($total)) {
            return "No Applicant to Reset Roll Number.";
        }

        $updateResponseRN = $oApplicationsModel->resetRNByMultiOfferIdsAndMajorId($offerIds, $majorId, $slotId, $cityId);
        $deleteResponse = $oSqlBuilder->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->where('slotId', $slotId)
                ->where('expired', 'YES')
                ->delete();
        if ($updateResponseRN && $deleteResponse) {
            $oSqlBuilder->commit();
            return 'Roll Number Reset Successfully for ' . $total . ' Applicants.';
        } else {
            $oSqlBuilder->rollback();
            return 'Nothing to Reset OR Some Internal Error.';
        }
    }

    private function assignVenueByOfferIdAndSlotNoMulti($offerIds, $slotNo, $rooms, $cityId, $id) {
        $oSQLBuilder = $this->getSQLBuilder();
        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        foreach ($rooms as $room) {
            $appData = $this->getByOfferIdAndSlotNoWOVenueMulti($offerIds, $slotNo, $cityId);
//            echo "<pre>";
//            print_r($appData);exit;
            $totalAppData = sizeof($appData);
            $i = 0;
            foreach ($appData as $row) {
                $oSQLBuilder = $this->getSQLBuilder();
                $updateResponse = $oSQLBuilder->set('venue', $room['venue'])
                        ->set('roomId', $room['roomId'])
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('slotNo', $slotNo)
                        ->where('userId', $row['userId'])
                        ->update();
//                $oSQLBuilder->printQuery();exit;                
                $i = $i + 1;
                if ($i == $totalAppData) {
                    $diff = $room['diff'] - $i;
                    $updateRoomsAllocation = $oRoomsAllocationModel->updateAllottedRoom($room['id'], $i + $room['allotted'], $diff);
                    $i = 0;
                    break 2;
                }
                if ($i == $room['diff']) {
//                    $diff = $room['capacity'] - $i;
                    $diff = $room['diff'] - $i;
                    $updateRoomsAllocation = $oRoomsAllocationModel->updateAllottedRoom($room['id'], $i + $room['allotted'], $diff);
                    $i = 0;
                    break 1;
                }
            }
        }
    }

    private function assignVenueByOfferIdAndSlotNoSingle($offerIds, $slotNo, $rooms, $cityId, $id) {
        $perPage = 100;
        $offset = 0;
        $oSQLBuilder = $this->getSQLBuilder();
//        $out = $this->countByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo);
        //$appData = $this->getByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo, $offset, $perPage);
        // $tot = sizeof($appData);
//        $tot = $out['tot'];
        // $totalIteratios = ceil($tot / $perPage);
        foreach ($rooms as $room) {
            // for ($k = 0; $k < $totalIteratios; $k++) {
            $appData = $this->getByOfferIdAndSlotNoWOVenueSingle($offerIds, $slotNo, $cityId);
//            $appData = $this->getByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo, $offset, $room['diff']);
//            echo "<pre>";
//            print_r($appData);exit;
            $totalAppData = sizeof($appData);
            $i = 0;
            foreach ($appData as $row) {
                $oSQLBuilder = $this->getSQLBuilder();
                $updateResponse = $oSQLBuilder->set('venue', $room['venue'])
                        ->set('roomId', $room['roomId'])
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('slotNo', $slotNo)
                        ->where('userId', $row['userId'])
                        ->update();
//                $oSQLBuilder->printQuery();exit;
                $i = $i + 1;
                if ($i == $totalAppData) {
                    $diff = $room['diff'] - $i;
                    $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                    $updateRoomsAllocation = $oRoomsAllocationModel->updateAllottedRoom($room['id'], $i + $room['allotted'], $diff);
                    $i = 0;
                    break 2;
                }
                if ($i == $room['diff']) {
//                        $diff = $room['capacity'] - $i;
//                        $diff = $room['capacity'] - $i;
                    $diff = 0;
//                        $diff = $room['capacity'] - $i;
                    $oRoomsAllocationModel = new \models\RoomsAllocationModel();
                    $updateRoomsAllocation = $oRoomsAllocationModel->updateAllottedRoom($room['id'], $i + $room['allotted'], $diff);
                    $i = 0;
                    break 1;
                }
            }
            // $offset += $perPage;
            //}
        }
    }

//    public function assignVenueByOfferIdAndSlotNo($offerId, $slotNo, $id) {
//
//        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
//        $allottedRooms = $oRoomsAllocationModel->getVacantRoomsbyOfferIdAndSlotNo($offerId, $slotNo, 'MULTI');
//        $this->assignVenueByOfferIdAndSlotNoMulti($offerId, $slotNo, $allottedRooms, $id);
//        $allottedRoomsSingle = $oRoomsAllocationModel->getVacantRoomsbyOfferIdAndSlotNo($offerId, $slotNo, 'SINGLE');
//        $this->assignVenueByOfferIdAndSlotNoSingle($offerId, $slotNo, $allottedRoomsSingle, $id);
//    }
    public function assignVenueByOfferIdAndSlotNo($offerId, $slotId, $slotNo, $cityId, $id) {
        $offerIds = $this->offerIdsBySlotId($slotId);
        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $allottedRooms = $oRoomsAllocationModel->getVacantRoomsbyOfferIdAndSlotNo($offerIds, $slotNo, $cityId, 'MULTI');
        $this->assignVenueByOfferIdAndSlotNoMulti($offerIds, $slotNo, $allottedRooms, $cityId, $id);
        $allottedRoomsSingle = $oRoomsAllocationModel->getVacantRoomsbyOfferIdAndSlotNo($offerIds, $slotNo, $cityId, 'SINGLE');
        $this->assignVenueByOfferIdAndSlotNoSingle($offerIds, $slotNo, $allottedRoomsSingle, $cityId, $id);
    }

    public function countApplicantsByOfferIdAndSlotNo($offerId, $slotNo) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) tot')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->find();

        return $data;
    }

    public function countApplicantsWithVenueByOfferIdAndSlotNo($offerIds, $slotNo, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) tot')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->whereNotNull('venue')
                ->find();

        return $data;
    }

    public function countDistinctApplicantsWithVenueByOfferIdAndSlotNo($offerId, $slotNo) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(distinct userId) tot')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->whereNotNull('venue')
                ->find();

        return $data;
    }

    public function countDistinctApplicantsWithoutVenueByOfferIdAndSlotNo($offerId, $slotNo) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(distinct userId) tot')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->whereNull('venue')
                ->find();

        return $data;
    }

    public function countDistinctApplicantsByOfferIdAndSlotNo($offerId, $slotId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(distinct userId) tot')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->find();

        return $data;
    }

    public function countDistinctApplicantsBySlotId($slotId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(distinct userId) tot')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->find();

        return $data;
    }

    public function deleteVenueByOfferIdAndSlotNo($offerId, $slotId, $slotNo, $cityId, $id) {
        $oTestScheduleModel = new \models\TestScheduleModel();
        $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($slotId);
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->beginTransaction();

        $total = $this->countApplicantsWithVenueByOfferIdAndSlotNo($offerIds, $slotNo, $cityId);

        if (empty($total)) {
            return "No Applicant Exist to Reset Venue.";
        }

        $updateVenueResponse = $oSQLBuilder->set('venue', NULL)
                ->set('roomId', NULL)
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->where('expired', 'YES')
                ->update();

        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $updateRoomResponse = $oRoomsAllocationModel->resetAllottedRoomsByOfferIdAndSlotNo($offerId, $slotNo, $cityId, $id);

        if ($updateVenueResponse && $updateRoomResponse) {
            $oSQLBuilder->commit();
            return 'Test Venue Deleted Successfully for  ' . $total['tot'] . ' Applicants.';
        } else {
            $oSQLBuilder->rollback();
            return 'Nothing to Reset OR Some Internal Error.....';
        }
    }

    public function getByOfferIdAndSlotNoMulti($offerIds, $slotNo, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('count(userId) cnt, userId')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('slotNo', $slotNo)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('rollNo')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->groupBy('userId')
                        ->having('cnt', 1, '>')
                        ->orderBy('userId')
                        ->findAll();
//        $oSqlBuilder->printQuery();exit;
    }

    private function getByOfferIdAndSlotNoWOVenueMulti($offerIds, $slotNo, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('count(userId) cnt, userId')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('slotNo', $slotNo)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('rollNo')
                        ->whereNull('venue')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->groupBy('userId')
                        ->having('cnt', 1, '>')
                        ->orderBy('userId')
                        ->findAll();
//        $oSqlBuilder->printQuery();exit;
    }

    public function getByOfferIdAndSlotNoMultiMajorsApplicants($offerId, $slotId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('count(userId) cnt, userId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotId', $slotId)
                        ->whereNotNull('rn')
                        ->whereNotNull('rollNo')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->groupBy('userId, baseId')
                        ->having('cnt', 1, '>')
                        ->orderBy('userId')
                        ->findAll();
    }

    public function getBySlotNoMultiMajorsApplicants($slotId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('count(distinct userId) cnt, userId')
                        ->from($this->table)
                        ->where('slotId', $slotId)
                        ->where('cityId', $cityId)
                        ->whereNotNull('rn')
                        ->whereNotNull('rollNo')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->groupBy('userId, baseId')
                        ->having('cnt', 1, '>')
                        ->orderBy('userId')
                        ->findAll();
    }

    public function countByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('count(distinct userId) tot')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotNo', $slotNo)
                        ->whereNotNull('rn')
                        ->whereNotNull('rollNo')
                        ->whereNull('venue')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->find();
        //$oSqlBuilder->printQuery();
    }

    public function getByOfferIdAndSlotNoSingleMajorApplicants($offerId, $slotId) {
//    private function getByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo, $offset, $perPage) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(userId) cnt, userId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->whereNotNull('rn')
                ->whereNotNull('rollNo')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->groupBy('userId, baseId')
                ->having('cnt', 1, '=')
                ->orderBy('userId')
                ->findAll();

        return ($data);
    }

    public function getBySlotNoSingleMajorApplicants($slotId, $cityId) {
//    private function getByOfferIdAndSlotNoWOVenueSingle($offerId, $slotNo, $offset, $perPage) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(distinct userId) cnt, userId')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('rollNo')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->groupBy('userId, baseId')
                ->having('cnt', 1, '=')
                ->orderBy('userId')
                ->findAll();

        return ($data);
    }

    private function getByOfferIdAndSlotNoWOVenueSingle($offerIds, $slotNo, $cityId) {
//    
        $arr = [];
//        
        $sortData = $this->getSortedDataByOfferIdAndSlotNoWOVenueSingle($offerIds, $slotNo, $cityId, $arr);

        return ($sortData);
    }

    private function getSortedDataByOfferIdAndSlotNoWOVenueSingle($offerIds, $slotNo, $cityId, $usersIdArr) {
        $oSqlBuilder = $this->getSQLBuilder();

//        $data = $oSqlBuilder->select('userId, rn, majId')
        $data = $oSqlBuilder->select('userId, rn, majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('rollNo')
                ->whereNull('venue')
//                ->whereIN('userId', $usersIdArr)
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->orderBy('majId, rn')
                ->findAll();
        return $data;
    }

    private function maxRnByOfferIdAndMajorId($offerId, $majorId, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('max(rn) maxRn')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->find();

        return $data['maxRn'];
    }

    private function maxRnBySlotIdAndMajorId($offerIds, $majorId, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('max(rn) maxRn')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->find();

        return $data['maxRn'];
    }

    private function WOTestSchedulebyOfferIdAndMajorId($offerId, $majorId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->whereNull('date')
                ->whereNull('time')
                ->whereNull('slotNo')
                ->find();

        return $data['totalRn'];
    }

    private function WOTestSchedulebyOfferIdsAndMajorIdMulti($offerIds, $majorId, $cityId, $slotId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->where('slotId', $slotId)
                ->whereNull('date')
                ->whereNull('time')
                ->find();
        return $data['totalRn'];
    }

    private function WithTestSchedulebyOfferIdAndMajorId($offerId, $majorId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('slotNo')
                ->where('expired', 'YES')
                ->find();

        return $data['totalRn'];
    }

    private function WithTestSchedulebyOfferIdAndMajorIdMulti($offerIds, $majorId, $slotId, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majorId)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('slotNo')
                ->where('expired', 'YES')
                ->find();

        return $data['totalRn'];
    }

    private function totalExpiredStatusByOfferIdAndSlotNo($offerIds, $slotNo, $cityId, $expired) {
        $_expired[] = $expired;
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->whereNotIn('expired', $_expired)
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('slotNo')
                ->whereNotNull('rn')
                ->whereNotNull('venue')
                ->find();
//        $oSQLBuilder->printQuery();exit;
        return $data['totalRn'];
    }

    private function rollNumberFormatByOfferIdAndMajorId($offerId, $majorId) {

        $oMajorsModel = new \models\MajorsModel();
        $rnFormat = $oMajorsModel->getRollNoFormatByOfferIdAndMajorId($offerId, $majorId);

        return ($rnFormat);
    }

    public function applicantsWithRnByOfferId($offerId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cityId', $cityId)
                ->whereNotIN('baseId', [1, 3, 16, 17])
                ->whereNotNull('rn')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithRnByOfferIdsMulti($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->groupBy('majId')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithRnByMultiOfferIds($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithScheduleByOfferId($offerId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('offerId', $cityId)
                ->whereNotNull('date')
                ->whereNotNull('slotNo')
                ->whereNotNull('time')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithScheduleByMultiOfferIds($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('cityId', $cityId)
                ->whereNotNull('date')
                ->whereNotNull('slotNo')
                ->whereNotNull('time')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithVenueByOfferId($offerId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cityId', $cityId)
                ->whereNotNull('date')
                ->whereNotNull('slotNo')
                ->whereNotNull('time')
                ->whereNotNull('venue')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsWithVenueByMultiOfferIds($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('cityId', $cityId)
                ->whereNotNull('date')
                ->whereNotNull('slotNo')
                ->whereNotNull('time')
                ->whereNotNull('venue')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function countApplicantsByOfferIdSlotWise($offerId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) total, slotNo')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->groupBy('slotNo')
                ->findAll();
//        echo '<pre>';
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['slotNo']] = $row['total'];
        }
        return $arr;
    }

    public function countApplicantsBySlotId($slotId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) total')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->find();

        return $data['total'];
    }

    public function countApplicantsByOfferIdWithVenueSlotWise($offerId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) total, slotNo')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->whereNotNull('venue')
                ->groupBy('slotNo')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['slotNo']] = $row['total'];
        }
        return $arr;
    }

    public function countApplicantsBySlotIdWithVenue($slotId, $cityId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(userId) total')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->whereNotNull('venue')
                ->find();
        return $data['total'];
    }

    public function countApplicantsByOfferIdWithVenueSlotAndVenueWise($offerIds, $slotNo, $cityId, $employees = []) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.roomId, a.venue, major, count(distinct userId) total, capacity, sortOrder')
                ->from($this->table . ' a', 'roomsAllocation ra')
                ->join('a.roomId', 'ra.roomId')
                ->join('a.slotId', 'ra.slotId')
                ->join('a.slotNo', 'ra.slotNo')
                ->whereIN('a.offerId', $offerIds)
                ->where('a.slotNo', $slotNo)
                ->where('a.cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('a.venue')
                ->groupBy('a.roomId, a.venue, capacity, major, sortOrder')
                ->orderBy('sortOrder')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        if (!empty($employees)) {
            $newArr = [];
            foreach ($arr as $key => $row) {
                $newArr[$key]['employees'] = $employees[$key];
                $newArr[$key]['details'] = $row;
            }
            $arr = $newArr;
        }
        return $arr;
    }

    public function countApplicantsBySlotIdWithVenueSlotAndVenueWise($slotId, $depttId, $employees = []) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.roomId, a.venue, major, count(distinct userId) total, capacity')
                ->from($this->table . ' a', 'roomsAllocation ra', 'employeeTestDuty e')
                ->join('a.roomId', 'ra.roomId')
                ->join('a.slotId', 'ra.slotId')
                ->join('a.slotNo', 'ra.slotNo')
                ->join('a.slotId', 'e.slotId')
                ->join('a.roomId', 'e.roomId')
                ->where('a.slotId', $slotId)
                ->where('e.depttId', $depttId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('a.venue')
                ->groupBy('a.roomId, a.venue, capacity, major')
                ->orderBy('a.roomId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        if (!empty($employees)) {
            $newArr = [];
            foreach ($arr as $key => $row) {
                $newArr[$key]['employees'] = $employees[$key];
                $newArr[$key]['details'] = $row;
            }
            $arr = $newArr;
        }
        return $arr;
    }

    public function countApplicantsByOfferIdAndCityIdWithVenue($offerId, $cityId, $depttId, $employees = []) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.roomId, a.venue, major, count(distinct userId) total, capacity, a.slotNo')
                ->from($this->table . ' a', 'roomsAllocation ra', 'employeeTestDuty e')
                ->join('a.roomId', 'ra.roomId')
                ->join('a.slotId', 'ra.slotId')
                ->join('a.slotNo', 'ra.slotNo')
                ->join('a.slotId', 'e.slotId')
                ->join('a.roomId', 'e.roomId')
                ->where('a.offerId', $offerId)
                ->where('e.depttId', $depttId)
                ->where('e.cityId', $cityId)
                ->whereNotNull('rn')
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->whereNotNull('a.venue')
                ->groupBy('a.slotNo, a.roomId, a.venue, capacity, major')
                ->orderBy('a.roomId')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        //        echo "<pre>";
//        print_r($data);exit;   
        if (!empty($employees)) {
            $newArr = [];
            foreach ($arr as $key => $row) {
                $newArr[$key]['employees'] = $employees[$key];
                $newArr[$key]['details'] = $row;
            }
            $arr = $newArr;
        }
        return $arr;
    }

    public function totalAwardByOfferIdAndMajorId($offerId, $majId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $totalApplicants = $oSqlBuilder->select('count(rn) total')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
                ->where('award', 'YES')
                ->find();

        return $totalApplicants['total'];
    }

    public function resetAwardByOfferIdAndMajorId($offerId, $majId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('award', 'NO')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
                ->update();
        return $updateResponse;
    }
}
