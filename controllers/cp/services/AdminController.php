<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace controllers\cp\services;

/**
 * Description of AdminController
 *
 * @author sys-111
 */
class AdminController extends \controllers\cp\SuperControlller {

    public function getFormInfoAction() {
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $formData = $oApplicationsModel->getApplicationInfoByFormNo($post['formNo']);
        if (!empty($formData['offerId']) && !empty($formData['majId'])) {
            $oMajorsModel = new \models\MajorsModel();
        }
        $majorName = $oMajorsModel->getMajorNameByOfferIdAndMajorId($formData['offerId'], $formData['majId']);
        $formData['majorName'] = $majorName;
        $oBaseClassModel = new \models\BaseClassModel();
        $baseName = $oBaseClassModel->getBaseByClassIdAndBaseId($formData['cCode'], $formData['baseId']);
        $formData['baseName'] = $baseName['name'];
//        print_r($baseName['name']);exit;

        $oUsersModel = new \models\UsersModel();
        $userName = $oUsersModel->findOneByField('userId', $formData['userId'], 'name');
        $formData['userName'] = $userName['name'];

        if (!empty($formData['rn'])) {
            $oGatSlipModel = new \models\GatSlipModel();
            $slipData = $oGatSlipModel->findOneByField('formNo', $post['formNo'], 'rollNo, venue, date, time');
            $formData['rollNo'] = $slipData['rollNo'];
            $formData['date'] = $slipData['date'];
            $formData['time'] = $slipData['time'];
            $formData['venue'] = $slipData['venue'];
        }

        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $formData]);
    }

    public function getFormInfoForUpdateBaseAction() {
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $formData = $oApplicationsModel->getApplicationInfoByFormNoForUpdateBase($post['formNo']);
        if (!empty($formData)) {
            $oMajorsModel = new \models\MajorsModel();
            $majorName = $oMajorsModel->getMajorNameByOfferIdAndMajorId($formData['offerId'], $formData['majId']);
            $formData['majorName'] = $majorName;
            $oBaseClassModel = new \models\BaseClassModel();
            $baseName = $oBaseClassModel->getBaseByClassIdAndBaseId($formData['cCode'], $formData['baseId']);
            $formData['baseName'] = $baseName['name'];
//        print_r($baseName['name']);exit;

            $oUsersModel = new \models\UsersModel();
            $userName = $oUsersModel->findOneByField('userId', $formData['userId'], 'name');
            $formData['userName'] = $userName['name'];
        }
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $formData]);
    }

    public function getUserInfoAction() {
        $post = $this->post()->all();
        $oUsersModel = new \models\UsersModel();
        if (!empty($post['cnic'])) {
            $userData = $oUsersModel->findOneByField('cnic', $post['cnic'], 'userId, name, fatherName, cnic, email, ph1, gender, dob, md5(paswrd) pswrd');
        }
        if (!empty($post['userId'])) {
            $userData = $oUsersModel->findByPK($post['userId'], 'userId, name, fatherName, cnic, email, ph1, gender, dob, md5(paswrd) pswrd');
        }
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $userData]);
    }

    public function updateApplicationAction() {
        $post = $this->post()->all();
        $oApplicationModel = new \models\ApplicationsModel();
        $out = $oApplicationModel->updateApplication($post);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
    }

    public function saveClassTestMarksAction() {
        $post = $this->post()->all();
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $out = $oAdmissionOfferModel->upsert(
                [
                    'compTotal' => $post['compTotal'],
                    'subTotal' => $post['subTotal'],
                    'testTotal' => $post['testTotal'],
                    'testPassPer' => $post['testPassPer'],
                    'testCity' => $post['testCity'],
                ], $post['offerId']
        );
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Record Update Successfully', 'data' => $out]);
        }
    }

    public function updateBaseAction() {
        $post = $this->post()->all();
        $oApplicationModel = new \models\ApplicationsModel();
        $out = $oApplicationModel->updateBase($post);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
    }

    public function assignRollNoByformNoAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        if (empty($post['testCity']) || empty($post['fno']) || empty($post['newRn'])) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please Enter All Informations.']);
        }
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignRollNoByFormNo($post['testCity'], $post['fno'], $post['newRn'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

    public function updateProfileAction() {
        $post = $this->post()->all();
        $oUsersModel = new \models\UsersModel();
        $out = $oUsersModel->updateProfile($post);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
    }
}
