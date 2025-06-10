<?php

/**
 * Description of UGTResultController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp\services;

class UGTResultController extends \controllers\cp\SuperControlller {

    public function shiftApplicantsMajorAndBaseWiseAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oGatSlipModel = new \models\GatSlipModel();
        $out = $oGatSlipModel->assignMajorWiseRollNoMulti($post['offerId'], $post['slotId'], $post['slotNo'], $post['majorId'], $post['cityId'], $post['strength'], $id);
        $this->printAndDieJsonResponse(true, ['msg' => $out]);
    }

}
