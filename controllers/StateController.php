<?php

/**
 * Description of StateController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class StateController extends SuperController {

    public function beforeAction() {
        parent::beforeAction();
        $this->moveToLoginIfGuest();
        $oUserModel = new \models\UsersModel();
        $userImage = $oUserModel->getUserImg();
        define('USER_IMG', $userImage);
        //$this->state()->pushTo('userInfo', ['userImage' => $userImage]);
        //echo "<pre>";print_r($this->state()->all());exit;
        //echo $this->getControllerName();echo $this->getActionName();exit;
        $pagesToOmitFromOfferId = ['controllers\MyApplicationsController'];
        if (empty($this->state()->get('userInfo')['offerId'])) {
            if ($this->getControllerName() == 'controllers\DashboardController' && $this->getActionName() == 'applyExtensionAction') {
                return true;
            } 
            if ($this->getControllerName() == 'controllers\DashboardController' && $this->getActionName() == 'academicsAction') {
                return true;
            } 
            if ($this->getControllerName() == 'controllers\DashboardController' && $this->getActionName() == 'profileAction') {
                return true;
            } 
            if ($this->getControllerName() == 'controllers\DashboardController' && $this->getActionName() == 'loadEducationAction') {
                return true;
            } 
            if ($this->getControllerName() == 'controllers\DashboardController' && $this->getActionName() == 'overseasDetailAction') {
                return true;
            } 
            if (!in_array(CALLED_ACTION, $pagesToOmitFromOfferId)) {
                $this->redirect(SITE_URL . '/home/index');
            }
        }
    }

}
