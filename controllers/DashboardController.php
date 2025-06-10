<?php

namespace controllers;

/**
 * Description of DashboardController
 *
 * @author SystemAnalyst
 */
class DashboardController extends StateController {

    public function indexAction() {
        $this->render('dashboard');
    }

    public function duplicatedAction() {
        $this->render('duplicated');
    }

    public function internationalInfoAction() {
        $baseId = 57;
        $userId = $this->state()->get('userInfo')['userId'];
        $oApplicationsModel = new \models\ApplicationsModel();
        $internationalApplications = $oApplicationsModel->byUserIdAndBaseId($userId, $baseId);
        if ($internationalApplications) {
            $oInternationalInfoModel = new \models\InternationalInfoModel();
            $data['internationalInfo'] = $oInternationalInfoModel->findOneByField('userId', $userId);
            $this->render('internationalInfo', $data);
        } else {
            $data['message'] = 'Dear Applicant! This is only for International Admission Base.';
            $this->render('internationalInfo', $data);
        }
    }

    public function testCentreAction() {
        $offerId = $this->state()->get('userInfo')['offerId'];
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $data['testCentre'] = $oAdmissionOfferModel->findByPK($offerId, 'testCity');
        if ($data['testCentre']['testCity'] == 'YES') {

            $oMetaDataModel = new \models\MetaDataModel();
            $data['testCities'] = $oMetaDataModel->byKeyValue('testCentre');
//            echo "<pre>"; print_r($data['cities']);exit;
            $userId = $this->state()->get('userInfo')['userId'];
            $oUserModel = new \models\UsersModel();
            $data['userInfo'] = $oUserModel->findByPK($userId);
            $this->render('testCentre', $data);
        } else {
            die('Not Applicable');
        }
    }

    public function testStreamAction() {
        die('Not Applicable');
        $offerId = $this->state()->get('userInfo')['offerId'];
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $data['testStream'] = $oAdmissionOfferModel->findByPK($offerId, 'testStream');
        if ($data['testStream']['testStream'] == 'YES') {
            $userId = $this->state()->get('userInfo')['userId'];
            $oUserModel = new \models\UsersModel();
            $data['userInfo'] = $oUserModel->findByPK($userId);
            $this->render('testStream', $data);
        } else {
            die('Not Applicable');
        }
    }

    public function validate() {
        $userData = $this->state()->get('userInfo');
//        var_dump($userData);exit;
        $oUserModel = new \models\UsersModel();
        $data['userInfo'] = $oUserModel->findByPK($userData['userId']);
        $oEducationModel = new \models\EducationModel();
        $data['eduId'] = $oEducationModel->preReq($userData['userId'], $userData['preReq']);
//        var_dump($data['userInfo']['picture']);exit;
        if (empty($data['userInfo']['picture'])) {
            $this->redirect(SITE_URL . 'dashboard/applicationStatus');
            exit;
//        } else if (empty($data['userInfo']['religion']) && ($userData['cCode']) != 100) {
        } else if (empty($data['userInfo']['religion']) && ($userData['cCode']) != 100) {
            $this->redirect(SITE_URL . 'dashboard/applicationStatus');
            exit;
        } else if (empty($data['eduId'])) {
            $this->redirect(SITE_URL . 'dashboard/applicationStatus');
            exit;
        }
    }

    public function applicationStatusAction() {
        $userData = $this->state()->get('userInfo');
        $data['userState'] = $userData;
        $oUserModel = new \models\UsersModel();
        $data['user'] = $oUserModel->findByPK($userData['userId']);
        $preReq1 = $userData['preReq'];
        $preReq2 = $userData['preReq1'];
        $oEducationModel = new \models\EducationModel();
        $data['eduDiff'] = $oEducationModel->preReqDiff($userData['userId'], $preReq1, $preReq2);
//        print_r($data);exit;
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['applications'] = $oApplicationsModel->byUserId($userData['userId'], $userData['offerId']);
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        foreach ($data['applications'] as $key => $row) {
//            print_r($row['offerId']);
            $preReq = $oAdmissionOfferModel->findByPK($row['offerId'], 'preReq');
            $eduId = $oEducationModel->preReqDiff($userData['userId'], $preReq['preReq']);
            $data['applications'][$key]['baseTypeDet'] = $eduId;
//            var_dump($eduId);exit;
        }
//            print_r($data);
//            exit;
        $this->render('applicationStatus', $data);
    }

