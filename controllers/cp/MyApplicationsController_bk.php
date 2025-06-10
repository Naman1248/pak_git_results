<?php

/**
 * Description of MyApplicationController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class MyApplicationsController extends StateController {

    public function challanFormAction() {
//        die('die');
        $get = $this->get()->all();
        $oMyApplications = new \useCases\MyApplications();
        $oMyApplications->challanForm($get);
    }

    public function printFormAction() {
        $get = $this->get()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $data = $oApplicationsModel->findByPK($get['appId'], 'userId');
        $data['appId'] = $get['appId'];
        $oMyApplications = new \useCases\MyApplications();
        $response = $oMyApplications->printForm($data);
        $HTML = $this->getHTML($response['formName'], $response['data']);
        $undertakingsHTML = $specialFormsHTML = '';
        if ($response['undertakings'] != '') {
            $undertakingsHTML = $this->getHTML($response['undertakings'], $response['data']);
        }
        if ($response['specialForms'] != '') {
            foreach ($specialForms as $specialForm) {
                $specialFormsHTML = $this->getHTML($response['specialForms'], $response['data']);
            }
            $oMyApplications->makePrintFormPDF($response['data'], $HTML, $undertakingsHTML, $specialFormsHTML);
        }
    }

}
