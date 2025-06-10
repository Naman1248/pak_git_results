<?php

/**
 * Description of HomeController
 *
 * @author SystemAnalyst
 */

namespace controllers\affiliation;

class HomeController extends SuperControlller {

    public function indexAction() {
        die('here');
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