    public function loadKinshipDetailAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oKinshipDetailModel = new \models\KinshipDetailModel();
        if ($this->post()->exists('kinId')) {
            $kinshipDetailData = $oKinshipDetailModel->findByPKAndUserId($this->post()->kinId, $userId);
        }
        $this->printAndDieJsonResponse(($kinshipDetailData ? true : false), $kinshipDetailData);
    }

    public function loadEducationAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oEducationModel = new \models\EducationModel();
        if ($this->post()->exists('eduId')) {
            $eduData = $oEducationModel->findByPKAndUserId($this->post()->eduId, $userId);
            $oBoardModel = new \models\BoardModel();
            $eduData['boards'] = $oBoardModel->getBoards($eduData['examLevel']);
            $oExamLevelClassModel = new \models\ExamLevelClassModel();
            $eduData['examClasses'] = $oExamLevelClassModel->getClassesByExam($eduData['examLevel']);

            $oOALevelModel = new \models\OalevelModel();
            $eduData['oaLevel'] = $oOALevelModel->getByUserIdAndExamLevel($userId, $eduData['examClass']);
        }
        $this->printAndDieJsonResponse(($eduData ? true : false), $eduData);
    }

    public function loadProfessionAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oProfessionInfoModel = new \models\ProfessionInfoModel();
        if ($this->post()->exists('id')) {
            $professionData = $oProfessionInfoModel->findByPKAndUserId($this->post()->id, $userId);
        }
        $this->printAndDieJsonResponse(($professionData ? true : false), $professionData);
    }

    public function loadPublicationAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oPublicationsModel = new \models\PublicationsModel();
        if ($this->post()->exists('id')) {
            $pubData = $oPublicationsModel->findByPKAndUserId($this->post()->id, $userId);
        }
        $this->printAndDieJsonResponse(($pubData ? true : false), $pubData);
    }

    public function loadResearchWorkAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oResearchWorkModel = new \models\ResearchWorkModel();
        if ($this->post()->exists('id')) {
            $researchData = $oResearchWorkModel->findByPKAndUserId($this->post()->id, $userId);
        }
        $this->printAndDieJsonResponse(($researchData ? true : false), $researchData);
    }

    public function loadReferencesAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oAcademicReferencesModel = new \models\AcademicReferencesModel();
        if ($this->post()->exists('id')) {
            $refData = $oAcademicReferencesModel->findByPKAndUserId($this->post()->id, $userId);
        }
