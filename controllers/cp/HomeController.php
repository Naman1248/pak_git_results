<?php

/**
 * Description of HomeController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class HomeController extends SuperControlller {

    public function indexAction() {
        $this->render('login');
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
