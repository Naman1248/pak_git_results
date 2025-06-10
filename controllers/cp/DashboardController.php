<?php

/*
 * Description of DashboardController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class DashboardController extends StateController {

    public function indexAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['role'] = $this->state()->get('depttUserInfo')['role'];
//        echo "<pre>";
//        var_dump($data);exit;
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['classes'] = $oApplicationsModel->ClassStatbyDepttId($data['dId']);
        $data['classesUnpaid'] = $oApplicationsModel->ClassStatbyDepttIdUnPaid($data['dId']);
        $this->render('index', $data);
    }

    public function SendSmsAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['id'];
        if ($data['dId'] != 1) {
            die('Not Authorized');
        } else {
            if ($this->isPost()) {
                $post = $this->post()->all();
                if (strlen($post['textsms']) < 501 && !empty($post['phones']) && !empty($post['textsms'])) {
                    $oSmsQueueModel = new \models\cp\SmsQueueModel();
                    $oSmsQueueModel->insertPhones($post['phones'], $post['textsms']);
                }
            }
        }
        $this->render('sendSms', $data);
    }

    public function SendSpecialSmsAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['id'];
        if ($data['dId'] != 1) {
            die('Not Authorized');
        }
        $oUgtResultModel = new \models\UGTResultModel();
        $data = $oUgtResultModel->smsTrialByBaseId(11);
        foreach ($data as $row) {
            $msg = 'Dear Candidate, Your Overseas Admission Base Interview is on ' . $row['trialDate'] . ' - ' . $row['trialTime'] . ' at ' . $row['trialVenue'] . ' Download your schedule from online admission portal.';
            $oSmsQueueModel = new \models\cp\SmsQueueModel();
            $row['contactNo'] = '0' . ltrim($row['contactNo'], 0);
            $oSmsQueueModel->insertPhones($row['contactNo'], $msg);
        }
    }

    public function baseWiseInteriewMarksAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['childBase'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);exit;
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode,className,year');
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
//                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
                $data['childBases'] = $oBaseClass->getBasesByOfferIdAndClassIdAndParentBase($post['offerId'], $data['offerData']['cCode'], $post['admissionBase']);
                $data['childBaseName'] = $oBaseClass->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBaseChild'], $post['admissionBase']);
                $data['baseId'] = $post['admissionBase'];
                $data['childBase'] = $post['admissionBaseChild'];
                $oUserMarksModel = new \models\UserMarksModel();
                $data['marks'] = $oUserMarksModel->allByOfferIdAndBaseAndChildbaseMarks($data['offerId'], $data['baseId'], $data['childBase']);
//               
                $data['total'] = sizeof($data['marks']);
            }
            $data['baseId'] = $post['admissionBase'];
            $data['admissionYear'] = $post['admissionYear'];
        }
//        else {
//            $post['admissionYear'] = date('Y');
//        }

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('baseWiseInterviewMarks', $data);
    }

    public function meritListEntryReservedAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['majorId'] = $post['majorId'];
                $data['meritListNo'] = $post['meritListNo'];
                $data['totApp'] = $post['totApp'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
            }
            $data['baseId'] = $post['admissionBase'];

            if (!empty($data['offerId'])) {
                $oUGTResultModel = new \models\UGTResultModel();
                $data['applications'] = $oUGTResultModel->meritListByOfferIdAndByMajorIdAndBaseId($post['offerId'], $post['majorId'], $data['baseId']);
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('meritListEntry', $data);
    }

    public function meritListEntrySpecialAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['majorId'] = $post['majorId'];
                $data['meritListNo'] = $post['meritListNo'];
                $data['totApp'] = $post['totApp'];
                $data['baseId'] = $post['admissionBase'];
                $data['meritListCtgry'] = $post['meritListCtgry'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
                $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
                $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($data['offerData']['cCode'], $post['majorId'], $data['dId']);
                $oBaseClassModel = new \models\BaseClassModel();
//                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            }
            if (!empty($data['offerId'])) {
                $oUGTResultModel = new \models\UGTResultModel();
                $isMeritListExit = $oUGTResultModel->isMeritListExist($post['offerId'], $post['majorId'], $data['baseId'], $data['meritListNo']);
                if (!empty($isMeritListExit)) {
                    $data['errMsg'] = 'This Merit List Already Exist.';
                }
//                elseif (empty($isMeritListCtgry)) {
//                    $data['errMsg'] = 'Please Select Merit List Catergory.';
//                } 
                else {
                    $data['applications'] = $oUGTResultModel->meritListByOfferIdAndByMajorIdAndBaseId($post['offerId'], $post['majorId'], $data['baseId'], $data['totApp']);
                }
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('meritListEntrySpecial', $data);
    }

    public function meritListEntryAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['majorId'] = $post['majorId'];
                $data['meritListNo'] = $post['meritListNo'];
                $data['totApp'] = $post['totApp'];
                $data['baseId'] = $post['admissionBase'];
                $data['meritListCtgry'] = $post['meritListCtgry'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'year, cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
                $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
                $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($data['offerData']['cCode'], $post['majorId'], $data['dId']);
                $oBaseClassModel = new \models\BaseClassModel();
//                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            }
            if (!empty($data['offerId'])) {
                $oUGTResultModel = new \models\UGTResultModel();
//                $isMeritListExit = $oUGTResultModel->isMeritListExist($post['offerId'], $post['majorId'], $data['baseId'], $data['meritListNo']);
                $isMeritListExit = $oUGTResultModel->isMeritListExistByYear($data['offerData']['year'], $data['offerData']['cCode'], $post['majorId'], $data['baseId'], $data['meritListNo']);
                if ($data['offerId'] == 119 && $data['meritListCtgry'] == 'Morning') {
                    $data['errMsg'] = 'Mphil Morning is not avaiable.';
                } else if (!empty($isMeritListExit)) {
                    $data['errMsg'] = 'This Merit List Already Exist.';
                } else {
                    $data['applications'] = $oUGTResultModel->meritListByOfferIdAndByMajorIdAndBaseId($post['offerId'], $post['majorId'], $data['baseId'], $data['totApp']);
                }
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('meritListEntry', $data);
    }

    public function meritListInfoAction() {
        $get = $this->get()->all();
        $post = $this->post()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['majorId'] = $get['majorId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');

            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);

            $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
            $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($data['offerData']['cCode'], $data['majorId'], $data['dId']);

            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);

            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->meritListInfoByOfferIdAndByMajorIdAndBaseId($data['offerId'], $data['majorId'], $data['baseId'], $data['totApp']);
//                print_r($data['applications']);exit;
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = $post['admissionYear'];
//            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        if (!empty($data['dId']) && !empty($post['admissionYear'])) {
            $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        }
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('meritListInfo', $data);
    }

    public function meritListInfoSpecialAction() {
        $get = $this->get()->all();
        $data['offerId'] = $get['offerId'] ?? '';
        $data['majorId'] = $get['majorId'] ?? '';
        $data['baseId'] = $get['baseId'] ?? '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');

            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);

            $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
            $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($data['offerData']['cCode'], $data['majorId'], $data['dId']);

            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);

            $oMeritListInfoModel = new \models\MeritListInfoModel();
            $data['applications'] = $oMeritListInfoModel->meritListInfoByOfferIdAndByMajorIdAndBaseId($data['offerId'], $data['majorId'], $data['baseId'], $data['totApp']);
//                print_r($data['applications']);exit;
            $data['total'] = sizeof($data['applications']);
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('meritListInfoSpecial', $data);
    }

    public function testMarksPDFAction() {
        $post = $this->get()->all();
        if (!empty($post['offerId'])) {
            $data['offerId'] = $post['offerId'];
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode,className,year');
            $oBaseClassModel = new \models\BaseClassModel();
            $data['childBase'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['childBase'], $post['baseId']);

            $oUserMarksModel = new \models\UserMarksModel();
            $data['applications'] = $oUserMarksModel->allByOfferIdAndBaseAndChildbaseMarks($post['offerId'], $post['baseId'], $post['childBase']);
//            print_r($post);
//            echo "<pre>";
//            print_r($data['applications']);
//            exit;
            $data['total'] = sizeof($data['applications']);

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('testMarksPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Test Marks Report</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
//            echo $HTML; exit;
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function baseWiseInterviewMarksPDFAction() {
        $post = $this->get()->all();
        if (!empty($post['offerId'])) {
            $data['offerId'] = $post['offerId'];
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode,className,year');
            $oBaseClassModel = new \models\BaseClassModel();
            $data['childBase'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['childBase'], $post['baseId']);

            $oUserMarksModel = new \models\UserMarksModel();
            $data['applications'] = $oUserMarksModel->allByOfferIdAndBaseAndChildbaseMarks($post['offerId'], $post['baseId'], $post['childBase']);
//            print_r($post);
//            echo "<pre>";
//            print_r($data['applications']);
//            exit;
            $data['total'] = sizeof($data['applications']);

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('baseWiseInterviewMarksPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Interview Marks Report</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
//            echo $HTML; exit;
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function majorWiseResultPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, e.marksTot';
            $data['applications'] = $oUGTResultModel->interviewInfoByOfferIdAndByMajorId($offerIds, $get['mid'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('majorWiseUGTResultPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function majorAndBaseWiseResultPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, e.marksTot, e.marksObt';
            $data['applications'] = $oUGTResultModel->interviewInfoByOfferIdAndByMajorIdAndByBaseId($offerIds, $get['mid'], $get['bid'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);

            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $get['bid']);

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('majorAndBaseWiseUGTResultPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, a.interviewTot, a.interviewObt, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, e.marksTot';
            $data['applications'] = $oUGTResultModel->interviewInfoByOfferIdAndByMajorId($offerIds, $get['mid'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('interviewMarksPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksBaseWisePDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, a.interviewTot, a.interviewObt, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, e.marksObt';
            $data['applications'] = $oUGTResultModel->interviewInfoByOfferIdAndByMajorIdAndBaseId($offerIds, $get['mid'], $get['bid'], $fields);
//            $data['applications'] = $oUGTResultModel->interviewInfoByOfferIdAndByMajorIdAndBaseId($get['oid'], $get['mid'], $get['bid'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);

            $oBaseModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseModel->getBaseByClassIdAndBaseId($offerData['cCode'], $get['bid']);

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('interviewMarksInterPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
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

    public function interviewMarksMSPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfo($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
            $HTML = $this->getHTML('interviewMarksMSPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MS-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksMSPDFDescAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfoDesc($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
            $HTML = $this->getHTML('interviewMarksMSPDFDesc', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> MS-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksUGTPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfo($get['oid'], $get['mid'], $get['bid']);
//            $data['applications'] = $oUGTResultModel->marksInfoUGT($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('aggregateDetailUGTPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Aggregate</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksInterPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfoInter($get['oid'], $get['mid'], $get['bid']);
//            $data['applications'] = $oUGTResultModel->marksInfoUGT($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('aggregateDetailInterPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Aggregate</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksUGTPDFDescAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfoDesc($get['oid'], $get['mid'], $get['bid']);
//            $data['applications'] = $oUGTResultModel->marksInfoUGT($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('aggregateDetailUGTPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Aggregate</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewMarksInterPDFDescAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->marksInfoDesc($get['oid'], $get['mid'], $get['bid']);
//            $data['applications'] = $oUGTResultModel->marksInfoUGT($get['oid'], $get['mid'], $get['bid']);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $data['offerData']['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('aggregateDetailInterPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Aggregate</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function interviewAttendancePDFAction() {
        $get = $this->get()->all();
//        print_r($get);
//        exit;
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, e.marksTot';
            $data['applications'] = $oUGTResultModel->findByOfferIdAndByMajorIdByInterviewDate($get['oid'], $get['mid'], $get['idate'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($post);
//            echo "<pre>";
//            print_r($data['applications']);
//            exit;

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('interviewAttendanceSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> UGT-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
//            $obj->getPDFObject()->SetMargins(0, 0, 0);
//            $obj->getPDFObject()->SetRightMargin(2);
//            $obj->getPDFObject()->SetLeftMargin(0);
//echo $HTML; exit;
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function testMarksAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['childBase'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
//                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
                $data['childBases'] = $oBaseClass->getBasesByOfferIdAndClassIdAndParentBase($post['offerId'], $offerData['cCode'], $post['admissionBase']);
                $data['baseId'] = $post['admissionBase'];
                $data['childBase'] = $post['admissionBaseChild'];
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByOfferIdAndBaseAndChildbase($data['offerId'], $data['baseId'], $data['childBase'], 'Y');
                $oUserMarksModel = new \models\UserMarksModel();
                $data['marks'] = $oUserMarksModel->allByOfferIdAndBaseAndChildbase($data['offerId'], $data['baseId'], $data['childBase']);
                $data['total'] = sizeof($data['applications']);
            }
            $data['baseId'] = $post['admissionBase'];
            $data['admissionYear'] = $post['admissionYear'];
        }
//        else {
//            $post['admissionYear'] = date('Y');
//        }

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('testMarks', $data);
    }

    public function admissionExtensionAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (empty($post['reqBy']) || empty($post['email']) || empty($post['admissionYear']) || empty($post['className']) || empty($post['endDate'])) {
                $data['message'] = 'Please Enter Complete Information.';
            } else {
                $oUserModel = new \models\UsersModel();
                $out = $oUserModel->validUserByEmail($post['email']);
//                $out = $oUserModel->validUserByContactNo($post['email']);

                if (!empty($out)) {
                    $oUserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
                    $post['userId'] = $out['userId'];
                    $post['name'] = $out['name'];
                    $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                    $classData = $oAdmissionOfferModel->findByPK($post['className']);
                    $post['classLabel'] = $classData['className'];
                    $post['liuid'] = $this->state()->get('depttUserInfo')['id'];
                    $alreadyExists = $oUserAdmissionOfferModel->exist($post['userId'], $post['className']);
                    $oApplicationsModel = new \models\ApplicationsModel();
                    $isApplicationExist = $oApplicationsModel->isApplicationExistByOfferId($post['className'], $post['userId']);
//                    print_r($isApplicationExist);
                    if (!empty($isApplicationExist)) {
                        
                    }
                    if (!empty($alreadyExists)) {
                        $post['contactNo'] = $out['ph1'];
                        $isSaved = $oUserAdmissionOfferModel->add($post, $alreadyExists['id']);
                    } else {
                        $post['contactNo'] = $out['ph1'];
                        $isSaved = $oUserAdmissionOfferModel->add($post);
                    }
                    if ($isSaved) {
                        $data['message'] = 'Record added successfully...';
                        $out = $oApplicationsModel->updateApplicationsEndDateByOfferIdAndUserId($post['className'], $post['userId'], $post['endDate']);
//                        $oApplicationsModel->upsert(['endDate' => $post['endDate'] . ' 19:00:00'], $isApplicationExist['appId']);
                    } else {
                        $data['message'] = 'Record not added. Please try again..';
                    }
                } else {
                    $data['message'] = 'Invalid User Name...';
                }
            }
            $post['yearAdmission'] = $post['admissionYear'];
        } else {
            $post['yearAdmission'] = date('Y');
        }
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['yearAdmission']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('admissionExtension', $data);
    }

    public function admExtensionAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (empty($post['reqBy']) || empty($post['email']) || empty($post['admissionYear']) || empty($post['offerId']) || empty($post['majorId']) || empty($post['majorId']) || empty($post['setNumber']) || empty($post['admissionBase']) || empty($post['endDate'])) {
                $data['message'] = 'Please Enter Complete Information.';
            } else {
                $oUserModel = new \models\UsersModel();
                $out = $oUserModel->validUserByEmail($post['email']);

                if (!empty($out)) {
                    $oUserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
                    $post['userId'] = $out['userId'];
                    $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                    $classData = $oAdmissionOfferModel->findByPK($post['offerId']);
                    $post['classLabel'] = $classData['className'];
                    $post['liuid'] = $this->state()->get('depttUserInfo')['id'];
//                        print_r($post['liuid']);exit;
                    $isSaved = $oUserAdmissionOfferModel->add($post);
                    if ($isSaved) {
                        $oApplicationsModel = new \models\ApplicationsModel();
                        $oApplicationsModel->apply($post, $userId);

                        $data['message'] = 'Record added successfully...';
                    } else {
                        $data['message'] = 'Record not added. Please try again..';
                    }
                } else {
                    $data['message'] = 'Invalid User Name...';
                }
            }
            $post['yearAdmission'] = $post['admissionYear'];
        } else {
            $post['yearAdmission'] = date('Y');
        }
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['yearAdmission']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('admExtension', $data);
    }

    public function updateChallanAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $this->render('updateChallan', $data);
    }

    public function testCentreWiseStatAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
//            print_r($post);

            if (!empty($post['offerId'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->TestCentrebyOfferId($post['offerId'], $data['dId']);
//                print_r($data['paid']);exit;
//                $data['unpaid'] = $oApplicationsModel->BaseStatbyOfferIdNotPaid($post['offerId'], $data['dId']);
                $data['totalPaidMale'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
                $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
//                $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
//                $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');
//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
//                print_r($data);              
//////                print_r(array_sum($data['paid']['Male']));
//                exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('testCentreWiseStat', $data);
    }

    public function baseWiseStatAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['role'] = $this->state()->get('depttUserInfo')['role'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
//            print_r($post);

            if (!empty($post['offerId'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->BaseStatbyOfferIdPaid($post['offerId'], $data['dId']);
                $data['unpaid'] = $oApplicationsModel->BaseStatbyOfferIdNotPaid($post['offerId'], $data['dId']);
                $data['totalPaidMale'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
                $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
                $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
                $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');

//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
//                print_r($data);              
//////                print_r(array_sum($data['paid']['Male']));
//                exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        if ($data['role'] == 'vc_admin') {

            $data['yearList'] = \helpers\Common::yearListVC();
        }

        $this->render('baseWiseStat', $data);
    }

    public function classWiseStatAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
//            print_r($post);exit;

            if (!empty($post['offerId'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->ClassStatbyOfferIdPaid($post['offerId'], $data['dId']);
                $data['unpaid'] = $oApplicationsModel->ClassStatbyOfferIdNotPaid($post['offerId'], $data['dId']);
                $data['totalPaidMale'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
                $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
                $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
                $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');

//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
//                print_r($data);              
//////                print_r(array_sum($data['paid']['Male']));
//                exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();

        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearListVC();

        $this->render('classWiseStat', $data);
    }

    public function classWiseAllStatAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
//            print_r($post);exit;

            if (!empty($post['offerId'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->ClassAllStatbyOfferId($post['offerId'], $data['dId']);
//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
//                print_r($data);              
//////                print_r(array_sum($data['paid']['Male']));
//                exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('classWiseAllStat', $data);
    }

    public function testCenterClassWiseStatAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
//            print_r($post);exit;

            if (!empty($post['offerId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->TestCentreByOfferIdPaid($post['offerId']);
                $data['totalKarachi'] = \helpers\Common::sumOfArray($data['paid'], 'Karachi');
                $data['totalQuetta'] = \helpers\Common::sumOfArray($data['paid'], 'Quetta');
                $data['totalLahore'] = \helpers\Common::sumOfArray($data['paid'], 'Lahore');
                $data['totalIslamabad'] = \helpers\Common::sumOfArray($data['paid'], 'Islamabad');
                $data['totalPeshawar'] = \helpers\Common::sumOfArray($data['paid'], 'Peshawar');
                $data['totalSargodha'] = \helpers\Common::sumOfArray($data['paid'], 'Sargodha');
                $data['totalMultan'] = \helpers\Common::sumOfArray($data['paid'], 'Multan');
                $data['totalFaisalabad'] = \helpers\Common::sumOfArray($data['paid'], 'Faisalabad');
//                $data['unpaid'] = $oApplicationsModel->TestCentreByOfferIdNotPaid($post['offerId']);
//                $data['totalPaidKarachi'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
//                $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
//                $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
//                $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');
//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
//                print_r($data);              
//////                print_r(array_sum($data['paid']['Male']));
//                exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('testCenterClassWiseStat', $data);
    }

    public function testCenterClassWiseStatPDFAction() {
        $post = $this->get()->all();
        $data['offerId'] = $post['offerId'];
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if (!empty($post['offerId'])) {

            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className');
            $oApplicationsModel = new \models\ApplicationsModel();
            $data['paid'] = $oApplicationsModel->TestCentreByOfferIdPaid($post['offerId']);
            $data['totalKarachi'] = \helpers\Common::sumOfArray($data['paid'], 'Karachi');
            $data['totalQuetta'] = \helpers\Common::sumOfArray($data['paid'], 'Quetta');
            $data['totalLahore'] = \helpers\Common::sumOfArray($data['paid'], 'Lahore');
            $data['totalIslamabad'] = \helpers\Common::sumOfArray($data['paid'], 'Islamabad');
            $data['totalPeshawar'] = \helpers\Common::sumOfArray($data['paid'], 'Peshawar');
            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('testCenterClassWiseStatPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Class Wise Statistics</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
//            echo $HTML; exit;
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function testCenterOverAllStatAction() {
        $data['admissionYear'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['admissionYear'] = $post['admissionYear'];
//            print_r($post);exit;

            if (!empty($post['admissionYear'])) {
//                print_r($post);
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->TestCentreAllOfferIds($post['admissionYear']);

                $data['totalKarachi'] = \helpers\Common::sumOfArray($data['paid'], 'Karachi');
                $data['totalQuetta'] = \helpers\Common::sumOfArray($data['paid'], 'Quetta');
                $data['totalLahore'] = \helpers\Common::sumOfArray($data['paid'], 'Lahore');
                $data['totalIslamabad'] = \helpers\Common::sumOfArray($data['paid'], 'Islamabad');
                $data['totalPeshawar'] = \helpers\Common::sumOfArray($data['paid'], 'Peshawar');
//                $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
//                $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');
//                echo "<pre>";
////                $tot=array_sum($data['paid']['Male']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('testCenterOverAllStat', $data);
    }

    public function yearWiseStatAction() {
        $data = [];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['startDate'] = $post['startDate'];
            $data['endDate'] = $post['endDate'];
            if (!empty($post['startDate']) || !empty($post['endDate'])) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->yearlyStat($post['startDate'], $post['endDate']);
            }
        }
        $this->render('yearWiseStat', $data);
    }

    public function classWiseStatPDFAction() {
        $post = $this->get()->all();
//            print_r($post);
//exit;
        $data['offerId'] = $post['offerId'];
        if (!empty($post['offerId'])) {
//                print_r($post);
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className,year');
            $oApplicationsModel = new \models\ApplicationsModel();
            $data['paid'] = $oApplicationsModel->ClassStatbyOfferIdPaid($post['offerId'], $data['dId']);
            $data['unpaid'] = $oApplicationsModel->ClassStatbyOfferIdNotPaid($post['offerId'], $data['dId']);
            $data['totalPaidMale'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
            $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
            $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
            $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('classWiseStatPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Class Wise Statistics</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
//            echo $HTML; exit;
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function baseWiseStatPDFAction() {
        $post = $this->get()->all();
        $data['offerId'] = $post['offerId'];
        $data['dId'] = 1;
//exit;
        if (!empty($post['offerId'])) {
//                print_r($post);
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $data['className'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'className,year');
            $oApplicationsModel = new \models\ApplicationsModel();
            $data['paid'] = $oApplicationsModel->BaseStatbyOfferIdPaid($post['offerId'], $data['dId']);
            $data['unpaid'] = $oApplicationsModel->BaseStatbyOfferIdNotPaid($post['offerId'], $data['dId']);
            $data['totalPaidMale'] = \helpers\Common::sumOfArray($data['paid'], 'Male');
            $data['totalPaidFemale'] = \helpers\Common::sumOfArray($data['paid'], 'Female');
            $data['totalUnpaidMale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Male');
            $data['totalUnpaidFemale'] = \helpers\Common::sumOfArray($data['unpaid'], 'Female');

            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('baseWiseStatPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Base Wise Statistics</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function majorWiseStatAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['role'] = $this->state()->get('depttUserInfo')['role'];

        $post = $this->post()->all();
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];

            if (!empty($post['offerId']) && !empty($post['majorId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['paid'] = $oApplicationsModel->ClassStatbyOfferIdAndMajorIdPaid($post['offerId'], $post['majorId']);
                $data['unpaid'] = $oApplicationsModel->ClassStatbyOfferIdAndMajorIdnotPaid($post['offerId'], $post['majorId']);
            }
        } else {
            $post['admissionYear'] = $post['admissionYear'];
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        if (!empty($data['dId']) && !empty($post['admissionYear'])) {
            $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        }
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        if ($data['role'] == 'vc_admin') {

            $data['yearList'] = \helpers\Common::yearListVC();
        }
        $this->render('majorWiseStat', $data);
    }

    public function majorWiseResultDownloadAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, matricTotal, matricObt, interTotal, interObt';
            $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorId($offerIds, $get['mid'], $fields);
            $data['total'] = sizeof($data['results']);
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'rollNo' => "Roll No.", 'total' => "Test Marks", 'status' => "Result",
                'interviewDate' => "Interview Date", 'interviewTime' => "Interview Time", 'interviewVenue' => "interview Venue", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address",
                'appId' => "App Id", 'offerId' => "Offer Id", 'majId' => "Major Id", 'cCode' => "Class Code", 'baseId' => "Base Id",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'matricTotal' => "Matric Total", 'matriObt' => "Matric Obtained", 'interTotal' => "Inter Total", 'interObt' => "Inter Obtained"
            ];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($offerData['className']);exit;
            \helpers\Common::downloadCSV($data['results'], $data['majorName']['name'], $headings);
        }
    }

    public function majorAndBaseWiseResultDownloadAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $offerIds = $oAdmissionOffer->getChildOfferIds($get['oid']);
            $oUGTResultModel = new \models\UGTResultModel();
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.interviewDate, a.interviewTime, a.interviewVenue, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName, matricTotal, matricObt, interTotal, interObt';
            $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdAndBaseId($offerIds, $get['mid'], $get['bid'], $fields);
            $data['total'] = sizeof($data['results']);
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'rollNo' => "Roll No.", 'total' => "Test Marks", 'status' => "Result",
                'interviewDate' => "Interview Date", 'interviewTime' => "Interview Time", 'interviewVenue' => "interview Venue", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address",
                'appId' => "App Id", 'offerId' => "Offer Id", 'majId' => "Major Id", 'cCode' => "Class Code", 'baseId' => "Base Id",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'matricTotal' => "Matric Total", 'matriObt' => "Matric Obtained", 'interTotal' => "Inter Total", 'interObt' => "Inter Obtained"
            ];
            $oMajorsModel = new \models\MajorsModel();
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($offerData['className']);exit;
            \helpers\Common::downloadCSV($data['results'], $data['majorName']['name'] . '-' . $data['baseName']['name'], $headings);
        }
    }

    public function majorWiseResultAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];

            if (!empty($post['offerId']) && !empty($post['majorId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorId($post['offerId'], $post['majorId']);
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
//                  echo '<pre>';
//                var_dump($data['majorName']);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('majorWiseResult', $data);
    }

    public function trialPlanPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.trialDate, a.trialTime, a.trialVenue, a.childBaseId, a.childBaseName,a.interviewTot, a.interviewObt, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName';
            $data['applications'] = $oUGTResultModel->findByOfferIdAndByBaseIdTrialInfo($get['oid'], $get['bid'], $fields);
//            print_r($data);exit;
            $data['total'] = sizeof($data['applications']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'L']);
            $HTML = $this->getHTML('baseWiseTrialPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Trial-List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function trialResultPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oUGTResultModel = new \models\UGTResultModel();
            $fields = 'a.userId, a.formNo, a.rollNo,a.total,a.status,a.trialDate, a.trialTime, a.trialVenue, a.trialTotal, a.trialObt,a.interviewTot, a.interviewObt, u.name applicantName,u.gender,u.fatherName, u.cnic, u.dob, u.ph1, u.email, u.add1, '
                    . 'a.appId,a.offerId,a.majId,a.cCode,a.baseId,ao.className,m.name majorName,b.name baseName';
            $data['applications'] = $oUGTResultModel->findByOfferIdAndByBaseIdTrialInfo($get['oid'], $get['bid'], $fields);
            $data['total'] = sizeof($data['applications']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $get['bid']);
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('baseWiseResultTrialPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Trial-Result</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function trialPlanAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['insertMsg'] = $oUGTResultModel->transferApplicantsByOfferIdAndBaseId($data['offerId'], $data['baseId'], 1);
                $data['results'] = $oUGTResultModel->findByOfferIdAndByBaseId($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
//                $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($offerData['cCode'], $post['majorId'], $dId);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('trialPlan', $data);
    }

    public function kinshipMarksAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->kinshipResult($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
//                $data['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($offerData['cCode'], $post['majorId'], $dId);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('kinshipMarks', $data);
    }

    public function specialInterviewPlanAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->specialInterviewPlan($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('specialInterviewPlan', $data);
    }

    public function specialInterviewMarksAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->specialInterviewMarks($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('specialInterviewMarks', $data);
    }

    public function verifyMarksSpecialAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className, studyLevel, test');
                $oBaseClassModel = new \models\BaseClassModel();
                $reservedBase = $oBaseClassModel->getReservedBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
                if ($reservedBase['reserved'] == 1) {
                    if (($data['offerData']['studyLevel'] == 3) && ($data['offerData']['test'] == 'NO')) {
                        $oUGTResultWOTestModel = new \models\withoutTest\UGTResultModel();
                        $data['results'] = $oUGTResultWOTestModel->marksInfoSpecial($post['offerId'], $post['admissionBase']);
                    } else if (($data['offerData']['studyLevel'] == 2) && ($data['offerData']['test'] == 'NO')) {
                        $oUGTResultWOTestModel = new \models\withoutTest\UGTResultModel();
                        $data['results'] = $oUGTResultWOTestModel->marksInfoSpecial($post['offerId'], $post['admissionBase']);
                    } else {
                        $oUGTResultModel = new \models\UGTResultModel();
                        $data['results'] = $oUGTResultModel->marksInfoSpecial($post['offerId'], $post['admissionBase']);
                        $data['total'] = sizeof($data['results']);
                    }
                }
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('verifyMarksSpecial', $data);
    }

    public function trialMarksAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];

            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->findByOfferIdAndByBaseIdTrialResult($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
//                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('trialMarks', $data);
    }

    public function trialResultAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];

            if (!empty($post['offerId']) && !empty($post['admissionBase'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oBaseClassModel = new \models\BaseClassModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['results'] = $oUGTResultModel->findByOfferIdAndByBaseIdTrialResult($post['offerId'], $post['admissionBase']);
                $data['total'] = sizeof($data['results']);
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
//                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $post['admissionBase']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('trialResult', $data);
    }

    public function interviewMarksMSAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['verifyCtgry'] = $post['verifyCtgry'];
            if (!empty($post['offerId']) && !empty($post['majorId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className, studyLevel, test');
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();

                if ($data['offerData']['cCode'] == 8) {
                    $data['results'] = $oUGTResultModel->marksInfoPHD($post['offerId'], $post['majorId'], $data['baseId']);
                } else if (($data['offerData']['studyLevel'] == 3) && ($data['offerData']['test'] == 'NO')) {
                    $oUGTResultWOTestModel = new \models\withoutTest\UGTResultModel();
                    $data['results'] = $oUGTResultWOTestModel->marksInfoWOTest($post['offerId'], $post['majorId'], $data['baseId'], $data['verifyCtgry']);
                } else if (($data['offerData']['studyLevel'] == 2) && ($data['offerData']['test'] == 'NO')) {
                    $oUGTResultWOTestModel = new \models\withoutTest\UGTResultModel();
                    $data['results'] = $oUGTResultWOTestModel->marksInfoWOTest($post['offerId'], $post['majorId'], $data['baseId'], $data['verifyCtgry']);
                } else {
                    $data['results'] = $oUGTResultModel->marksInfo($post['offerId'], $post['majorId'], $data['baseId'], $data['verifyCtgry']);
                }
//                $data['results'] = $oUGTResultModel->marksInfoNomination($post['offerId'], $post['majorId'], $data['baseId']);
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $oBaseClassModel = new \models\BaseClassModel();
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
//        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewMarksMS', $data);
    }

    public function verifyMarksNominationAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            if (!empty($post['offerId']) && !empty($post['majorId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className, studyLevel');
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                $data['insertion'] = $oUGTResultModel->transferApplicantsByOfferIdAndBaseId($post['offerId'], $post['admissionBase'], 1);
                $data['results'] = $oUGTResultModel->marksInfoNomination($post['offerId'], $post['majorId'], $data['baseId']);
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
                $data['baseName'] = $oBaseClass->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
//        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('verifyMarksNomination', $data);
    }

    public function interviewMarksAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['marksCtgry'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['marksCtgry'] = $post['marksCtgry'];

            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['marksCtgry'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                if ($post['marksCtgry'] == 'WITH') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdWithInterviewMarks($post['offerId'], $post['majorId']);
                } else if ($post['marksCtgry'] == 'WITHOUT') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdWOInterviewMarks($post['offerId'], $post['majorId']);
                }
                $data['total'] = sizeof($data['results']);
                if ($offerData['cCode'] == 4 || $offerData['cCode'] == 50) {
                    $data['totalMarks'] = 40;
                } else {
                    $data['totalMarks'] = 20;
                }

                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
                $data['cCode'] = $offerData['cCode'];
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
//        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewMarks', $data);
    }

    public function interviewMarksInterAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['marksCtgry'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['marksCtgry'] = $post['marksCtgry'];
            $data['baseId'] = $post['baseId'];

            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['marksCtgry']) && !empty($post['baseId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oBaseClassModel = new \models\BaseClassModel();
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                if ($post['marksCtgry'] == 'WITH') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdAndBaseIdWithInterviewMarks($offerIds, $post['majorId'], $post['baseId']);
                } else if ($post['marksCtgry'] == 'WITHOUT') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdAndBaseIdWOInterviewMarks($offerIds, $post['majorId'], $post['baseId']);
                }
                $data['total'] = sizeof($data['results']);
                $data['totalMarks'] = 10;

                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $data['baseId']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
                $data['cCode'] = $offerData['cCode'];
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
//        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewMarksInter', $data);
    }

    public function interviewPlanAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['planCtgry'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['planCtgry'] = $post['planCtgry'];

            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['planCtgry'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, transferInterviewData');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                if ($post['planCtgry'] == 'With Schedule') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdInterviewPlan($offerIds, $post['majorId']);
                } elseif ($post['planCtgry'] == 'Without Schedule') {
                    if ($offerData['transferInterviewData'] == 'YES') {
                        $data['insertMsg'] = $oUGTResultModel->transferApplicantsByOfferIdAndMajorId($data['offerId'], $data['majorId'], 1);
                    }
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdWOInterviewPlan($offerIds, $post['majorId']);
                }
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
//                  echo '<pre>';
//                var_dump($data['majorName']);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewPlan', $data);
    }

    public function interviewPlanCopyPasteAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['planCtgry'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['planCtgry'] = $post['planCtgry'];

            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['planCtgry'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                if ($post['planCtgry'] == 'With Schedule') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdInterviewPlan($offerIds, $post['majorId']);
                } elseif ($post['planCtgry'] == 'Without Schedule') {
//                    $data['insertMsg'] = $oUGTResultModel->transferApplicantsByOfferIdAndMajorId($data['offerId'], $data['majorId'], 1);
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdWOInterviewPlan($offerIds, $post['majorId']);
                }
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
//                  echo '<pre>';
//                var_dump($data['majorName']);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewPlanCopyPaste', $data);
    }

    public function interviewPlanInterAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['planCtgry'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['baseId'];
            $data['planCtgry'] = $post['planCtgry'];

            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['planCtgry']) && !empty($post['baseId'])) {
//                print_r($post);exit;
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oBaseClassModel = new \models\BaseClassModel();
                $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBaseAndDId($data['dId'], $offerData['cCode']);
                $oMajorsModel = new \models\MajorsModel();
                $oUGTResultModel = new \models\UGTResultModel();
                if ($post['planCtgry'] == 'With Schedule') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdAndBaseIdInterviewPlan($offerIds, $post['majorId'], $post['baseId']);
                } elseif ($post['planCtgry'] == 'Without Schedule') {
                    $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdAndBaseIdWOInterviewPlan($offerIds, $post['majorId'], $post['baseId']);
                }
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
//                  echo '<pre>';
//                var_dump($data['majorName']);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewPlanInter', $data);
    }

    public function interviewAttendanceAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['interviewDate'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {

            $post = $this->post()->all();
//            print_r($post);exit;
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['interviewDate'] = $post['interDate'];
            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['interDate'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $oUGTResultModel = new \models\UGTResultModel();
                $data['interviewDates'] = $oUGTResultModel->findInterviewDatesByOfferIdAndByMajorId($data['offerId'], $data['majorId']);
                $data['applications'] = $oUGTResultModel->findByOfferIdAndByMajorIdByInterviewDate($offerIds, $data['majorId'], $data['interviewDate']);
                $data['total'] = sizeof($data['applications']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $data['majorId']);

                $obj = new \mihaka\formats\MihakaPDF();
                $HTML = $this->getHTML('interviewAttendanceSheetPDF', $data);
                $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
                $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendence Sheet</td></tr></table> ");
                $obj->getPDFObject()->SetHeader($obj->getHeader());
                $obj->getPDFObject()->SetFooter($obj->getFooter());
                $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                $obj->getPDFObject()->output();
            }
        } else {

            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewAttendance', $data);
    }

    public function interviewAttendanceAsPannelAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['interviewDate'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {

            $post = $this->post()->all();
//            print_r($post);exit;
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['interviewDate'] = $post['interDate'];
            if (!empty($post['offerId']) && !empty($post['majorId']) && !empty($post['interDate'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $offerIds = $oAdmmissionOfferModel->getChildOfferIds($post['offerId']);
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $oUGTResultModel = new \models\UGTResultModel();
                $data['interviewDates'] = $oUGTResultModel->findInterviewDatesByOfferIdAndByMajorId($data['offerId'], $data['majorId']);
                $data['interviewVenues'] = $oUGTResultModel->findInterviewVenuesByOfferIdAndByMajorIdAndByInterviewDate($data['offerId'], $data['majorId'], $post['interDate']);
//                print_r($data['interviewVenues']);
//                exit;
//                $data['applications'] = $oUGTResultModel->findByOfferIdAndByMajorIdByInterviewDate($offerIds, $data['majorId'], $data['interviewDate']);
//                $data['total'] = sizeof($data['applications']);
//                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $data['majorId']);
//
//                $obj = new \mihaka\formats\MihakaPDF();
//                $HTML = $this->getHTML('interviewAttendanceSheetPDF', $data);
//                $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
//                $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendence Sheet</td></tr></table> ");
//                $obj->getPDFObject()->SetHeader($obj->getHeader());
//                $obj->getPDFObject()->SetFooter($obj->getFooter());
//                $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
//                $obj->getPDFObject()->WriteHTML($HTML, 2);
//                $obj->getPDFObject()->output();
            }
        } else {

            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('interviewAttendanceAsPannel', $data);
    }

    public function trialAttendanceAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['interviewDate'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if ($post['offerId'] != '') {
                $data['offerId'] = $post['offerId'];
                $data['baseId'] = $post['admissionBase'];
                $data['trialDate'] = $post['trialDate'];
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
                $oBaseClassModel = new \models\BaseClassModel();
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $post['admissionBase']);
                $oUGTResultModel = new \models\UGTResultModel();
                $data['trialDates'] = $oUGTResultModel->findTrialDatesByOfferIdAndByBaseId($data['offerId'], $post['admissionBase']);
                $data['applications'] = $oUGTResultModel->findByOfferIdAndByBaseIdByTrialDate($data['offerId'], $data['baseId'], $data['trialDate']);
                $data['total'] = sizeof($data['applications']);
//              
//            print_r($data);exit;
                $obj = new \mihaka\formats\MihakaPDF();
                $HTML = $this->getHTML('trialAttendanceSheetPDF', $data);
                $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
                $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendence Sheet</td></tr></table> ");
                $obj->getPDFObject()->SetHeader($obj->getHeader());
                $obj->getPDFObject()->SetFooter($obj->getFooter());
                $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
                $obj->getPDFObject()->WriteHTML($HTML, 2);
                $obj->getPDFObject()->output();
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
//        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByBase($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('trialAttendance', $data);
    }

    public function attendancePDFAction() {
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['interviewDate'] = $post['interDate'];
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
            $oMajorsModel = new \models\MajorsModel();
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->findByOfferIdAndByMajorIdByInterviewDate($data['offerId'], $data['majorId'], $data['interviewDate']);
            $data['total'] = sizeof($data['applications']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $data['majorId']);
            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('interviewAttendanceSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendence Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function trialAttendancePDFAction() {
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['baseId'] = $post['admissionBase'];
            $data['trialDate'] = $post['trialDate'];
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode');
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $post['admissionBase']);
            $oUGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $oUGTResultModel->findByOfferIdAndByBaseIdByTrialDate($data['offerId'], $data['baseId'], $data['trialDate']);
            $data['total'] = sizeof($data['applications']);
//            print_r($data['applications']);exit;
            $obj = new \mihaka\formats\MihakaPDF();
            $HTML = $this->getHTML('trialAttendanceSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendence Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function applicationsAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['majorId'] = $post['majorId'];
            }
            $data['baseId'] = $post['admissionBase'];

            if (!empty($data['offerId'])) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByFilter($data['offerId'], $data['baseId'], $data['majorId']);
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
//        $data['activeClassCode'] = $oAdmmissionOfferModel->getOpeningsOfTheYear($post['admissionYear']);
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
//        print_r($data); exit;
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('applications', $data);
    }

    public function applicationsAdminAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['majorId'] = $post['majorId'];
            }
            $data['baseId'] = $post['admissionBase'];

            if (!empty($data['offerId'])) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByFilter($data['offerId'], $data['baseId'], $data['majorId']);
//                \helpers\Common::downloadCSV($data['applications'], 'applications');
//                exit;
//                  echo "<pre>";
//                print_r($data['applications']);exit;
                $data['total'] = sizeof($data['applications']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
//        $data['activeClassCode'] = $oAdmmissionOfferModel->getOpeningsOfTheYear($post['admissionYear']);
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
//        print_r($data); exit;
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('applicationsAdmin', $data);
    }

    public function applicationsDownloadAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oApplicationsModel = new \models\ApplicationsModel();
            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode, a.version';
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.dob,u.gender,u.fatherName,u.cnic,u.ph1,u.email,u.add1,ao.className,m.name majorName,m.majId,b.name baseName,isPaid,a.offerId';
//            $fields = 'a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,m.name majorName,b.name baseName,isPaid,u.name applicantName';
            if (empty($get['mid'])) {
                $data['applications'] = $oApplicationsModel->allByFilter($get['oid'], $get['bid'], '', $fields);
            } else {
                $data['applications'] = $oApplicationsModel->allByFilter($get['oid'], $get['bid'], $get['mid'], $fields);
            }
//            echo "<pre>";
//            print_r($data['applications']);
//            exit;
            $oMajorsModel = new \models\MajorsModel();
            $major = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
            $oBaseClassModel = new \models\BaseClassModel();
            $base = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $get['bid'], 0);
//            print_r($base['name']);exit;
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'isPaid' => "Paid",
                'offerId' => "Offer Id", 'appId' => "App Id", 'majId' => "Major Id", 'baseId' => "Base Id", 'cCode' => "Class Code", 'version' => "Version"];
//            $headings = ['userId'=>"User Id", 'majId'=>"Major Id", 'formNo'=>"Form #", 'cCode'=>"Class Code", 'baseId'=>"Base ID", 'childBase'=>"Child Base", 'className'=>"Class", 'majorName'=>"Major Name", 'baseName'=>"Base Name", 'isPaid'=>"Paid", 'applicantName'=>"Applicant Name"];
            \helpers\Common::downloadCSV($data['applications'], $major['name'] . '_' . strtoupper($base['name']), $headings);
        }
    }

    public function classBaseDownloadAction() {
        $get = $this->get()->all();
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode,className');
            $oApplicationsModel = new \models\ApplicationsModel();
            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.baseId,a.cCode';
//            $fields = 'a.userId,a.formNo,u.name applicantName,u.dob,u.gender,u.fatherName,u.cnic,u.ph1,u.email,u.add1,ao.className,m.name majorName,b.name baseName,isPaid';
            $data['applications'] = $oApplicationsModel->allByClassBase($get['oid'], $get['bid'], $get['cbid'], $fields);
//            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'dob' => "Date of Birth", 'gender' => "Gender",
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'isPaid' => "Paid",
                'offerId' => "Offer Id", 'appId' => "App Id", 'majId' => "Major Id", 'baseId' => "Base Id", 'cCode' => "Class Code"];
            $oBaseClassModel = new \models\BaseClassModel();
            $childBaseName = $oBaseClassModel->getBaseByClassIdAndBaseId($offerData['cCode'], $get['cbid'], $get['bid']);
//            print_r($offerData['className']);exit;
            \helpers\Common::downloadCSV($data['applications'], $offerData['className'] . ' - ' . $childBaseName['name'], $headings);
        }
    }

    public function classMajorDownloadAction() {
        $get = $this->get()->all();
//        print_r($get);exit;
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode');
            $oApplicationsModel = new \models\ApplicationsModel();
            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,testCity,religion, ao.className, shift, m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.setNo,a.baseId,a.cCode';
//            $data['applications'] = $oApplicationsModel->allByClassAndMajor($get['oid'], $get['mid'], 'Y', $fields);
            $data['applications'] = $oApplicationsModel->allByClassAndMajor($get['oid'], $get['mid'], $get['payment'], $fields);
            $oMajorsModel = new \models\MajorsModel();
            $fileName = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($fileName['name']);exit;
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address", 'testCity' => "Test City", 'religion' => "Religion",
                'className' => "Class", 'shift' => "Shift", 'majorName' => "Major", 'baseName' => "Base Name", 'isPaid' => "Paid",
                'offerId' => "Offer Id", 'appId' => "App Id", 'majId' => "Major Id", 'setNo' => "Set No", 'baseId' => "Base Id", 'cCode' => "Class Code"];
            if ($get['payment'] == 'Y') {
                $paymentStatus = '-PAID';
            } else {
                $paymentStatus = '-UNPAID';
            }
            \helpers\Common::downloadCSV($data['applications'], $fileName['name'] . $paymentStatus, $headings);
//            \helpers\Common::downloadCSV($data['applications'], 'BaseWiseApplications', $headings);
        }
    }

    public function classMajorVersionDownloadAction() {
        $get = $this->get()->all();
//        print_r($get);exit;
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode');
            $oApplicationsModel = new \models\ApplicationsModel();
            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,testCity,religion, ao.className, shift, m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.setNo,a.baseId,a.cCode';
//            $data['applications'] = $oApplicationsModel->allByClassAndMajor($get['oid'], $get['mid'], 'Y', $fields);
            $version = 2;
            $data['applications'] = $oApplicationsModel->allByClassAndMajorAndVersion($get['oid'], $get['mid'], $get['payment'], $version, $fields);
            $oMajorsModel = new \models\MajorsModel();
            $fileName = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($fileName['name']);exit;
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address", 'testCity' => "Test City", 'religion' => "Religion",
                'className' => "Class", 'shift' => "Shift", 'majorName' => "Major", 'baseName' => "Base Name", 'isPaid' => "Paid",
                'offerId' => "Offer Id", 'appId' => "App Id", 'majId' => "Major Id", 'setNo' => "Set No", 'baseId' => "Base Id", 'cCode' => "Class Code"];
            if ($get['payment'] == 'Y') {
                $paymentStatus = '-PAID';
            } else {
                $paymentStatus = '-UNPAID';
            }
            \helpers\Common::downloadCSV($data['applications'], $fileName['name'] . $paymentStatus, $headings);
//            \helpers\Common::downloadCSV($data['applications'], 'BaseWiseApplications', $headings);
        }
    }

    public function classMajorMorningEveningDownloadAction() {
        $get = $this->get()->all();
//        print_r($get);exit;
        if (!empty($get['oid'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $offerData = $oAdmissionOffer->findByPK($get['oid'], 'cCode');
            $oApplicationsModel = new \models\ApplicationsModel();
            $fields = 'a.userId,a.formNo,u.name applicantName,u.gender,u.fatherName,u.cnic,u.dob,u.ph1,u.email,u.add1,testCity,ao.className,m.name majorName,b.name baseName,isPaid,a.offerId,a.appId,m.majId,a.setNo,a.baseId,a.cCode';
//            $data['applications'] = $oApplicationsModel->allByClassAndMajor($get['oid'], $get['mid'], 'Y', $fields);
            $data['applications'] = $oApplicationsModel->allByClassAndMajorMorningEvening($get['oid'], $get['mid'], $get['payment'], $fields);
            $oMajorsModel = new \models\MajorsModel();
            $fileName = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($get['oid'], $offerData['cCode'], $get['mid']);
//            print_r($fileName['name']);exit;
            $headings = ['userId' => "User Id", 'formNo' => "Form #", 'applicantName' => "Applicant Name", 'gender' => "Gender",
                'fatherName' => "Father Name", 'cnic' => "CNIC", 'dob' => "Date of Birth", 'ph1' => "Contact No", 'email' => "Email", 'add1' => "Address", 'testCity' => "Test City",
                'className' => "Class", 'majorName' => "Major", 'baseName' => "Base Name", 'isPaid' => "Paid",
                'offerId' => "Offer Id", 'appId' => "App Id", 'majId' => "Major Id", 'setNo' => "Set No", 'baseId' => "Base Id", 'cCode' => "Class Code"];
            if ($get['payment'] == 'Y') {
                $paymentStatus = '-PAID';
            } else {
                $paymentStatus = '-UNPAID';
            }
            \helpers\Common::downloadCSV($data['applications'], $fileName['name'] . $paymentStatus, $headings);
//            \helpers\Common::downloadCSV($data['applications'], 'BaseWiseApplications', $headings);
        }
    }

    public function classBaseListAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['childBase'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
                $oBaseClass = new \models\BaseClassModel();
                $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
                $data['baseId'] = $post['admissionBase'];
                $data['childBase'] = $post['admissionBaseChild'];
                $data['childBases'] = $oBaseClass->getBasesByOfferIdAndClassIdAndParentBase($post['offerId'], $offerData['cCode'], $post['admissionBase']);
//              $data['childBases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode'], $post['admissionBase']);
//echo '<pre>';
//print_r($data['childBases']);
//exit;
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->allByClassBase($data['offerId'], $data['baseId'], $data['childBase']);
                $data['total'] = sizeof($data['applications']);
            }
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('classBaseList', $data);
    }

    public function challanListAction() {
        $data = [];
        if ($this->isPost()) {
            $post = $this->post()->all();
//        print_r($post);exit;
            $oApplicationsModel = new \models\ApplicationsModel();
            $data['challans'] = $oApplicationsModel->getChallanByDate($post['dochallan']);
            $data['date'] = $post['dochallan'];
            $data['total'] = sizeof($data['challans']);
//        print_r($data);exit;
        }
        $this->render('challanList', $data);
    }
}
