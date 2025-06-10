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

}