//        var_dump($refData); exit;
        $this->printAndDieJsonResponse(($refData ? true : false), $refData);
    }

    private function postUserPic() {
        $userData = $this->state()->get('userInfo');
        $userId = $userData['userId'];
        /* Picture upload code */
        $bucketId = \helpers\Common::generateBucket();
        $target_dir = UPLOAD_PATH . $bucketId . '/';
        $target_file = $target_dir . basename($_FILES["uimg"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileName = 'i' . md5($userId) . '.' . $imageFileType;
        $target_file = $target_dir . $fileName;
// Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["uimg"]["tmp_name"]);
        if ($check === false) {
            return $this->getJsonResponse(false, ['msg' => 'File is not an image.']);
        } else if ($_FILES["uimg"]["size"] > 35000) {// Check file size 50kb
            return $this->getJsonResponse(false, ['msg' => 'Sorry, your file is too large.']);
        }
// Allow certain file formats
        else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return $this->getJsonResponse(false, ['msg' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $imageFileType]);
        } else if (move_uploaded_file($_FILES["uimg"]["tmp_name"], $target_file)) {
            $oPostArray['picture'] = $fileName;
            $oPostArray['picBucket'] = $bucketId;
            $oPostArray['picExt'] = $imageFileType;
            $oPostArray['updatedOn'] = date("Y-m-d H:i:s");
            $oUserModel = new \models\UsersModel();
            if ($oUserModel->saveImage($userData, $oPostArray, $bucketId, $fileName)) {
                $userData['picBucket'] = $bucketId;
                $userData['picture'] = $fileName;
                $this->state()->set('userInfo', $userData);
                return $this->getJsonResponse(true, ['msg' => 'Image uploaded successfully.']);
            } else {
                return $this->getJsonResponse(false, ['msg' => "Sorry, there was an internal error uploading your file. Please try again."]);
            }
        } else {
            return $this->getJsonResponse(false, ['msg' => "Sorry, there was an error uploading your file. Please try again."]);
        }
    }

    public function profileAction() {
        if (!empty($_FILES)) {
            $data['picStatus'] = $this->postUserPic();
            $filesData = json_decode($data['picStatus']);
            if ($filesData->status) {
                $this->redirect(SITE_URL . 'dashboard/profile');
            }
        }
        $userId = $this->state()->get('userInfo')['userId'];
        $cCode = $this->state()->get('userInfo')['cCode'];
        $oFieldsModel = new \models\FieldsModel();
        if (!empty($cCode)) {
            $data['fields'] = $oFieldsModel->getFields(FRM_PROFILE, $cCode);
        }
        $oUserModel = new \models\UsersModel();
        $data['userInfo'] = $oUserModel->findByPK($userId);

        $oCityModel = new \models\CityModel();
        $data['cities'] = $oCityModel->getCities('cityID,cityName');

        $oCountriesModel = new \models\CountriesModel();
        $data['countries'] = $oCountriesModel->findAll('countryId,name');

        $oOccupationsModel = new \models\OccupationsModel();
        $data['occupations'] = $oOccupationsModel->findAll('occupName, occupName');

        $oParentQualificationModel = new \models\ParentQualificationModel();
        $data['qualifications'] = $oParentQualificationModel->findAll();

        $oProvinceModel = new \models\ProvinceModel();
        $data['provinces'] = $oProvinceModel->findAll('provinceId,provinceName');

        $oDistrictModel = new \models\DistrictModel();
        $data['districts'] = $oDistrictModel->findAll('distId,distnm');

        $oTehsilModel = new \models\TehsilModel();
        $data['tehsils'] = $oTehsilModel->findAll('tehId,tehnm');

//        $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
//        $offerIds = explode(",", $streamOfferIds);
//
//        $oApplicationsModel = new \models\ApplicationsModel();
//        $data['testStreamMajId'] = $oApplicationsModel->findTestStreambyUserIdAndOfferIds($userId, $offerIds);
//        if ($cCode == 21) {
//            if (empty($data['testStreamMajId'])) {
//                die('NOT APPLICABLE');
//            }
//        }

        $this->render('profile', $data);
    }

    public function professionAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oProfessionInfoModel = new \models\ProfessionInfoModel();
        $data['professionInfo'] = $oProfessionInfoModel->byUserId($userId);
        $this->render('professionalInformation', $data);
    }

    public function researchObjectiveAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oResearchObjectiveModel = new \models\ResearchObjectiveModel();
        $data['researchObjective'] = $oResearchObjectiveModel->findOneByField('userId', $userId);
