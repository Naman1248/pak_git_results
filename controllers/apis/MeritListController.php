<?php

/**
 * Description of MeritListController
 *
 * @author SystemAnalyst
 */

namespace controllers\apis;

class MeritListController extends SuperController {

    public function dataAction() {
//        echo md5('ditoffice');exit;
        $post = $this->post()->all();
        if (empty($post['year'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Admission Year is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($post['program'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Class is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($post['majorId'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Major is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($post['baseId'])) {
            header("HTTPS/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Admission Base is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($post['meritList'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Merit List is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($post['adm_cycle'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Admission Cycle is missing';
            $this->printJsonResponse(FALSE, $response);
            return false;
        }
        
        $LMSClass = $post['program'];
        $oMajorsModel = new \models\MajorsModel();
        $mapClassCode = $oMajorsModel->getClassByYearLMSClassIdAndMajorId($post['year'], $LMSClass, $post['majorId']);
        $post['program'] = $mapClassCode['cCode'];
        
        $oAdmissionOffer = new \models\AdmissionOfferModel();
//        $offerData = $oAdmissionOffer->getOfferIdByYearAndClass($post['year'], $post['program']);
        $offerData = $oAdmissionOffer->getOfferIdByYearAndClassAndAttemptNo($post['year'], $post['program'], $post['adm_cycle']);
        
        if (empty($offerData)) {
            header("HTTP/1.O 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Invalid Admission Year or Program.';
            $this->printJsonResponse(FALSE, $response);
            return false;
        }
        
        $oUGTResultModel = new \models\UGTResultModel();
        $meritList = $oUGTResultModel->isMeritListExist($offerData['offerId'], $post['majorId'], $post['baseId'], $post['meritList']);

        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $meritListPublish = $oMeritListInfoModel->isMeritListsLockedAndPublished($offerData['offerId'], $post['majorId'], $post['baseId'], $post['meritList']);

        if (empty($meritList)) {
            header("HTTP/1.O 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Merit List Does Not Exist.';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } elseif (empty($meritListPublish)) {
            header("HTTP/1.O 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Merit List is not Published.';
            $this->printJsonResponse(FALSE, $response);
            return false;
        } else {
            $meritListdata = $oUGTResultModel->meritListDetailApi($offerData['offerId'], $post['majorId'], $post['baseId'], $post['meritList'], $post['year'], $LMSClass);
            if (!empty($meritListdata)) {
                $response['status'] = TRUE;
                $response['message'] = 'Success';
                $response['data'] = $meritListdata;
                $out = json_encode($response);
                echo str_replace('\n', ' ', $out);
                return true;
            } else {
                header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
                $response['status'] = FALSE;
                $response['message'] = 'Some Internal Error Occured, Please Try Again with Patience.';
                $this->printJsonResponse(FALSE, $response);
                return false;
            }
        }
    }

}
