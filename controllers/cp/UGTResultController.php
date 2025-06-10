<?php

/**
 * Description of GATController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class UGTResultController extends \controllers\cp\StateController {

    public function venuesListInterviewAction() {

        $data['offerId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];

        $oUGTResultModel = new \models\UGTResultModel();
        $data['rooms'] = $oUGTResultModel->allInterviewVenues();

        $this->render('venuesListInterview', $data);
    }
    public function attendanceSheetInterviewPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['venue'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK(151, 'cCode,className');
            $UGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $UGTResultModel->applicantsByInterviewVenue($get['venue'], $get['interviewDate']);
            $data['total'] = sizeof($data['applications']);
            $data['interviewVenue'] = $get['venue'];
            $data['interviewDate'] = $get['interviewDate'];
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('attendanceSheetInterviewPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }
     public function interviewMarksSheetPDFAction() {
        $get = $this->get()->all();
        if (!empty($get['venue'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK(151, 'cCode,className');
            $UGTResultModel = new \models\UGTResultModel();
            $data['applications'] = $UGTResultModel->applicantsByInterviewVenue($get['venue'], $get['interviewDate']);
            $data['total'] = sizeof($data['applications']);
            $data['interviewVenue'] = $get['venue'];
            $data['interviewDate'] = $get['interviewDate'];
            $obj = new \mihaka\formats\MihakaPDF(['format' => 'Letter', 'orientation' => 'P']);
            $HTML = $this->getHTML('interviewMarksSheetPDF', $data);
            $obj->setFooter("<table><tr><td>@Powered by System Analyst Office</td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->setHeader("<table><tr><td>GC University Lahore</td><td align='right'></td><td align='right'> Attendance Sheet</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $obj->getPDFObject()->output();
        }
    }
    public function SearchFormAction() {
        $this->render('searchForm');
    }

    public function ViewDataAction() {
        $data['offerId'] = '';
        $data['baseId'] = '';
        $data['majorId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            if (!empty($post['offerId'])) {
                $data['offerId'] = $post['offerId'];
                $data['majorId'] = $post['majorId'];
                $oAdmissionOffer = new \models\AdmissionOfferModel();
                $data['offerData'] = $oAdmissionOffer->findByPK($post['offerId'], 'cCode,className');
                $oMajorsModel = new \models\MajorsModel();
                $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
                $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $post['majorId']);
                $oBaseClassModel = new \models\BaseClassModel();
                $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
            }
            if (!empty($data['offerId'])) {
                $oUGTResultModel = new \models\UGTResultModel();
                $data['applications'] = $oUGTResultModel->viewDataByOfferIdAndByMajorId($post['offerId'], $post['majorId']);
            }
        } else {
            $post['admissionYear'] = date('Y');
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
//        print_r($data);exit;
        $this->render('viewData', $data);
    }

    public function updateUGTResultAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $oUGTResultModel = new \models\UGTResultModel();
            $params = [
                'compulsory' => $post['compMarks'],
                'subject' => $post['subMarks'],
                'total' => $post['testObt'],
                'testTotal' => $post['testTotal'],
                'status' => $post['testResult'],
                'updatedBy' => $data['id'],
                'updatedOn' => date('Y-m-d H:i:s')
            ];

            $out = $oUGTResultModel->upsert($params, $post['appId']);
//            if ($out) {
            $data['updateMsg'] = 'Record updated successfully.';
            //          } else {
            //            $data['errorMsg'] = 'Record not added, please try again..';
            //      }
        }

        $this->render('updateUGTResult', $data);
    }

    public function addUGTResultAction() {
        $data['offerId'] = '';
        $data['majorId'] = '';
        $data['baseId'] = '';
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $data['id'] = $this->state()->get('depttUserInfo')['id'];
        $post = $this->post()->all();

        if ($this->isPost()) {
            $oMajorsModel = new \models\MajorsModel();
            $post['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($post['offerId'], $post['majorId']);
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmmissionOfferModel->findByPK($post['offerId'], 'cCode, className, studyLevel, shift');
            $oBaseClassModel = new \models\BaseClassModel();
            $post['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $post['admissionBase']);
            $oUsersModel = new \models\UsersModel();
            $userData = $oUsersModel->findByPK($post['userid']);
            $oApplicationsModel = new \models\ApplicationsModel();
            $appData = $oApplicationsModel->isApplicationExistForAdmission($post['offerId'], $post['majorId'], $post['admissionBase'], $post['formNumber']);
            $data['childBaseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $appData['childBase'], $post['admissionBase']);
            $isPaid = $oApplicationsModel->isApplicationPaid($post['offerId'], $post['majorId'], $post['admissionBase'], $post['formNumber']);
            $oUGTResultModel = new \models\UGTResultModel();
            $ugtResultData = $oUGTResultModel->getByFormNo($post['formNumber']);
            $educationData = $this->userEducation($appData['userId']);
            if (empty($appData)) {
                $data['errorMsg'] = 'Application for this Form Does Not Exist.';
            } elseif (empty($isPaid)) {
                $data['errorMsg'] = 'Application is unpaid.';
            } elseif (!empty($ugtResultData)) {
                $data['errorMsg'] = 'This Form Already Exist for Merit List.';
            } elseif (empty($userData)) {
                $data['errorMsg'] = 'User Info Does Not Exist.';
            } else {
                $params = [
                    'appId' => $appData['appId'],
                    'userId' => $post['userid'],
                    'formNo' => $post['formNumber'],
                    'rollNo' => $post['rno'],
                    'name' => $post['name'],
                    'gender' => $userData['gender'],
                    'fatherName' => $post['fname'],
                    'fatherContact' => $userData['ph2'],
                    'fatherCNIC' => $userData['fatherNic'],
                    'cnic' => $post['cnic'],
                    'dob' => $userData['dob'],
                    'contactNo' => $userData['ph1'],
                    'email' => $userData['email'],
                    'add1' => $userData['add1'],
                    'class' => $data['offerData']['className'],
                    'major' => $post['majorName'],
                    'baseName' => $post['baseName']['name'],
                    'paid' => $appData['isPaid'],
                    'offerId' => $post['offerId'],
                    'majId' => $post['majorId'],
                    'baseId' => $post['admissionBase'],
                    'childBaseId' => $appData['childBase'],
                    'testCity' => $userData['testCity'],
                    'childBaseName' => $data['childBaseName']['name'],
                    'cCode' => $data['offerData']['cCode'],
                    'compulsory' => $post['compMarks'],
                    'subject' => $post['subMarks'],
                    'total' => $post['testObt'],
                    'testTotal' => $post['testTotal'],
                    'status' => $post['testResult'],
                    'testDate' => $post['testDate'],
                    'setNo' => $appData['setNo'],
                    'religion' => $userData['religion'],
                    'shift' => $data['offerData']['shift'],
                    'matricTotal' => $educationData['matricTotal'],
                    'matricObt' => $educationData['matricObt'],
                    'matricBrd' => $educationData['matricBrd'],
                    'matricRn' => $educationData['matricRn'],
                    'matricPassYear' => $educationData['matricPassYear'],
                    'matricExamNature' => $educationData['matricExamNature'],
                    'interTotal' => $educationData['interTotal'] ?? NULL,
                    'interObt' => $educationData['interObt'] ?? NULL,
                    'interBrd' => $educationData['interBrd'] ?? NULL,
                    'interRn' => $educationData['interRn'] ?? NULL,
                    'interPassYear' => $educationData['interPassYear'] ?? NULL,
                    'interExamNature' => $educationData['interExamNature'] ?? NULL,
                    'bsHonsTot' => $educationData['bsHonsTot'] ?? NULL,
                    'bsHonsObt' => $educationData['bsHonsObt'] ?? NULL,
                    'honsUni' => $educationData['honsUni'] ?? NULL,
                    'honsRn' => $educationData['honsRn'] ?? NULL,
                    'honsPassYear' => $educationData['honsPassYear'] ?? NULL,
                    'honsExamNature' => $educationData['honsExamNature'] ?? NULL
                ];
//            var_dump($params);exit;
                $out = $oUGTResultModel->insert($params);
//                if ($out) {
//                    $data['addMsg'] = 'Record added successfully.';
//                } else {
//                    $data['errorMsg'] = 'Record not added, please try again..';
//                }
            }
        } else {
            $post['admissionYear'] = date('Y');
        }

        $data['admissionYear'] = $post['admissionYear'];

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();
        $this->render('addUGTResult', $data);
    }

    public function transferMSAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['formNo'] = $post['formNo'];
            $data['setNo'] = $post['setNo'];
            if (!empty($data['offerId'])) {
                if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo'])) {
                    $data['errorMsg'] = 'Please enter all information';
                } else {
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');
                    $oMajorsModel = new \models\MajorsModel();
                    $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
                    $oBaseClassModel = new \models\BaseClassModel();
                    $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
                    $oUgtResultModel = new \models\UGTResultModel();
                    $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId');
                    $oApplicationsModel = new \models\ApplicationsModel();
                    $appData = $oApplicationsModel->isApplicationExistForAdmission($data['offerId'], $data['majorId'], $data['baseId'], $data['formNo']);
                    $data['childBaseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $appData['childBase'], $data['baseId']);
                    $isPaid = $oApplicationsModel->isApplicationPaid($data['offerId'], $data['majorId'], $data['baseId'], $data['formNo']);
                    $oGATResultModel = new \models\gatResultModel();
                    $gatData = $oGATResultModel->getPassResultByUserId($appData['userId'], $data['majorId']);
                    $oUsersModel = new \models\UsersModel();
                    $userData = $oUsersModel->findByPK($appData['userId']);
                    $educationData = $this->userEducation($appData['userId']);
                    if (empty($appData)) {
                        $data['errorMsg'] = 'Application for this Form Does Not Exist.';
                    } elseif (empty($isPaid)) {
                        $data['errorMsg'] = 'Application is unpaid.';
                    } elseif (!empty($ugtData)) {
                        $data['errorMsg'] = 'This Form Already Exist for Merit List.';
                    } elseif (empty($gatData)) {
                        $data['errorMsg'] = 'GAT Pass Record Does Not Exist.';
                    } elseif (empty($userData)) {
                        $data['errorMsg'] = 'User Info Does Not Exist.';
                    } else {
                        $ugtresultinsert = $oUgtResultModel->insert(
                                [
                                    'userId' => $userData['userId'],
                                    'name' => $userData['name'],
                                    'fatherName' => $userData['fatherName'],
                                    'fatherContact' => $userData['ph2'],
                                    'fatherCNIC' => $userData['fatherNic'],
                                    'cnic' => $userData['cnic'],
                                    'dob' => $userData['dob'],
                                    'contactNo' => $userData['ph1'],
                                    'add1' => $userData['add1'],
                                    'email' => $userData['email'],
                                    'gender' => $userData['gender'],
                                    'testCity' => $userData['testCity'],
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'majId' => $data['majorId'],
                                    'baseId' => $data['baseId'],
                                    'formNo' => $data['formNo'],
                                    'paid' => 'Y',
                                    'appId' => $appData['appId'],
                                    'childBaseId' => $appData['childBase'],
                                    'childBaseName' => $data['childBaseName']['name'],
                                    'baseName' => $data['baseName']['name'],
                                    'major' => $data['majorName']['name'],
                                    'class' => $data['offerData']['className'],
                                    'rollNo' => $gatData['rollNo'],
                                    'compulsory' => $gatData['compulsory'],
                                    'subject' => $gatData['subject'],
                                    'testTotal' => $gatData['testTotal'],
                                    'total' => $gatData['total'],
                                    'status' => $gatData['status'],
                                    'testDate' => $gatData['testDate'],
                                    'religion' => $userData['religion'],
                                    'shift' => $data['offerData']['shift'],
                                    'matricTotal' => $educationData['matricTotal'],
                                    'matricObt' => $educationData['matricObt'],
                                    'matricBrd' => $educationData['matricBrd'],
                                    'matricRn' => $educationData['matricRn'],
                                    'matricPassYear' => $educationData['matricPassYear'],
                                    'matricExamNature' => $educationData['matricExamNature'],
                                    'interTotal' => $educationData['interTotal'],
                                    'interObt' => $educationData['interObt'],
                                    'interBrd' => $educationData['interBrd'],
                                    'interRn' => $educationData['interRn'],
                                    'interPassYear' => $educationData['interPassYear'],
                                    'interExamNature' => $educationData['interExamNature'],
                                    'bsHonsTot' => $educationData['bsHonsTot'],
                                    'bsHonsObt' => $educationData['bsHonsObt'],
                                    'honsUni' => $educationData['honsUni'],
                                    'honsRn' => $educationData['honsRn'],
                                    'honsPassYear' => $educationData['honsPassYear'],
                                    'honsExamNature' => $educationData['honsExamNature']
                        ]);
                        $agg = $oUgtResultModel->calculateMSAggregatebyAppId($appData['appId']);
                        $data['insertMsg'] = 'Applicant transferred successfully.';
                    }
                }// offerid
            }//is post
        }//is post
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('transferMS', $data);
    }

    public function transferApplicantsAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $post = $this->post()->all();
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['testBase'] = $post['testBase'];
            if (!empty($data['offerId'])) {
                if ( empty($data['offerId']) || empty($data['majorId']) ) {
                    $data['errorMsg'] = 'Please enter all information';
                } else {
//                    print_r($post);exit;
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');
                    $oMajorsModel = new \models\MajorsModel();
                    $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
                    $oBaseClassModel = new \models\BaseClassModel();
                    $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
                    $oUgtResultModel = new \models\UGTResultModel();
                    if (empty($data['baseId']) && empty($data['testBase'])) {
                        $data['insertMsg'] = $oUgtResultModel->transferApplicantsByOfferIdAndMajorId($data['offerId'], $data['majorId'],1);
                    } else {
                        $data['insertMsg'] = $oUgtResultModel->transferApplicantsByOfferIdAndBaseIdAndMajorId($data['offerId'], $data['majorId'], $data['baseId'], $data['testBase'], 1);
                    }
                }//is post
            }//is post
        }
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('transferApplicants', $data);
    }

    public function transferUGTApplicationAction() {
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['offerId'] = $post['offerId'];
            $data['majorId'] = $post['majorId'];
            $data['baseId'] = $post['admissionBase'];
            $data['formNo'] = $post['formNo'];
            $data['setNo'] = $post['setNo'];
            if (!empty($data['offerId'])) {
                if (empty($data['offerId']) || empty($data['majorId']) || empty($data['baseId']) || empty($data['formNo'])) {
                    $data['errorMsg'] = 'Please Enter All Information.';
                } else {
                    $oAdmissionOffer = new \models\AdmissionOfferModel();
                    $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className, shift');
                    $oMajorsModel = new \models\MajorsModel();
                    $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
                    $oBaseClassModel = new \models\BaseClassModel();
                    $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
                    $oUgtResultModel = new \models\UGTResultModel();
                    $ugtData = $oUgtResultModel->findOneByField('formNo', $data['formNo'], 'appId');
                    $oApplicationsModel = new \models\ApplicationsModel();
                    $appData = $oApplicationsModel->isApplicationExistForAdmission($data['offerId'], $data['majorId'], $data['baseId'], $data['formNo']);
                    $data['childBaseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $appData['childBase'], $data['baseId']);
                    $isPaid = $oApplicationsModel->isApplicationPaid($data['offerId'], $data['majorId'], $data['baseId'], $data['formNo']);
                    $oUsersModel = new \models\UsersModel();
                    $userData = $oUsersModel->findByPK($appData['userId']);
                    $educationData = $this->userEducation($appData['userId']);
                    if (empty($appData)) {
                        $data['errorMsg'] = 'Application for this Form Does Not Exist.';
                    } elseif (empty($isPaid)) {
                        $data['errorMsg'] = 'Application is unpaid.';
                    } elseif (!empty($ugtData)) {
                        $data['errorMsg'] = 'This Form Already Exist for Merit List.';
                    } elseif (empty($userData)) {
                        $data['errorMsg'] = 'User Info Does Not Exist.';
                    } else {
                        $ugtresultinsert = $oUgtResultModel->insert(
                                [
                                    'userId' => $userData['userId'],
                                    'name' => $userData['name'],
                                    'fatherName' => $userData['fatherName'],
                                    'fatherContact' => $userData['ph2'],
                                    'fatherCNIC' => $userData['fatherNic'],
                                    'cnic' => $userData['cnic'],
                                    'dob' => $userData['dob'],
                                    'contactNo' => $userData['ph1'],
                                    'add1' => $userData['add1'],
                                    'email' => $userData['email'],
                                    'gender' => $userData['gender'],
                                    'testCity' => $userData['testCity'],
                                    'offerId' => $data['offerId'],
                                    'cCode' => $data['offerData']['cCode'],
                                    'majId' => $data['majorId'],
                                    'baseId' => $data['baseId'],
                                    'formNo' => $data['formNo'],
                                    'paid' => 'Y',
                                    'appId' => $appData['appId'],
                                    'setNo' => $appData['setNo'],
                                    'childBaseId' => $appData['childBase'],
                                    'childBaseName' => $data['childBaseName']['name'],
                                    'baseName' => $data['baseName']['name'],
                                    'major' => $data['majorName']['name'],
                                    'class' => $data['offerData']['className'],
                                    'rollNo' => $data['formNo'],
                                    'status' => 'PASS',
                                    'religion' => $userData['religion'],
                                    'shift' => $data['offerData']['shift'],
                                    'matricTotal' => $educationData['matricTotal'],
                                    'matricObt' => $educationData['matricObt'],
                                    'matricBrd' => $educationData['matricBrd'],
                                    'matricRn' => $educationData['matricRn'],
                                    'matricPassYear' => $educationData['matricPassYear'],
                                    'matricExamNature' => $educationData['matricExamNature'],
                                    'interTotal' => $educationData['interTotal'] ?? NULL,
                                    'interObt' => $educationData['interObt'] ?? NULL,
                                    'interBrd' => $educationData['interBrd'] ?? NULL,
                                    'interRn' => $educationData['interRn'] ?? NULL,
                                    'interPassYear' => $educationData['interPassYear'] ?? NULL,
                                    'interExamNature' => $educationData['interExamNature'] ?? NULL,
                                    'bsHonsTot' => $educationData['bsHonsTot'] ?? NULL,
                                    'bsHonsObt' => $educationData['bsHonsObt'] ?? NULL,
                                    'honsUni' => $educationData['honsUni'] ?? NULL,
                                    'honsRn' => $educationData['honsRn'] ?? NULL,
                                    'honsPassYear' => $educationData['honsPassYear'] ?? NULL,
                                    'honsExamNature' => $educationData['honsExamNature'] ?? NULL
                        ]);
                        $data['insertMsg'] = 'Applicant transferred successfully.';
                    }
                }// offerid
            }//is post
        }//is post
        if (!empty($data['offerId'])) {
            $oAdmissionOffer = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOffer->findByPK($data['offerId'], 'cCode,className');
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($data['offerId'], $data['offerData']['cCode'], $data['dId']);
            $data['majorName'] = $oMajorsModel->getMajorNameByOfferIdClassIdAndMajorId($data['offerId'], $data['offerData']['cCode'], $data['majorId']);
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($data['offerData']['cCode']);
            $oBaseClassModel = new \models\BaseClassModel();
            $data['baseName'] = $oBaseClassModel->getBaseByClassIdAndBaseId($data['offerData']['cCode'], $data['baseId']);
        }
        $data['admissionYear'] = $post['admissionYear'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['admissionYear']);
        $data['yearList'] = \helpers\Common::yearList();

        $this->render('transferUGTApplication', $data);
    }

    private function userEducation($userId) {
        $oEducationModel = new \models\EducationModel();
        $userEducation = $oEducationModel->byUserIdWithoutNA($userId);
        $arr = [];
        $examsLevel = ["1" => ["marksTot" => "matricTotal", "marksObt" => "matricObt", "brdUni" => "matricBrd", "rollNo" => "matricRn", "passYear" => "matricPassYear", "examNature" => "matricExamNature"],
            "2" => ["marksTot" => "interTotal", "marksObt" => "interObt", "brdUni" => "interBrd", "rollNo" => "interRn", "passYear" => "interPassYear", "examNature" => "interExamNature"],
            "4" => ["marksTot" => "bsHonsTot", "marksObt" => "bsHonsObt", "brdUni" => "honsUni", "rollNo" => "honsRn", "passYear" => "honsPassYear", "examNature" => "honsExamNature"],
        ];
        foreach ($userEducation as $row) {
            $fieldMarksTot = $examsLevel[$row['examLevel']]['marksTot'];
            $arr[$fieldMarksTot] = $row['marksTot'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['marksObt'];
            $arr[$fieldMarksObt] = $row['marksObt'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['brdUni'];
            $arr[$fieldMarksObt] = $row['brdUni'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['rollNo'];
            $arr[$fieldMarksObt] = $row['rollNo'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['passYear'];
            $arr[$fieldMarksObt] = $row['passYear'];
            $fieldMarksObt = $examsLevel[$row['examLevel']]['examNature'];
            $arr[$fieldMarksObt] = $row['examNature'];
        }
        return $arr;
    }

    public function majorWiseResultOverAllAction() {
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
                $data['results'] = $oUGTResultModel->findByOfferIdAndByMajorIdOverAll($post['offerId'], $post['majorId']);
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
        $this->render('majorWiseResultOverAll', $data);
    }

}
