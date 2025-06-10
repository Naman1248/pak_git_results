<?php

/**
 * Description of SiteController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp\services;

class SiteController extends \controllers\cp\SuperControlller {

    public function loadSetsAction() {
        $gender = $this->state()->get('userInfo')['gender'];
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($this->post()->cCode, 'cCode');

        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $result['sets'] = $oSubjectCombinationModel->findByClassAndGroup($offerData['cCode'], $this->post()->gCode);

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $result['bases'] = $oClassBaseMajor->getBasesByClassParentBaseMajorGender($offerData['cCode'], $this->post()->gCode, $gender);

        echo json_encode($result);
    }

    //put your code here
}
