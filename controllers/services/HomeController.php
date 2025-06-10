<?php

/**
 * Description of HomeController
 *
 * @author SystemAnalyst
 */

namespace controllers\services;

class HomeController extends SuperController {

    public function indexAction() {
        echo "in services";
    }

    public function updateProfileAction() {
        $post = $this->post()->all();
        $oUsersModel = new \models\UsersModel();
        $out = $oUsersModel->updateProfile($post);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
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
}
