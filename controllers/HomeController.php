<?php

namespace controllers;

class HomeController extends SuperController {

    public function indexAction() {
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['programme'])) {
                $offerData = $oAdmmissionOfferModel->findByPK($post['programme']);
                $this->state()->pushTo('userInfo', $offerData);
            }
            $this->redirect(SITE_URL . 'dashboard');
        }
        //$this->getControllerName();
        //$this->redirect(SITE_URL.'user/login', ['error'=>'mesage here']);
        $data['activeClassCode'] = $oAdmmissionOfferModel->getCurrentOpenings();
//        print_r($data);die();
        $this->render('index', $data);
    }

    public function offeredProgramAction() {
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCodes'] = $oAdmmissionOfferModel->getOfferedProgramByMajors();
        foreach ($data ['activeClassCodes'] as $key => $value) {
            $value['studyLevelLabel'] = \helpers\Common::getStudyLevelById($value['studyLevel']);
            $data['activeClassCodes'][$key] = $value;
        }
//        var_dump($data['activeClassCodes']);exit;
        $this->render('offeredProgram', $data);
    }

    public function recoverAccountAction() {

        $this->render('recoverAccount');
    }

    public function instructionsAction() {
        $this->render('instructions');
    }

    public function errorsAction() {
        $this->render('errors');
    }

    public function alertAction() {
        $this->render('alert');
    }

    public function pdfAction() {
        $obj = new \mihaka\formats\MihakaPDF();
        $obj->setHTML("<h1>Welcome to PDF</h1>");
        $obj->browse();
    }
}
