<?php

/**
 * Description of GATController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class GATController extends \controllers\cp\StateController {

    public function addSeatingPlanAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $post = $this->post()->all();

        if ($this->isPost()) {
            $oMajorsModel = new \models\MajorsModel();
            $post['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($post['offerId'], $post['majorId']);
            $oGatSlipModel = new \models\GatSlipModel();
            $params = [
                'name' => $post['name'],
                'fatherName' => $post['fname'],
                'cnic' => $post['cnic'],
                'userId' => $post['userid'],
                'majId' => $post['majorId'],
                'offerId' => $post['offerId'],
                'date' => $post['testDate'],
                'time' => $post['testTime'],
                'venue' => $post['testVenue'],
                'rollNo' => $post['rno'],
                'major' => $post['majorName'],
            ];
            $oApplicationsModel = new \models\ApplicationsModel();
            $result = $oApplicationsModel->byUserIdAndOfferIdAndMajorId($post['userid'], $post['offerId'], $post['majorId']);
            if ($result) {
                $out = $oGatSlipModel->insert($params);
                if ($out) {
                    $data['addMsg'] = 'Record added successfully.';
                } else {
                    $data['errorMsg'] = 'Record not added, please try again..';
                }
            } else {
                $data['errorMsg'] = 'Application does not exist for this user.';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getGATOpenning($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('addSeatingPlan', $data);
    }

    public function addGATResultAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        $post = $this->post()->all();

        if ($this->isPost()) {
            $oMajorsModel = new \models\MajorsModel();
            $post['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($post['offerId'], $post['majorId']);
            $oGatResultModel = new \models\gatResultModel();
            $params = [
                'userId' => $post['userid'],
                'rollNo' => $post['rno'],
                'name' => $post['name'],
                'fatherName' => $post['fname'],
                'cnic' => $post['cnic'],
                'majId' => $post['majorId'],
                'major' => $post['majorName'],
                'compulsory' => $post['compMarks'],
                'subject' => $post['subMarks'],
                'total' => $post['testObt'],
                'testTotal' => $post['testTotal'],
                'status' => $post['testResult'],
                'testDate' => $post['testDate'],
                'offerId' => $post['offerId'],
                'updatedBy' => $data['id'],
                'updatedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oGatResultModel->insert($params);
            if ($out) {
                $data['addMsg'] = 'Record added successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getGATOpenning($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('addGATResult', $data);
    }

    public function updateGATResultAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $oGatResultModel = new \models\gatResultModel();
            $params = [
                'compulsory' => $post['compMarks'],
                'subject' => $post['subMarks'],
                'total' => $post['testObt'],
                'testTotal' => $post['testTotal'],
                'status' => $post['testResult'],
                'updatedBy' => $data['id'],
                'updatedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oGatResultModel->upsert($params, $post['gatId']);
            if ($out) {
                $data['updateMsg'] = 'Record updated successfully.';
            } else {
                $data['errorMsg'] = 'Record not added, please try again..';
            }
        }

        $this->render('updateGATResult', $data);
    }

}
