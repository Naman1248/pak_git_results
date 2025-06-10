<?php

/**
 * Description of MeritListController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp\services;

class MeritListController extends \controllers\cp\SuperControlller {

    public function addExpiryAction() {
        $post = $this->post()->all();
        $userId = $this->state()->get('depttUserInfo')['id'];
        $oMeritListInfoModel = new \models\MeritListInfoModel();
        if (!empty($post['expiry']) && !empty($post['totApplicants'])) {
            $oMeritListInfoModel->addExpiry($post, $userId);
        }
    }

    public function publishMeritListAction() {
        $post = $this->post()->all();
        $userId = $this->state()->get('depttUserInfo')['id'];
        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $dueDate = $oMeritListInfoModel->findByPK($post['id'], 'dueDate');
        if (!empty($dueDate['dueDate'])) {
            $oMeritListInfoModel->publishMeritList($post, $userId);
        }
    }

    public function deleteMeritListAction() {
        $id = $this->post()->id;
        $oUGTResultModel = new \models\UGTResultModel();
        $oUGTResultModel->deleteMeritList($id);
    }

    public function lockMeritListAction() {
        $id = $this->post()->id;
        $oUGTResultModel = new \models\UGTResultModel();
        $oUGTResultModel->lockMeritList($id);
    }

    public function unLockMeritListAction() {
        $id = $this->post()->id;
        $oUGTResultModel = new \models\UGTResultModel();
        $oUGTResultModel->unLockMeritList($id);
    }

    public function aggregateDetailMSPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->aggregateInfo($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
            $HTML = $this->getHTML('aggregateDetailMSPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MS-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

}
