<?php

/**
 * Description of UGTPlanController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class UGTPlanController extends \controllers\cp\StateController {

    public function venuesListAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        $oUGTPlanModel = new \models\UGTPlanModel();
        $data['rooms'] = $oUGTPlanModel->allVenues();

        $this->render('venuesList', $data);
    }

    public function attendanceSheetPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['venue'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK(151, 'cCode,className');
            $UGTPlanModel = new \models\UGTPlanModel();
            $data['applications'] = $UGTPlanModel->applicantsByVenue($get['venue']);
            $data['venueMajors'] = $UGTPlanModel->majorsByVenue($get['venue']);
            $data['total'] = sizeof($data['applications']);
            $data['venue'] = $get['venue'];
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('attendanceSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }
    public function seatingPlanPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['venue'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK(151, 'cCode,className');
            $UGTPlanModel = new \models\UGTPlanModel();
            $data['applications'] = $UGTPlanModel->applicantsByVenue($get['venue']);
            $data['venueMajors'] = $UGTPlanModel->majorsByVenue($get['venue']);
            $data['total'] = sizeof($data['applications']);
            $data['venue'] = $get['venue'];
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('seatingPlanPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function addUGTPlanAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $post = $this->post()->all();

        if ($this->isPost()) {
            $oMajorsModel = new \models\MajorsModel();
            $post['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($post['offerId'], $post['majorId']);

            $oAdmissionOfferModel = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOfferModel->findByPK($post['offerId']);

            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $post['admissionBase']);

            $oUsersModel = new \models\UsersModel();
            $userData = $oUsersModel->findByPK($post['userid']);
            $oUGTPlanModel = new \models\UGTPlanModel();
            $params = [
                'name' => $post['name'],
                'fatherName' => $post['fname'],
                'cnic' => $post['cnic'],
                'userId' => $post['userid'],
                'majId' => $post['majorId'],
                'baseId' => $post['admissionBase'],
                'baseName' => $data['baseName']['name'],
                'offerId' => $post['offerId'],
                'date' => $post['testDate'],
                'time' => $post['testTime'],
                'venue' => $post['testVenue'],
                'rollNo' => $post['rno'],
                'major' => $post['majorName'],
                'Class' => $offerData['className'],
                'Gender' => $userData['gender'],
                'DOB' => $userData['dob'],
                'contactNo' => $userData['ph1'],
                'Email' => $userData['email'],
                'add1' => $userData['add1'],
                'cCode' => $offerData['cCode']
            ];
            $oApplicationsModel = new \models\ApplicationsModel();
            $result = $oApplicationsModel->byUserIdAndOfferIdAndMajorIdAndBaseId($post['userid'], $post['offerId'], $post['majorId'], $post['admissionBase']);
            if ($result) {
                $params['appId'] = $result['appId'];
                $params['formNo'] = $result['formNo'];
                $params['Paid'] = $result['isPaid'];
                $out = $oUGTPlanModel->insert($params);
//                var_dump($out);exit;
                if ($out) {
                    $data['addMsg'] = 'Record added successfully.';
                } else {
                    $data['errorMsg'] = 'Record not added, please try again..';
                }
            } else {
                $data['errorMsg'] = 'Application does not exist for this user.';
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getOpeningsOfTheYear($post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('addUGTPlan', $data);
    }

}