//        print_r($data); exit;
        $this->render('researchObjective', $data);
    }

    public function publicationsAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oPublicationsModel = new \models\PublicationsModel();
        $data['publications'] = $oPublicationsModel->findByField('userId', $userId);
        $this->render('publications', $data);
    }

    public function researchWorkAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oResearchWorkModel = new \models\ResearchWorkModel();
        $data['researchWorks'] = $oResearchWorkModel->findByField('userId', $userId);
        $this->render('researchWork', $data);
    }

    public function academicReferenceAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oAcademicReferencesModel = new \models\AcademicReferencesModel();
        $data['academicReferences'] = $oAcademicReferencesModel->findByField('userId', $userId);
        $this->render('academicReference', $data);
    }

    public function kinshipDetailAction() {
//        $baseId = 15;
        $offerId = $this->state()->get('userInfo')['offerId'];
        $userId = $this->state()->get('userInfo')['userId'];
        $oApplicationsModel = new \models\ApplicationsModel();
//        $kinshipApplications = $oApplicationsModel->byUserIdAndOfferIdAndBaseId($userId, $offerId, $baseId);
        $kinshipApplications = $oApplicationsModel->kinshipbyUserIdAndOfferId($userId, $offerId);
        if ($kinshipApplications) {
            $oKinshipDetailModel = new \models\KinshipDetailModel();
            $data['kinshipDetails'] = $oKinshipDetailModel->byUserId($userId);
            $this->render('kinshipDetail', $data);
        } else {
            $data['message'] = 'Dear Applicant! This is only for those applicants who wish to apply on Kinship Admssion Base. First apply on kinship base then fill this form with thanks.';
            $this->render('kinshipDetail', $data);
        }
    }

    public function testInfoAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oTestInfoModel = new \models\TestInfoModel();
        $data['testInfo'] = $oTestInfoModel->byUserId($userId);
        $this->render('testInfo', $data);
    }

    public function overseasDetailAction() {
//        $baseId = 15;
        $userId = $this->state()->get('userInfo')['userId'];
        $oApplicationsModel = new \models\ApplicationsModel();
        $overseasApplications = $oApplicationsModel->overseasApplicationsbyUserId($userId);
        $oCountriesModel = new \models\CountriesModel();
        $data['countries'] = $oCountriesModel->overseasCountries();
        if ($overseasApplications) {
            $oOverseasModel = new \models\OverseasModel();
            $data['overseasDetail'] = $oOverseasModel->byUserId($userId);
            $this->render('overseasDetail', $data);
        } else {
            $data['message'] = 'Dear Applicant! This is only for those applicants whose Admission Base was Overseas';
            $this->render('overseasDetail', $data);
        }
    }

    public function academicsAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $preReq = $this->state()->get('userInfo')['preReq'];
        $preReq1 = $this->state()->get('userInfo')['preReq1'];
        $offerId = $this->state()->get('userInfo')['offerId'];
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        if (!empty($offerId)) {
            $offerData = $oAdmissionOfferModel->findByPK($offerId, 'cCode, studyLevel');
        }
//          var_dump($preReq1);exit;
//        $cCode = $this->state()->get('userInfo')['cCode'];
//        if (empty($preReq)) {
//            $this->redirect(SITE_URL);
//        }

        $oEducationModel = new \models\EducationModel();
        $data['educations'] = $oEducationModel->byUserId($userId);
        foreach ($data ['educations'] as $key => $value) {
            $value['examLevelLabel'] = \helpers\Common::getClassById($value['examLevel']);
            $data['educations'][$key] = $value;
        }
        $data['examLevels'] = \helpers\Common::getExamLevel();
