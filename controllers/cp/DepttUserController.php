<?php

/**
 * Description of DepttUserController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class DepttUserController extends \controllers\cp\StateController {

    public function resetAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        $this->render('resetPasswordDeptt');
    }
}
