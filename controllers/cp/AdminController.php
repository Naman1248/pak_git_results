<?php

/**
 * Description of AdminController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class AdminController extends \controllers\cp\StateController {

    private $aggregateUGTFunction = [
        '3' => 'UGTAggregateSpecial',
        '7' => 'UGTAggregateHafiz',
        '9' => 'UGTAggregateGeneral',
        '10' => 'UGTAggregateSpecial',
        '11' => 'UGTAggregateGeneral',
        '18' => 'UGTAggregateGeneral',
        '20' => 'UGTAggregateGeneral',
        '36' => 'UGTAggregateGeneral',
        '69' => 'UGTAggregateGeneral',
        '31' => 'UGTAggregateElectrical',
        '32' => 'UGTAggregateElectrical',
        '37' => 'UGTAggregateElectrical',
        '41' => 'UGTAggregateElectrical',
        '72' => 'UGTAggregateSpecial'
    ];

    public function classBaseMajorsAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $post = $this->post()->all();
        if ($this->isPost()) {
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];

            if (!empty($post['offerId']) && !empty($post['majorId'])) {
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className, year');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
//                var_dump($data['majorName']);exit;
                $oBaseClassModel = new \models\BaseClassModel();
                $data['results'] = $oBaseClassModel->getBasesByClassIdAndParentBase($data['offerData']['cCode']);

                $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
                $data['majorBases'] = $oClassBaseMajorModel->getBasesByMajorAdmission($data['offerData']['cCode'], $post['majorId']);
//                echo "<pre>"; var_dump($data['majorBases']);
                foreach ($data['results'] as $key => $rowParent) {
                    foreach ($data['majorBases'] as $key1 => $rowChild) {
                        if ($rowParent['baseId'] == $rowChild['baseId']) {
                            if ($rowChild['gender'] == 'Male') {

                                $data['results'][$key]['male'] = 'YES';
                            } else if ($rowChild['gender'] == 'Female') {

                                $data['results'][$key]['Female'] = 'YES';
                            } else if ($rowChild['gender'] == 'Transgender') {

                                $data['results'][$key]['Transgender'] = 'YES';
                            }
                        }
                    }
                }
//                echo "<pre>";
//                var_dump($data['results']);
//                exit;
            } else {
                $post['admissionYear'] = $post['admissionYear'];
            }
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        if (!empty($data['dId']) && !empty($post['admissionYear'])) {
            $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        }
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('classBaseMajors', $data);
    }

    public function testMarksAction() {

        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['admissionYear'] = 2025;
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $data['currentClasses'] = $oAdmissionOfferModel->getOfferedTestProgramByYear($data['admissionYear']);
//        echo "<pre>";
//        var_dump($data['currentClasses']);
        $this->render('testMarks', $data);
    }

    public function applicationsByUserAction() {
        $data['refNo'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
//            print_r($post);
            if (!empty($post['refNo'])) {
                $data['refNo'] = $post['refNo'];
                $oApplicationsModel = new \models\ApplicationsModel();
                $data['applications'] = $oApplicationsModel->byUserId($data['refNo'], $offerId);

                $oChallansModel = new \models\ChallansModel();
                foreach ($data['applications'] as &$row) {
                    $challanData = $oChallansModel->isChallanExistByChalId($row['chalId']);
                    $row['isFreezed'] = $challanData['isFreezed'] ?? "NA";
                    $row['challanIsPaid'] = $challanData['isPaid'];
                }
                unset($row); // break the reference with the last element
            }
        }

        $this->render('applicationsByUser', $data);
    }

    public function adminDashboardAction() {

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
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $userData = $this->state()->get('userInfo');
        $data['secKey'] = \mihaka\helpers\MString::encrypt($userData['userId']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('adminDashboard');
    }

    public function testAction() {
        $this->render('test');
    }

    public function MajorWiseCombinationPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOfferModel = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOfferModel->findByPK($get['offerId'], 'cCode, className, year');
            $oMajorsModel = new \models\MajorsModel();
            $data['majorData']['name'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($get['offerId'], $get['majId']);
            $oSubjectCombinationModel = new \models\SubjectCombinationModel();
            $data['subjects'] = $oSubjectCombinationModel->findByClassAndGroup($get['cCode'], $get['majId']);

            $data['total'] = sizeof($data['subjects']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('MajorWiseCombinationsPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Subject Combinations</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function ClassMajorWiseBasesPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['offerId'])) {
            $oAdmissionOfferModel = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOfferModel->findByPK($get['offerId'], 'cCode, className, year');
            $oMajorsModel = new \models\MajorsModel();
            $data['majorData']['name'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($get['offerId'], $get['majId']);
            $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
            $data['basesData'] = $oClassBaseMajorModel->getAllBasesByMajorAdmin($get['cCode'], $get['majId']);
            $oBaseClassModel = new \models\BaseClassModel();
            foreach ($data['basesData'] as $key => $row) {
                if ($row['parentBaseId'] == 0) {

                    $data['basesData'][$key]['parentBaseName'] = "PARENT";
                } else {
                    $parentBase = $oBaseClassModel->getBaseByClassIdAndBaseId($get['cCode'], $row['parentBaseId']);
                    $data['basesData'][$key]['parentBaseName'] = $parentBase['name'];
                }
            }
            $data['total'] = sizeof($data['basesData']);

            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('ClassMajorBasesPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Bases List</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }

    public function majorWiseBasesAction() {
        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->post()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            if (!empty($post['offerId'])) {
                $oAdmissionOfferModel = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOfferModel->findByPK($post['offerId'], 'cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorsByOfferId($post['offerId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::currentYear();
        $this->render('majorWiseBases', $data);
    }

    public function verifyAdmissionBasisAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['planCtgry'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];

            if (!empty($post['offerId']) && !empty($post['majorId'])) {
//                print_r($post);
                $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
                $offerData = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className');
                $oMajorsModel = new \models\MajorsModel();
                $oClassBaseMajor = new \models\ClassBaseMajorModel();
                $data['results'] = $oClassBaseMajor->getAllBasesByMajorAdmin($offerData['cCode'], $post['majorId']);
                $data['total'] = sizeof($data['results']);
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $offerData['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $offerData['cCode'], $post['majorId']);
                $data['className'] = $offerData['className'];
//                  echo '<pre>';
//                var_dump($data['className']);exit;
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('verifyAdmissionBasis', $data);
    }

    public function shiftApplicantAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['formNo'] = $post['formNo'];
            $data['setNo'] = $post['setNo'];
            if (!empty($data['offerId'])) {
                if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo']) || empty($data['setNo'])) {
                    $data['errorMsg'] = 'Please enter all information';
                } else {
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');

                    $oMajorsModel = new \models\MajorsModel();
                    $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);

                    $oBaseClassModel = new \models\BaseClassModel();
                    $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);

                    $oApplicationsModel = new \models\ApplicationsModel();
                    $formData = $oApplicationsModel->findOneByField('formNo', $data['formNo'], 'appId');

                    $oUgtResultModel = new \models\UGTResultModel();
                    $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId, meritList, majId');

                    $oGATSlipModel = new \models\GatSlipModel();
                    $slipData = $oGATSlipModel->findOneByField('formNo', $data['formNo'], 'id');

                    $oGATResultModel = new \models\gatResultModel();
                    $resultData = $oGATResultModel->findOneByField('formNo', $data['formNo'], 'id');

                    if (empty($formData)) {
                        $data['errorMsg'] = 'Application for this Form Does Not Exist.';
                    } elseif (!empty($ugtData) && !empty($ugtData['meritList'])) {
                        $data['errorMsg'] = 'Cannot shift because already selected for Merit List : ' . $ugtData['meritList'];
                    } else {
                        $this->db()->beginTransaction();
                        $applicationupdate = $oApplicationsModel->upsert(
                                [
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'majId' => $data['majorId'],
                                    'baseId' => $data['baseId'],
                                    'setNo' => $data['setNo']
                                ], $formData['appId']);

                        if (!empty($ugtData)) {
                            if ($data['majorId'] == $ugtData['majId']) {
                                $ugtresultupdate = $oUgtResultModel->upsert(
                                        [
                                            'offerId' => $data['offerId'],
                                            'cCode' => $data['offerData']['cCode'],
                                            'majId' => $data['majorId'],
                                            'baseId' => $data['baseId'],
                                            'setNo' => $data['setNo'],
                                            'major' => $data['majorName']['name'],
                                            'baseName' => $data['baseName']['name'],
                                            'shift' => $data['offerData']['shift'],
                                            'class' => $data['offerData']['className'],
                                            'totAgg' => NULL,
                                            'isVerified' => 'NO',
                                            'locMeritList' => 'N'
                                        ], $ugtData['appId']);
                            } else {
                                $ugtresultupdate = $oUgtResultModel->upsert(
                                        [
                                            'offerId' => $data['offerId'],
                                            'cCode' => $data['offerData']['cCode'],
                                            'majId' => $data['majorId'],
                                            'baseId' => $data['baseId'],
                                            'setNo' => $data['setNo'],
                                            'major' => $data['majorName']['name'],
                                            'baseName' => $data['baseName']['name'],
                                            'shift' => $data['offerData']['shift'],
                                            'class' => $data['offerData']['className'],
                                            'interviewObt' => NULL,
                                            'interviewResult' => NULL,
                                            'interviewAgg' => NULL,
                                            'totAgg' => NULL,
                                            'isVerified' => 'NO',
                                            'locMeritList' => 'N'
                                        ], $ugtData['appId']);
                            }
                        }
                        if (!empty($slipData)) {
                            $slipDataupdate = $oGATSlipModel->upsert(
                                    [
                                        'offerId' => $data['offerId'],
                                        'majId' => $data['majorId'],
                                        'baseId' => $data['baseId'],
                                        'major' => $data['majorName']['name'],
                                        'base' => $data['baseName']['name'],
                                    ], $slipData['id']);
                        }
                        if (!empty($resultData)) {
                            $gatResultUdate = $oGATResultModel->upsert(
                                    [
                                        'offerId' => $data['offerId'],
                                        'majId' => $data['majorId'],
                                        'major' => $data['majorName']['name'],
                                        'baseId' => $data['baseId'],
                                        'base' => $data['baseName']['name']
                                    ], $resultData['id']);
                        }

                        if ($applicationupdate) {
                            $this->db()->commit();
                            $data['updateMsg'] = 'Applicant Shifted Successfully.';
                        } else {
                            $this->db()->rollback();
                            $data['errorMsg'] = 'Some error occured, please try again.';
                        }
                    }
                }
            }
        }
        if (!empty($data['offerId'])) {
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('shiftApplicant', $data);
    }

    public function searchFormAction() {

        $offerId = 108;
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOfferModel->findByPK($offerId, 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $data['majors'] = $oMajorsModel->getMajorsByOfferId($offerId);
        $oBaseClassModel = new \models\BaseClassModel();
        $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($offerData['cCode']);
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $this->render('searchForm', $data);
    }

    public function updateBaseAction() {

        $offerId = 108;
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOfferModel->findByPK($offerId, 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $data['majors'] = $oMajorsModel->getMajorsByOfferId($offerId);
        $oBaseClassModel = new \models\BaseClassModel();
        $data['bases'] = $oBaseClassModel->getBasesByClassIdAndParentBase($offerData['cCode']);
        $oMetaDataModel = new \models\MetaDataModel();
        $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');

        $this->render('updateBase', $data);
    }

    public function applyAction() {

        $userId = 1;
        $oOverseasModel = new \models\OverseasModel();
        $data['overseasInfo'] = $oOverseasModel->findByPK($userId);

        $oCountriesModel = new \models\CountriesModel();
        $data['countries'] = $oCountriesModel->overseasCountries();

        $oFieldsModel = new \models\FieldsModel();
        $data['fields'] = $oFieldsModel->getFields(FRM_APPLY, 61);
        $oOalevelModel = new \models\OalevelModel();
        $data['oalevel']['compulsory'] = $oOalevelModel->getOLevelCompulsorySubjects();
        $data['oalevel']['gradesList'] = $oOalevelModel->getGradesList();
//        print_r($offerId); exit;

        $this->render('apply', $data);
    }

    public function updateUserAction() {

        $this->render('updateUser');
    }

    public function sendEmailsAction() {
        $ougtPlanModel = new \models\UGTPlanModel();
        $data = $ougtPlanModel->emailSeatingPlan();
        foreach ($data as $row) {
            $emailText = ' Dear Mr. / Ms. ' . $row['name'] . ', <br><br> It is to inform you that your GCU Entry Test Schedule is as follows : <br><br> Dated : ' . $row['date'] . '<br> Time  : ' . $row['time'] . '<br> Venue : ' . $row['venue'] . '<br><br> Please visit www.gcuonline.pk to download your Roll Number Slip for test.';

            $oEmailQueueModel = new \models\EmailQueueModel();
            $oEmailQueueModel->upsert([
                'email' => $row['Email'],
                'subject' => 'GCU ENTRY TEST SCHEDULE',
                'contents' => $emailText,
                'added_on' => date('Y-m-d H:i:s')
            ]);
            $oMailGun = new \components\MailGun();
            $tag = 'GCU Admissions 2022';
            $text = str_ireplace('<br>', "\r\n", $emailText);
            $oMailGun->sendMail($row['Email'], $row['name'], 'GCU ENTRY TEST SCHEDULE', $emailText, $text, $tag);
        }
    }

    public function sendEmailsTestCenterAction() {
        $get = $this->get()->all();
//        var_dump($get['majId']);exit;
//        $offerId = 26;
        $oApplicationsModel = new \models\ApplicationsModel();
        $oUsersModel = new \models\UsersModel();
        $out = $oApplicationsModel->countEmailsTestCenterByOfferId($get['offerId'], $get['majId']);
        $tot = $out['tot'];
        $perPage = 100;
        $offset = 0;
        $totalIteratios = ceil($tot / $perPage);
        for ($i = 0; $i < $totalIteratios; $i++) {

            $data = $oApplicationsModel->emailsTestCenterByOfferId($get['offerId'], $get['majId'], $offset, $perPage);
            //print_r($data);
            //exit;
            if (!empty($data)) {
                foreach ($data as $row) {
                    $emailText = ' Dear ' . $row['name'] . ', <br><br> It is to inform you that to facilitate our talented applicants across the country, we intend to set up examination center in each provincial capital (Lahore, Karachi, Peshawar, Quetta) and in Islamabad to conduct entrance test. You are requested to choose your convenient test center on GCU Online Admission Portal (www.gcuonline.pk) till 22 August 2022.';

                    $oEmailQueueModel = new \models\EmailQueueModel();
                    $oEmailQueueModel->upsert([
                        'email' => $row['em'],
                        'subject' => 'GCU ENTRY TEST CENTER',
                        'contents' => $emailText,
                        'added_on' => date('Y-m-d H:i:s')
                    ]);
                    $oMailGun = new \components\MailGun();
                    $tag = 'UPDATE TEST CENTER';
                    $text = str_ireplace('<br>', "\r\n", $emailText);
                    $oMailGun->sendMail($row['em'], $row['name'], 'GCU ENTRY TEST CENTER', $emailText, $text, $tag);
                    $oUsersModel->upsert(['centreUpdated' => 'Y'], $row['userId']);
                    echo "<br>" . $row['userId'] . "....here..." . $row['em'] . "<br>";
                    //$offset += $perPage;
                }
            }
            $offset += $perPage;
            echo "<br>" . $offset . "....offset...perpage..." . $perPage . "here is i ... " . $i . "<br>";
        }
    }

    public function sendEmailsMSAdmissionAction() {
        $get = $this->get()->all();
//        var_dump($get['majId']);exit;
//        $offerId = 26;
        $oUsersModel = new \models\UsersModel();
        $oGatResultModel = new \models\gatResultModel();
        $out = $oGatResultModel->countMSEmailsGATPassByOfferId($get['offerId']);
        print_r($out);
        $tot = $out['tot'];
        $perPage = 100;
        $offset = 0;
        $totalIteratios = ceil($tot / $perPage);
        echo $totalIteratios;
//        exit;
        for ($i = 0; $i < $totalIteratios; $i++) {

            $data = $oGatResultModel->emailsMSGATPassByOfferId($get['offerId'], $offset, $perPage);
            if (!empty($data)) {
                foreach ($data as $row) {
                    $emailText = ' Dear ' . $row['name'] . ', <br><br> It is to inform you that MS Admission are open for those who are pass in GCU-GAT-2022 I and II. You are requested to apply for MS admission on GCU Online Admission Portal (www.gcuonline.pk) till 26 August 2022.';

                    $oEmailQueueModel = new \models\EmailQueueModel();
                    $oEmailQueueModel->upsert([
                        'email' => $row['em'],
                        'subject' => 'GCU MS ADMISSION FOR GCC-GAT 2022 I AND II',
                        'contents' => $emailText,
                        'added_on' => date('Y-m-d H:i:s')
                    ]);
                    $oMailGun = new \components\MailGun();
                    $tag = 'APPLY FOR MS ADMISSION 2022';
                    $text = str_ireplace('<br>', "\r\n", $emailText);
                    $oMailGun->sendMail($row['em'], $row['name'], 'GCU MS ADMISSION', $emailText, $text, $tag);
                    $oUsersModel->upsert(['msAdmission' => 'Y'], $row['userId']);
                    echo "<br>" . $row['userId'] . "....here..." . $row['em'] . "<br>";
                    //$offset += $perPage;
                }
            }
            $offset += $perPage;
            echo "<br>" . $offset . "....offset...perpage..." . $perPage . "here is i ... " . $i . "<br>";
        }
    }

    public function logsAction() {
        $get = $this->get()->all();
        print_r($get['dochallan']);
        $data = [];
        $errFilePath = $this->getAppConfigsByKeys('errors', 'errFilePath');
//        $date = date('Y-m-d');
        $date = $get['dochallan'];
        $file = $errFilePath . $date . '.txt';
        $data['contents'] = nl2br(file_get_contents($file));
        $this->render('logs', $data);
    }

    public function editApplicationAction() {
        $get = $this->get()->all();
        $appId = $get['appId'];
        $oApplicationsModel = new \models\ApplicationsModel();
        $applicationData = $oApplicationsModel->findByPK($appId);
//        print_r($applicationData);exit;
        $userId = $applicationData['userId'];
        $offerId = $applicationData['offerId'];
        $cCode = $applicationData['cCode'];

        $oEmployeeBaseModel = new \models\employeeBaseModel();
        $data['employeeInfo'] = $oEmployeeBaseModel->findByPK($userId);

        $oOverseasModel = new \models\OverseasModel();
        $data['overseasInfo'] = $oOverseasModel->findByPK($userId);

        $oCountriesModel = new \models\CountriesModel();
        $data['countries'] = $oCountriesModel->overseasCountries();

        $oBaseClass = new \models\BaseClassModel();
        $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($cCode);
//        $data['bases']=$oBaseClass->findByField('cCode', $cCode);

        $oMajorsModel = new \models\MajorsModel();
        $data['majors'] = $oMajorsModel->getMajorByOfferIdClassId($offerId, $cCode, $userId);

        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $data['sets'] = $oSubjectCombinationModel->findByField('cCode', $cCode, 'gCode', 5, 'setNo,sub1');
        $data['offerId'] = $offerId;
        $oFieldsModel = new \models\FieldsModel();
        $data['fields'] = $oFieldsModel->getFields(FRM_APPLY, $cCode);
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getCurrentOpenings();

//        print_r($offerId); exit;
        $this->render('editApplication', $data);
    }

    public function byUserApplicationsAction() {
        $get = $this->get()->all();
//        print_r($get['referenceNo']);exit;
        $data['referenceNo'] = $get['referenceNo'];
        $oApplications = new \models\ApplicationsModel();
        $data['applications'] = $oApplications->byUserId($data['referenceNo'], NULL);
        $oUsersModel = new \models\UsersModel();
        $data['userData'] = $oUsersModel->findByPK($data['referenceNo']);
        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        foreach ($data['applications'] as $key => $row) {
            if (!empty($row['childBase'])) {
                $data['applications'][$key]['childBase'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($row['cCode'], $row['childBase'], $row['majId'], $data['userData']['gender'], $row['baseId']);
            } else {
                $data['applications'][$key]['childBase'] = [];
            }
        }
//        print_r($data['applications']);exit;
        $this->render('byUserApplications', $data);
    }

    public function MSAggregateAction() {
        $oUgtResultModel = new \models\UGTResultModel();
//        $oUgtResultModel->calculateMSAggregate($offerId, $majId, $baseId);
//        $oUgtResultModel->calculateMSAggregate(39, 3, 9, 11899321);
        $oUgtResultModel->calculateMSAggregate(39, 9, 9);
        $oUgtResultModel->calculateMSAggregate(39, 9, 3);
        $oUgtResultModel->calculateMSAggregate(40, 9, 9);
        $oUgtResultModel->calculateMSAggregate(40, 9, 3);
    }

    private function getAggregateName($baseId) {
        return $this->aggregateUGTFunction[$baseId];
    }

    public function addNewAdmissionAction() {
        $data['classCode'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        if ($this->isPost()) {
            $post = $this->post()->all();
//              
            $oGatSlipModel = new \models\GatSlipModel();
            $params = [
                'name' => $post['name'],
                'fatherName' => $post['fname'],
                'cnic' => $post['cnic'],
                'userId' => $post['userid'],
                'majId' => $post['majorId'],
                'offerId' => $post['offerId'],
                'date' => $post['testDate'],
                'time' => $post['testTime'],
                'venue' => $post['testVenue'],
                'rollNo' => $post['rno'],
                'major' => $post['majorName'],
            ];
            $oGatSlipModel->insert($params);
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['yearList'] = \helpers\Common::currentYearList();
        $oClassesModel = new \models\ClassesModel();
        $data['activeClassCode'] = $oClassesModel->findAll('cCode,className');
        return $this->render('addNewAdmission', $data);
    }

    public function UGTAggregateAction() {
//    public function UGTAggregateAction($offerId, $majId, $baseId) {
        $offerId = 34;
        $majId = 53;
        $oUgtResultModel = new \models\UGTResultModel();
        $baseId = 31;
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId);
        $baseId = 32;
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId);
        $baseId = 32;
        $oUgtResultModel->$func($offerId, $majId, $baseId);
        $baseId = 37;
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId);
        $baseId = 41;
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId);
    }

    public function meritListProfilePictureAction() {
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
                $data['baseId'] = $post['admissionBase'];
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
                if (empty($isMeritListExit)) {
                    $data['errMsg'] = 'This Merit List Does Not Exist.';
                } else {
                    $data['applications'] = $oUGTResultModel->profilePicturesByOfferIdAndByMajorIdAndBaseIdAndMeritList($post['offerId'], $post['majorId'], $data['baseId'], $data['meritListNo']);
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
        $this->render('meritListProfilePicture', $data);
    }
}
