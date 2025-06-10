<?php

namespace controllers;

class SuperController extends \mihaka\MihakaController {

    protected $userImage;

    public function isGuestUser() {
        if (empty($this->state()->get(('userInfo')))) {
            return true;
        } else {
            return false;
        }
    }

    public function moveToLoginIfGuest() {
        if ($this->isGuestUser()) {
            $this->redirect(SITE_URL . 'user/login');
        }
    }

    public function moveToDashbardIfUser() {
        if (!$this->isGuestUser()) {
            $this->redirect(SITE_URL . 'dashboard');
        }
    }

    public function beforeAction() {
        define('CALLED_ACTION', $this->getControllerName());
    }

    public function beforeRender() {
        
    }

    public function afterRender() {
        
    }

    public function afterAction() {
        
    }

    public function errorAction($actionName, $error) {
        //echo "Requested Action not found" . $actionName;
        $this->redirect(SITE_URL . 'home/errors?' . $error['code'] . '=' . $actionName);
    }

}
