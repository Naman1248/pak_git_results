<?php

/**
 * Description of HomeController
 *
 * @author SystemAnalyst
 */

namespace controllers\test;

class HomeController extends SuperControlller {

    public function indexAction() {
        $this->render('index');
    }
    public function resetPasswordAction() {
        $this->render('resetPassword');
    }

    public function logoutAction() {
        $oUserModel = new \models\cp\DepttUserModel();
        $oUserModel->logout();
        exit();
    }

}
