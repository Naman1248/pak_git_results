<?php

/**
 * Description of StateController
 *
 * @author SystemAnalyst
 */

namespace controllers\test;

class StateController extends SuperControlller {

    public function isGuestUser() {
        if (empty($this->state()->get(('depttUserInfo')))) {
            return true;
        } else {
            return false;
        }
    }

    public function moveToLoginIfGuest() {
        if ($this->isGuestUser()) {
            $this->redirect(ADMIN_URL);
        }
    }

    public function beforeAction() {
        $this->moveToLoginIfGuest();
//        die($this->getAction());
        $actions=['admissionExtensionAction','updateChallanAction'];
        if (in_array($this->getAction(),$actions)) {
            $userId = $this->state()->get('depttUserInfo')['id'];
            $oAclUserActions = new \models\cp\AclUserActions();
            $isValidate = $oAclUserActions->validate($this->getControllerName(), $this->getAction(), $userId);

            if (empty($isValidate)) {
                die('not allowed');
            }
        }
    }

}
