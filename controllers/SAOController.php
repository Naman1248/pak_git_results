<?php

/**
 * Description of SAOController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class SAOController extends SuperController {

    public function __construct() {
        $userData = $this->state()->get('userInfo');
        if ($userData['userId'] != 1) {
            die(':)');
        }
    }

    public function indexAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassId($data['offerId'], $offerData['cCode']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['majorId'] = $post['majorId'];
            }
            $data['baseId'] = $post['admissionBase'];

            if (!empty($data['offerId'])) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByFilter($data['offerId'], $data['baseId'], $data['majorId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getOpeningsOfTheYear($post['admissionYear']);
//print_r($data);exit;
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
//        print_r($data); exit;
        $this->render('index', $data);
    }

}