//        $data['examLevels'] = \helpers\Common::getExamLevelByPreReq($preReq);
//        if ($cCode == 4 || $cCode == 50) {
//            $data['examLevels'] = \helpers\Common::getExamLevelByTwoPreReq($preReq, $preReq1);
//        }

        $oOalevelModel = new \models\OalevelModel();
        $data['oalevel']['compulsory'] = $oOalevelModel->getOLevelCompulsorySubjects();
        $data['oalevel']['gradesList'] = $oOalevelModel->getGradesList();

        if ($offerData['studyLevel'] == 2) {
            $this->render('interAcademics', $data);
//            $this->render('academics', $data);
        } else if ($offerData['studyLevel'] == 3) {
//            $this->render('academics', $data);
            $this->render('bsAcademics', $data);
        } else if ($offerData['studyLevel'] == 100 || $offerData['studyLevel'] == 6) {
            $this->render('academics', $data);
        } else {
            $this->render('academics', $data);
        }
    }

    public function applyAction() {

        $this->validate();
        $offerId = $this->state()->get('userInfo')['offerId'];

        if ($offerId == 1118) {
//            if (!empty($this->state()->get('userInfo')['testStreamOfferIds'])) {
            $this->applyTestStream();
//            }
        } else {
            $cCode = $this->state()->get('userInfo')['cCode'];
            if ($cCode == 221) {
                die('NOT APPLICABLE');
            }
            $userId = $this->state()->get('userInfo')['userId'];
//        if ($cCode == 50 || $cCode == 4) {
//            $oGatResultModel = new \models\gatResultModel();
//            if (empty($oGatResultModel->getPassResultByUserId($userId))) {
//                $data['msg'] = "You are not eligible to apply.";
//            }
//        }

            $obj = new \mihaka\formats\MihakaPDF();
            if ($cCode == 50 || $cCode == 4) {
                $oResearchObjectiveModel = new \models\ResearchObjectiveModel();
                $data['researchObjective'] = $oResearchObjectiveModel->findOneByField('userId', $userId);
                if ($loggedInUserId['userId'] != 1) {
                    if (empty($data['researchObjective'])) {
//                        $obj->setHTML('<h1>Your research information is missing in MS/MPhil Admission Form. Please enter the relevant information to complete your application. Otherwise, your form will be incomplete and cannot be submitted.</h1>');
//                        $obj->browse();
//                        exit;
                        $this->redirect(SITE_URL . 'dashboard/researchObjective');
                        exit;
                    }
                }

                $oAcademicReferenceModel = new \models\AcademicReferencesModel();
                $data['references'] = $oAcademicReferenceModel->findByField('userId', $userId);
                if ($loggedInUserId['userId'] != 1) {
                    if (empty($data['references']) || count($data['references']) < 2) {
//                        $obj->setHTML('<h1>Please Add two References to proceed from MS Menu.</h1>');
//                        $obj->browse();
//                        exit;
                        $this->redirect(SITE_URL . 'dashboard/academicReference');
                        exit;
                    }
                }
            }

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

//        if ( $offerId == 74 || $offerId == 65 || $offerId == 66 || $offerId == 67 || $offerId == 68 || $offerId == 70 || $offerId == 71 || $offerId == 75 || $offerId == 77) {
//            $versionId=1;
//            $oApplicationsModel = new \models\ApplicationsModel();
//            $appliedMajors = $oApplicationsModel->byUserIdAndOfferIdAndVersion($userId, $offerId, $versionId);
//
//            foreach ($data['majors'] as $key => $rowParent) {
//                if (in_array($rowParent['majId'], $appliedMajors)) {
//                    unset($data['majors'][$key]);
//                }
//            }
//        }
//        print_r($result); exit();
//        $data['majors'] = $oMajorsModel->findByField('cCode', $cCode, 'majId,name');
            $oSubjectCombinationModel = new \models\SubjectCombinationModel();
            $data['sets'] = $oSubjectCombinationModel->findByField('cCode', $cCode, 'gCode', 5, 'setNo,sub1');
            $data['offerId'] = $offerId;
            $oFieldsModel = new \models\FieldsModel();
            $data['fields'] = $oFieldsModel->getFields(FRM_APPLY, $cCode);
            $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
            $data['activeClassCode'] = $oAdmmissionOfferModel->getCurrentOpenings();

            $data['streamStatus'] = $oAdmmissionOfferModel->findByPK($offerId, 'testStream');
            $oOalevelModel = new \models\OalevelModel();
            $data['oalevel']['compulsory'] = $oOalevelModel->getOLevelCompulsorySubjects();
            $data['oalevel']['gradesList'] = $oOalevelModel->getGradesList();
//        print_r($offerId); exit;

            $oAdmissionOfferModel = new \models\AdmissionOfferModel();
            $data['offerData'] = $oAdmissionOfferModel->findByPK($offerId, 'multipleSubCtgry');

            if ($data['offerData']['multipleSubCtgry'] == 'YES') {

//                print_r($data['offerData']['multipleSubCtgry']);
//                exit;
                $this->render('applyMultiSubCtgry', $data);
            } else {
                $this->render('apply', $data);
            }
        }
    }

    public function applyTestStream() {
        $userId = $this->state()->get('userInfo')['userId'];

        $offerId = $this->state()->get('userInfo')['offerId'];
        $cCode = $this->state()->get('userInfo')['cCode'];

        $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
        $offerIds = explode(",", $streamOfferIds);

        $oApplicationsModel = new \models\ApplicationsModel();
        $data['bases'] = $oApplicationsModel->findAllBasesbyUserIdAndOfferIds($userId, $offerIds);
        $data['testStreamMajId'] = $oApplicationsModel->findTestStreambyUserIdAndOfferIds($userId, $offerIds);

        $oGatResultModel = new \models\gatResultModel();
        $data['gatResult'] = $oGatResultModel->getResultByOfferIdsAndUserId($offerIds, $userId);

        $oMajorStreamModel = new \models\MajorStreamModel();
        $data['majors'] = $oMajorStreamModel->getMajorsByOfferIdAndTestStream($offerId, $data['testStreamMajId'], $userId);

        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $data['sets'] = $oSubjectCombinationModel->findByField('cCode', $cCode, 'gCode', 5, 'setNo,sub1');
        $data['offerId'] = $offerId;

        $oFieldsModel = new \models\FieldsModel();
        $data['fields'] = $oFieldsModel->getFields(FRM_APPLY, $cCode);
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $data['activeClassCode'] = $oAdmmissionOfferModel->getCurrentOpenings();

        if (!empty($data['testStreamMajId'])) {
            $this->render('applyNew', $data);
        } else {
            die('Not Applicable');
        }
    }

    public function applyExtensionAction() {

        $userId = $this->state()->get('userInfo')['userId'];
        $oUserAdmissionOfferModel = new \models\userAdmissionOfferModel();
//        $extensionId = \mihaka\helpers\MString::decrypt($this->get()->id);
        $extensionId = $this->get()->id;
        $extensionData = $oUserAdmissionOfferModel->findByUserIdAndExtId($userId, $extensionId);
        if (empty($extensionData)) {
            die('die');
        }

        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmmissionOfferModel->findByPK($extensionData['offerId']);
        $offerData['endDate'] = $extensionData['endDate'];
        $this->state()->pushTo('userInfo', $offerData);
        $this->validate();

        $offerId = $this->state()->get('userInfo')['offerId'];
        $cCode = $this->state()->get('userInfo')['cCode'];
        if ($offerId == 118) {
            $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
            $offerIds = explode(",", $streamOfferIds);

            $oApplicationsModel = new \models\ApplicationsModel();
            $data['bases'] = $oApplicationsModel->findAllBasesbyUserIdAndOfferIds($userId, $offerIds);
            $data['testStreamMajId'] = $oApplicationsModel->findTestStreambyUserIdAndOfferIds($userId, $offerIds);

            $oGatResultModel = new \models\gatResultModel();
            $data['gatResult'] = $oGatResultModel->getResultByOfferIdsAndUserId($offerIds, $userId);

            $oMajorStreamModel = new \models\MajorStreamModel();
            $data['majors'] = $oMajorStreamModel->getMajorsByOfferIdAndTestStream($offerId, $data['testStreamMajId'], $userId);
        } else {
            $oBaseClass = new \models\BaseClassModel();
            $data['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($cCode);
            $oMajorsModel = new \models\MajorsModel();
            $data['majors'] = $oMajorsModel->getMajorByOfferIdClassId($offerId, $cCode, $userId);
        }
        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $data['sets'] = $oSubjectCombinationModel->findByField('cCode', $cCode, 'gCode', 5, 'setNo,sub1');

        $data['offerId'] = $offerId;
//        print_r($offerId);exit;
        $oFieldsModel = new \models\FieldsModel();
        $data['fields'] = $oFieldsModel->getFields(FRM_APPLY, $cCode);
        $data['activeClassCode'][0] = $oAdmmissionOfferModel->findByPK($offerId);
        if ($data['offerId'] == 118) {
            $this->render('applyNew', $data);
        } else {
            $this->render('apply', $data);
        }
    }
}
