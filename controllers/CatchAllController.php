<?php

namespace controllers;

class CatchAllController extends SuperController {

    public function __construct() {
        
    }

    public function indexAction($controller, $action) {
        //echo "From catch all controller: $controller,$action not found";
        $this->redirect(SITE_URL . '/home/errors?cnf=' . $controller);
    }

}
