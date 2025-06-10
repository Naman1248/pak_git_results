<?php

/**
 * Description of SeatingPlanController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp\services;

class SeatingPlanController extends \controllers\cp\SuperControlller {

    public function deleteRoomSelectionAction() {
        $post = $this->post()->all();
        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $out = $oRoomsAllocationModel->deleteByPK($post['raId']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Room Selection Deleted Successfully']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please Try Again.']);
        }
    }

    public function deleteMajorWiseAwardAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatResultModel = new \models\gatResultModel();
        $out = $oGatResultModel->deleteMajorWiseAward($post['offerId'], $post['majorId'], $post['cityId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function shiftGATMajorWiseAwardAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatResultModel = new \models\gatResultModel();
        $out = $oGatResultModel->shiftMajorWiseAward($post['offerId'], $post['majorId'], $post['cityId'], $id);

        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

//    public function saveMajorWiseRollNoAction() {
//        $post = $this->post()->all();
//        $id = $this->state()->get('depttUserInfo')['id'];
//        $oGatSlipModel = new \models\GatSlipModel();
//        $out = $oGatSlipModel->assignMajorWiseRollNo($post['offerId'], $post['majorId'], $post['cityId'], $id);
//        $this->printAndDieJsonResponse(true, ['msg' => $out]);
//    }

    public function saveMajorWiseRollNoMultiAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignMajorWiseRollNoMulti($post['offerId'], $post['slotId'], $post['slotNo'], $post['majorId'], $post['cityId'], $post['strength'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function deleteMajorWiseRollNoMultiAction() {
        $post = $this->post()->all();
//        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->deleteMajorWiseRollNoMulti($post['slotId'], $post['majorId'], $post['cityId']);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function deleteMajorWiseRollNoAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->deleteMajorWiseRollNo($post['offerId'], $post['majorId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function deleteEmployeeTestDutyAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oEmployeeTestDutyModel = new \models\EmployeeTestDutyModel();
        $out = $oEmployeeTestDutyModel->deleteByPK($post['empTestId']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Employee Test Duty Deleted Successfully']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please Try Again.']);
        }
    }

    public function saveMajorWiseTestScheduleAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignTestSchedule($post['offerId'], $post['majorId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function saveMajorWiseTestScheduleMultiAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignTestScheduleMulti($post['offerId'], $post['slotId'], $post['majorId'], $post['cityId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function saveSlotWiseVenueAction() {
        $post = $this->post()->all();
//        print_r($post);exit;
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignVenueByOfferIdAndSlotNo($post['offerId'], $post['slotId'], $post['slotNo'], $post['cityId'], $id);
//        $out = $this->assignGATVenue($post['offerId'], $post['slotId'], $post['slotNo'], $post['cityId'], $id);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Test Schedule Assigned Successfully.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function deleteSlotWiseVenueAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $out = $this->deleteGATVenue($post['offerId'], $post['slotId'], $post['slotNo'], $post['cityId'], $id);

        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Test Venue Reset Successfully.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

//    private function assignGATVenue($offerId, $slotNo, $id) {
//        $oGatSlipModel = new \models\GatSlipModel();
//        $out = $oGatSlipModel->assignVenueByOfferIdAndSlotNo($offerId, $slotNo, $id);
//        return $out;
//    }
    private function assignGATVenue($offerId, $slotId, $slotNo, $cityId, $id) {
        $oGatSlipModel = new \models\GatSlipModel();
//        $out = $oGatSlipModel->assignVenueByOfferIdAndSlotNo($offerId, $slotNo, $id);
        $out = $oGatSlipModel->assignVenueByOfferIdAndSlotNo($offerId, $slotId, $slotNo, $cityId, $id);
        return $out;
    }

    private function deleteGATVenue($offerId, $slotId, $slotNo, $cityId, $id) {

        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->deleteVenueByOfferIdAndSlotNo($offerId, $slotId, $slotNo, $cityId, $id);

        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function deleteMajorWiseTestScheduleAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->resetTestSchedule($post['offerId'], $post['majorId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function deleteMajorWiseTestScheduleMultiAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->resetTestScheduleMulti($post['offerId'], $post['slotId'], $post['majorId'], $post['cityId'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function toggleExpiredSlipsSlotWiseAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->releaseSlipsByOfferIdBySlotNo($post['offerId'], $post['slotId'], $post['slotNo'], $post['cityId'], $post['expired'], $post['releasedAction'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function toggleExpiredResultSlotWiseAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatResultModel = new \models\gatResultModel();
        $out = $oGatResultModel->releaseResultByOfferIds($post['slotId'], $post['slotNo'], $post['expired'], $post['releasedAction'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function saveRoomSelectionAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oRoomSelectionModel = new \models\RoomsAllocationModel();
        if (!empty($post['roomId']) && !empty($post['sortOrder'])) {
            if (!empty($post['alottedCapacity'])) {
                $post['capacity'] = $post['allottedCapacity'];
            }
            $out = $oRoomSelectionModel->upsert(
                    [
                        'roomId' => $post['roomId'],
                        'capacity' => empty($post['allottedCapacity']) ? $post['capacity'] : $post['allottedCapacity'],
                        'diff' => empty($post['allottedCapacity']) ? $post['capacity'] : $post['allottedCapacity'],
                        'venue' => $post['venue'],
                        'offerId' => $post['offerId'],
                        'roomFor' => empty($post['roomFor']) ? 'SINGLE' : strtoupper($post['roomFor']),
                        'slotId' => $post['slotId'],
                        'slotNo' => $post['slotNo'],
                        'cityId' => $post['cityId'],
                        'sortOrder' => $post['sortOrder'],
                        'selectedBy' => $id,
                        'selectedOn' => date("Y-m-d H:i:s")
                    ], $post['roomIAlottedId']);
        }

        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Room Selection Added Successfully']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please Try Again.']);
        }
    }

    public function saveMajorRollNoFormatAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oMajorsModel = new \models\MajorsModel();
        if (!empty($post['rnFormat'])) {
            $out = $oMajorsModel->upsert(
                    [
                        'rnFormat' => $post['rnFormat'],
                        'rnFormatBy' => $id,
                        'rnFormatUpdatedOn' => date("Y-m-d H:i:s")
                    ], $post['appId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Roll Number Format Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveTestScheduleForMajorAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oMajorsModel = new \models\MajorsModel();
//        if (!empty($post['slotNo'])) {
        $out = $oMajorsModel->upsert(
                [
                    'date' => empty($post['testDate']) ? NULL : $post['testDate'],
                    'day' => $post['testDay'],
                    'startTime' => $post['testStartTime'],
                    'endTime' => $post['testEndTime'],
                    'slotNo' => $post['slotNo']
//                        'rnFormatBy' => $id,
//                        'rnFormatUpdatedOn' => date("Y-m-d H:i:s")
                ], $post['appId']);
//        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Test Schedule For This Major Updated Scuccessfully.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveGATResultAction() {

        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $total = (!empty($post['subject']) ? $post['subject'] : 0) + (!empty($post['compulsory']) ? $post['compulsory'] : 0);
        $ogatResultModel = new \models\gatResultModel();

        $this->checkValidMarks($total, $post['testTotal']);

        if (!empty($post['compulsory']) && !empty($post['subject'])) {

            $this->checkValidMarks($post['compulsory'], $post['compTotal']);
            $this->checkValidMarks($post['subject'], $post['subTotal']);

            $out = $ogatResultModel->upsert(
                    [
                        'compulsory' => !empty($post['compulsory']) ? $post['compulsory'] : 0,
                        'subject' => !empty($post['subject']) ? $post['subject'] : 0,
                        'total' => $total,
                        'testTotal' => $post['testTotal'],
                        'attendance' => 'Yes',
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $data = $ogatResultModel->findByPK($post['appId']);
                $ogatResultModel->GATResultCalculator($data['id'], $post['passPer']);
            }
        } else {
            $out = $ogatResultModel->upsert(
                    [
                        'attendance' => 'No',
                        'status' => 'Absent',
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
        }
        if ($out) {
            $data = $ogatResultModel->findByPK($post['appId']);
            $oUGTResultModel = new \models\UGTResultModel();
            $ugtData = $oUGTResultModel->findOneByField('formNo', $data['formNo'], 'appId');
            if (!empty($ugtData)) {
                $ugtData = $oUGTResultModel->updateResultInfo($ugtData, $data);
            }
            $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveInterResultAction() {

        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $ogatResultModel = new \models\gatResultModel();
        if (!empty($post['compulsory'])) {

            $this->checkValidMarks($post['compulsory'], $post['compTotal']);

            $out = $ogatResultModel->upsert(
                    [
                        'compulsory' => !empty($post['compulsory']) ? $post['compulsory'] : 0,
                        'total' => !empty($post['compulsory']) ? $post['compulsory'] : 0,
                        'testTotal' => $post['testTotal'],
                        'attendance' => 'Yes',
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $data = $ogatResultModel->findByPK($post['appId']);
                $ogatResultModel->GATResultCalculator($data['id'], $post['passPer']);
            }
        } else {
            $out = $ogatResultModel->upsert(
                    [
                        'attendance' => 'No',
                        'status' => 'Absent',
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
        }
        if ($out) {
            $data = $ogatResultModel->findByPK($post['appId']);
            $oUGTResultModel = new \models\UGTResultModel();
            $ugtData = $oUGTResultModel->findOneByField('formNo', $data['formNo'], 'appId');
            if (!empty($ugtData)) {
                $ugtData = $oUGTResultModel->updateResultInfo($ugtData, $data);
            }
            $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    private function checkValidMarks($obt, $tot) {

        if ($obt > $tot) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Obtained Marks is Greater Than Total Marks.']);
        }
    }

    public function resetGATResultAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $ogatResultModel = new \models\gatResultModel();
        $out = $ogatResultModel->resetGATResult($post['appId'], $id);

        if ($out) {
            $data = $ogatResultModel->findByPK($post['appId']);
            $oUGTResultModel = new \models\UGTResultModel();
            $ugtData = $oUGTResultModel->findOneByField('formNo', $data['formNo'], 'appId');
            if (!empty($ugtData)) {
                $ugtData = $oUGTResultModel->updateResultInfo($ugtData, $data);
            }
            $this->printAndDieJsonResponse(true, ['msg' => 'Result Reset Successfully.', 'data' => $data]);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

}
