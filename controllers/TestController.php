<?php

/**
 * Description of ChallanController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class TestController extends SuperController {

    public function indexAction() {
        $this->render('login');
    }
    public function dashboardAction() {
        $this->render('dashboard');
    }
    public function collegeProfileAction() {
        $this->render('instAffInfo');
    }

}
