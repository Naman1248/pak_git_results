<?php

/**
 * Description of MyApplicationsController
 *
 * @author SystemAnalyst
 */

namespace controllers;

class MyApplicationsController extends StateController {

    private $forms = [
        '100' => ['form' => 'gatForm'],
        '81' => ['form' => 'diplomaForm'],
        '59' => ['form' => 'diplomaForm'],
        '78' => ['form' => 'diplomaForm'],
        '26' => ['form' => 'diplomaForm'],
        '50' => ['form' => 'MSForm', 'undertakings' => ['include/MS/undertakingMS1', 'include/MS/undertakingMS2']],
        '4' => ['form' => 'MSForm', 'undertakings' => ['include/MS/undertakingMS1', 'include/MS/undertakingMS2']],
        '29' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '39' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '55' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '70' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '71' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '72' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '73' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '75' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '76' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '37' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '40' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '21' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '27' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '36' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '48' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '2' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '11' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '20' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '49' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '58' => ['form' => 'bachelorForm', 'undertakings' => ['include/underGraduate/undertakingBachelor1', 'include/underGraduate/undertakingBachelor2']],
        '1' => ['form' => 'intermediateForm', 'undertakings' => ['include/intermediate/undertakingIntermediate1', 'include/intermediate/undertakingIntermediate2']]
    ];
//    private $specialForms = ['16' => ['sportsForm','include/specialCategory/sportsUndertaking'],
    private $specialForms = [
        '1' => ['specialCategoryForm'],
        '3' => ['specialCategoryForm'],
        '5' => ['specialCategoryForm'],
        '6' => ['specialCategoryForm'],
        '7' => ['specialCategoryForm'],
        '11' => ['specialCategoryForm'],
        '13' => ['specialCategoryForm'],
        '15' => ['kinshipForm'],
        '16' => ['sportsForm', 'include/specialCategory/sportsUndertaking'],
        '17' => ['cocurricularForm', 'include/specialCategory/cocurricularUndertaking'],
        '18' => ['specialCategoryForm'],
        '20' => ['specialCategoryForm'],
        '36' => ['specialCategoryForm'],
        '37' => ['specialCategoryForm'],
        '41' => ['specialCategoryForm']
    ];
    private $challan = [
        '100' => 'gatChallanForm',
        '50' => 'bachelorChallanForm',
        '4' => 'bachelorChallanForm',
        '21' => 'bachelorChallanForm',
        '1' => 'bachelorChallanForm',
        '40' => 'bachelorChallanForm',
        '48' => 'bachelorChallanForm',
        '2' => 'bachelorChallanForm',
        '26' => 'bachelorChallanForm',
        '27' => 'bachelorChallanForm',
        '36' => 'bachelorChallanForm',
        '29' => 'bachelorChallanForm',
        '37' => 'bachelorChallanForm',
        '39' => 'bachelorChallanForm',
        '55' => 'bachelorChallanForm',
        '70' => 'bachelorChallanForm',
        '71' => 'bachelorChallanForm',
        '72' => 'bachelorChallanForm',
        '73' => 'bachelorChallanForm',
        '75' => 'bachelorChallanForm',
        '76' => 'bachelorChallanForm',
        '78' => 'bachelorChallanForm',
        '11' => 'bachelorChallanForm',
        '20' => 'bachelorChallanForm',
        '49' => 'bachelorChallanForm',
        '58' => 'bachelorChallanForm',
        '81' => 'bachelorChallanForm',
        '59' => 'bachelorChallanForm'
    ];

//    private function getFormViewName($cCode) {
//        return $this->forms[$cCode]['form'];
//    }
    private function getFormViewName($cCode) {
        $oFormsAndChallansModel = new \models\FormsAndChallansModel();
        $formName = $oFormsAndChallansModel->findByPK($cCode, 'forms');
        return ($formName['forms']);
    }

    private function getUndertakingViewName($cCode) {
        $oFormsAndChallansModel = new \models\FormsAndChallansModel();
        $undertakingsName = $oFormsAndChallansModel->findByPK($cCode, 'undertakings');
        return ($undertakingsName['undertakings']);
    }

//    private function getUndertakingViewName($cCode) {
//        return $this->forms[$cCode]['undertakings'];
//    }

    private function getChallanViewName($cCode) {
        $oFormsAndChallansModel = new \models\FormsAndChallansModel();
        $challanName = $oFormsAndChallansModel->findByPK($cCode, 'challan');
        return ($challanName['challan']);
    }

    private function getSpecialFormViewName($baseId) {
        $oSpecialFormsModel = new \models\specialFormsModel();
        $specialForms = $oSpecialFormsModel->specialFormsByBaseId($baseId);
//        var_dump($specialForms['forms']);exit;
        return ($specialForms['forms']);
    }

//    private function getChallanViewName($cCode) {
//        return $this->challan[$cCode];
//    }

    public function indexAction() {
        if (!empty($_FILES)) {
            $postImgData = $this->post()->all();
            $data['picStatus'] = $this->postChallanPic($postImgData['appId']);
        }
        $userData = $this->state()->get('userInfo');
        $oApplications = new \models\ApplicationsModel();
        $data['applications'] = $oApplications->byUserId($userData['userId'], $userData['offerId']);
//        print_r($data['applications']);exit;
        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        foreach ($data['applications'] as $key => $row) {
            if (!empty($row['childBase'])) {
                $data['applications'][$key]['childBase'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($row['cCode'], $row['childBase'], $row['majId'], $userData['gender'], $row['baseId']);
            } else {
                $data['applications'][$key]['childBase'] = [];
            }
        }
        $this->render('index', $data);
    }

    private function validate($data) {
        $obj = new \mihaka\formats\MihakaPDF();
        if (empty($data['userInfo']['picture'])) {
            $obj->setHTML('<h1>Please upload your picture to print form.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['userInfo']['religion']) && ($data['application']['cCode']) != 100) {
            $obj->setHTML('<h1>Please update your profile information.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['eduId'])) {
            $obj->setHTML('<h1>Please complete your academic record.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['application']['picExt']) && ($data['application']['isPaid'] == 'N')) {
            $obj->setHTML('<h1>Please upload your paid challan</h1> .');
            $obj->browse();
            exit;
        }
    }

    public function printFormAction() {
        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        $data['userImage'] = USER_IMG;
        $oUserModel = new \models\UsersModel();
        $data['userInfo'] = $oUserModel->findByPK($userData['userId']);
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['application'] = $oApplicationsModel->findByPK($get['appId'], 'offerId,majId,formNo,cCode,baseId,setNo,picExt,childBase,baseTypeDet,picExt, isPaid');
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $preReq = $oAdmissionOfferModel->findByPK($data['application']['offerId'], 'preReq, endDate');
//        $preReq = $this->state()->get('userInfo')['preReq'];
//        $data['eduId'] = $oApplicationsModel->applicationPrerequisite($userData['userId'], $preReq);
        $oEducationModel = new \models\EducationModel();
        $data['eduId'] = $oEducationModel->preReq($userData['userId'], $preReq['preReq']);
        $loggedInUserId = $this->state()->get('userInfo');
        $this->validate($data);
        $data['education'] = $oEducationModel->getLast($userData['userId']);
        $data['educations'] = $oEducationModel->byUserId($userData['userId']);
//        print_r($data['educations']); exit;
        foreach ($data['educations'] as $key => $row) {
            $data['educations'][$key]['examLevel'] = \helpers\Common::getClassById($row['examLevel']);
        }
        $data['education']['examLevel'] = \helpers\Common::getClassById($data['education']['examLevel']);
//        print_r($data['education']); exit;

        if (!empty($data['application']['baseTypeDet'])) {
            $det = (explode(",", $data['application']['baseTypeDet']));
            foreach ($det as $det1) {
                $det11[$det1] = \helpers\Common::getBaseTypeDetailById($det1);
            }
            $data['performances'] = $det11;
        }
        $data['examLevel'] = $data['education']['examLevel'];
        $data['performance'] = \helpers\Common::getBaseTypeDetailById(1);
//        var_dump($data['userInfo']);die();
        $oProvinceModel = new \models\ProvinceModel();
        if (!empty($data['userInfo']['provinceId'])) {

            $data['province'] = $oProvinceModel->findByPK($data['userInfo']['provinceId'], 'provinceName');
        }

        //      var_dump($data['userInfo']['countryID']); 
//        exit;

        $oCountriesModel = new \models\CountriesModel();
        if (!empty($data['userInfo']['countryID'])) {
            $data['country'] = $oCountriesModel->findByPK($data['userInfo']['countryID'], 'name');
        }

        if (!empty($data['userInfo']['fatherNationality'])) {
            $data['faCountry'] = $oCountriesModel->findByPK($data['userInfo']['fatherNationality'], 'name');
        }

        if (!empty($data['userInfo']['motherNationality'])) {
            $data['moCountry'] = $oCountriesModel->findByPK($data['userInfo']['motherNationality'], 'name');
        }

        if (!empty($data['userInfo']['districtId'])) {
            $oDistrictModel = new \models\DistrictModel();
            $data['district'] = $oDistrictModel->findByPK($data['userInfo']['districtId'], 'distnm');
        }

        $oThsilModel = new \models\TehsilModel();

        if (!empty($data['userInfo']['tehsilId'])) {

            $data['tehsil'] = $oThsilModel->findByPk($data['userInfo']['tehsilId'], 'tehNm');
        }
//        print_r($data); exit;
        $oCityModel = new \models\CityModel();
        $data['city'] = $oCityModel->findByPK($data['userInfo']['cityId'], 'cityName');

        $oMajors = new \models\MajorsModel();
        $data['major'] = $oMajors->getMajorByOfferIdClassIdAndMajorId($data['application']['offerId'], $data['application']['cCode'], $data['application']['majId']);
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $data['admissionOffer'] = $oAdmissionOffer->findByPk($data['application']['offerId'], 'year,className, endYear');

        $ouserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
        $extensionData = $ouserAdmissionOfferModel->exist($userData['userId'], $data['application']['offerId']);
        $data['admissionOffer']['endDate'] = $data['major']['endDate'];
        if (!empty($extensionData)) {
            $data['admissionOffer']['endDate'] = $extensionData['endDate'];
        }

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $data['base'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['baseId'], $data['application']['majId'], $userData['gender']);

        if ($data['application']['baseId'] == 15) {
            $oKinship = new \models\KinshipModel();
            $data['kinship'] = $oKinship->findByPK($userData['userId']);
            $oKinshipDetailModel = new \models\KinshipDetailModel();
            $data['kindDetail'] = $oKinshipDetailModel->findByField('userId', $userData['userId']);
        }

        if ($data['application']['baseId'] == 13) {
            $oEmployeeBaseModel = new \models\employeeBaseModel();
            $data['employeeBase'] = $oEmployeeBaseModel->findByPK($userData['userId']);
        }

        if ($data['application']['baseId'] == 11) {
            $oOverseasModel = new \models\OverseasModel();
            $data['overseas'] = $oOverseasModel->findByPK($userData['userId']);
        }
//        if ($data['application']['baseId'] == 1 || $data['application']['baseId'] == 20) {
        $oOalevelModel = new \models\OalevelModel();
        $oalevel = $oOalevelModel->getByUserId($userData['userId']);
        if (!empty($oalevel)) {
            $data['oalevel'] = $oalevel;
        }
//        }
        if (!empty($data['application']['childBase'])) {
            $data['childBase'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['childBase'], $data['application']['majId'], $userData['gender'], $data['application']['baseId']);
        } else {
            $data['childBase'] = [];
        }

        //NEW CODE
        $obj = new \mihaka\formats\MihakaPDF();

        if ($data['application']['cCode'] == 8) {

            $oTestInfoModel = new \models\TestInfoModel();
            $data['testInfo'] = $oTestInfoModel->findByField('userId', $userData['userId']);
//                if (empty($data['gatResult'])) {
//                    $obj->setHTML('<h1>You are not eligible to apply because of GAT Result.</h1>');
//                    $obj->browse();
//                    exit;
//                }
        }
        if ($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4 || $data['application']['cCode'] == 8) {
//        if (($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4) && ($data['application']['baseId'] == 9)){
//            if ($data['application']['majId'] != 80 || $data['application']['majId'] != 132) {
//                $oGatResultModel = new \models\gatResultModel();
//                $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userData['userId'], $data['application']['majId']);
//                if (empty($data['gatResult'])) {
//                    $obj->setHTML('<h1>You are not eligible to apply because of GAT Result.</h1>');
//                    $obj->browse();
//                    exit;
//                }
//            }


            $oPublicationsModel = new \models\PublicationsModel();
            $data['publications'] = $oPublicationsModel->findByField('userId', $userData['userId']);

            $oProfessionInfoModel = new \models\ProfessionInfoModel();
            $data['profession'] = $oProfessionInfoModel->findOneByField('userId', $userData['userId']);
//   
            $oResearchWorkModel = new \models\ResearchWorkModel();
            $data['researchWorks'] = $oResearchWorkModel->findByField('userId', $userData['userId']);

            $oResearchObjectiveModel = new \models\ResearchObjectiveModel();
            $data['researchObjective'] = $oResearchObjectiveModel->findOneByField('userId', $userData['userId']);
            if ($loggedInUserId['userId'] != 1) {
                if (empty($data['researchObjective'])) {
                    $obj->setHTML('<h1>Please Provide Research Objective Information to proceede.</h1>');
                    $obj->browse();
                    exit;
                }
            }

            $oAcademicReferenceModel = new \models\AcademicReferencesModel();
            $data['references'] = $oAcademicReferenceModel->findByField('userId', $userData['userId']);
            if ($loggedInUserId['userId'] != 1) {
                if (empty($data['references']) || count($data['references']) < 2) {
                    $obj->setHTML('<h1>Please Add two References to proceede.</h1>');
                    $obj->browse();
                    exit;
                }
            }
//                 var_dump($data['gatResult']);die();
        }

        if ($data['application']['cCode'] == 19 && $data['application']['majId'] == 27) {

            if (($data['userInfo']['experience'] == 'NA' || empty($data['userInfo']['experience'])) && ($data['application']['cCode']) == 19 && $data['application']['majId'] == 27) {
                $obj->setHTML('<h1>Please update Applicant Profile Information for experience.</h1>');
                $obj->browse();
                exit;
            }
        }
        //NEW CODE
        if (!empty($data['application']['setNo'])) {
            $oSubjectCombination = new \models\SubjectCombinationModel();
            $data['subjects'] = $oSubjectCombination->getSubjectsByClassAndMajorAndSetNo($data['application']['cCode'], $data['application']['majId'], $data['application']['setNo']);
        }
//        echo $HTML = $this->getHTML($this->getFormViewName($data['application']['cCode']), $data);
        $HTML = $this->getHTML($this->getFormViewName($data['application']['cCode']), $data);
        //else 
        {
            $obj->setCSS(ASSET_URL . 'ss/pdfForm');
//            $obj->setHTML($HTML);
            $i = 0;
            $obj->setFooter("<table><tr><td>Reference#: " . $data['userInfo']['userId'] . " </td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
            $obj->getPDFObject()->SetHeader($obj->getHeader());
            $obj->getPDFObject()->SetFooter($obj->getFooter());
            $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
            $obj->getPDFObject()->WriteHTML($HTML, 2);

            $undertakingsStr = $this->getUndertakingViewName($data['application']['cCode']);
            $undertakings = explode(',', $undertakingsStr);

            if (!empty($undertakingsStr)) {
                foreach ($undertakings as $undertaking) {
                    $obj->addPage();
                    $HTML = $this->getHTML($undertaking, $data);
                    $obj->getPDFObject()->WriteHTML($HTML, 2);
                }
            }
            $specialForms = $this->getSpecialFormViewName($data['application']['baseId']);
            if (!empty($specialForms)) {
                $specialForms1 = explode(',', $specialForms);
                foreach ($specialForms1 as $specialForm) {
                    $obj->addPage();
                    $HTML = $this->getHTML($specialForm, $data);
                    $obj->getPDFObject()->WriteHTML($HTML, 2);
                }
            }
            $obj->getPDFObject()->output();
        }
        //$obj->setFooter('Printed Date : ' . date('d-m-Y'));
        //$obj->browse();
    }

    public function ugtSlipAction() {

        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }

        $obj = new \mihaka\formats\MihakaPDF();
        $ougtPlanModel = new \models\UGTPlanModel();
        $gatSlipData = $ougtPlanModel->findByField('userId', $userData['userId']);
//        print_r($gatSlipData);exit;
        if (empty($gatSlipData)) {
            $obj->setHTML('<h1>Your Test Schedule is Not Available.</h1>');
            $obj->browse();
            exit;
        }
        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/gatForm.css'), 1);
        foreach ($gatSlipData as $row) {
            $data['userInfo'] = $userData;
            $data['gatSlip'] = $row;
            $data['userImage'] = $userImage;
            $HTML = $this->getHTML('ugtPlan', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function ugtInterviewSlipAction() {
//        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }

        $obj = new \mihaka\formats\MihakaPDF();
        $ougtResultModel = new \models\UGTResultModel();
        //$gatSlipData = $ougtResultModel->interviewInfoByUserId($userData['userId']);
        $gatSlipData = $ougtResultModel->findByFieldData('userId', $userData['userId']);
//        echo '<pre>';
//        print_r($gatSlipData);exit;
        if (empty($gatSlipData)) {
            $obj->setHTML('<h1>Your Interview Schedule is Not Available.</h1>');
            $obj->browse();
            exit;
        }
        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/gatForm.css'), 1);
        foreach ($gatSlipData as $row) {
            $data['userInfo'] = $userData;
            $data['gatSlip'] = $row;
            $data['userImage'] = $userImage;
            $HTML = $this->getHTML('ugtInterviewSlip', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function ugtTrialSlipAction() {
//        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }

        $obj = new \mihaka\formats\MihakaPDF();
        $ougtResultModel = new \models\UGTResultModel();
        $gatSlipData = $ougtResultModel->findTrialByField($userData['userId']);
//        echo '<pre>';
//        print_r($gatSlipData);exit;
        if (empty($gatSlipData)) {
            $obj->setHTML('<h1>Your Trial Schedule is Not Available.</h1>');
            $obj->browse();
            exit;
        }
        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/gatForm.css'), 1);
        foreach ($gatSlipData as $row) {
            $data['userInfo'] = $userData;
            $data['gatSlip'] = $row;
            $data['userImage'] = $userImage;
            $HTML = $this->getHTML('ugtTrialSlip', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function gatSlipAction() {
//        die("expired");
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }
        $oGatSlipModel = new \models\GatSlipModel();
        $gatSlipData = $oGatSlipModel->findByField('userId', $userData['userId']);

        $obj = new \mihaka\formats\MihakaPDF();
        if (empty($gatSlipData)) {
            $obj->setHTML('<h1>Your Test Schedule is Not Available.</h1>');
            $obj->browse();
            exit;
        }
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $oMetaDataModel = new \models\MetaDataModel();
        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/gatForm.css'), 1);
        foreach ($gatSlipData as $row) {
            $data['userInfo'] = $userData;
            $data['gatSlip'] = $row;
            $data['userImage'] = $userImage;
            $data['className'] = $oAdmissionOfferModel->findByPK($row['offerId'], 'className');
            $data['cityName'] = $oMetaDataModel->nameByKeyValueAndByKeyId('testCentre', $row['cityId']);
            if ($row['cityId'] == 1) {
                $data['lahoreCentreName'] = ' GC University Lahore Campus';
            }
            $HTML = $this->getHTML('gatSlip', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function gatResultAction() {
        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }

        $oGatResultModel = new \models\gatResultModel();
        $ogatResultData = $oGatResultModel->findByField('userId', $userData['userId']);
//        print_r($ogatResultData);exit;

        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $obj = new \mihaka\formats\MihakaPDF();
        if (empty($ogatResultData)) {
            $obj->setHTML('<h1>Your Result is Not Available.</h1>');
            $obj->browse();
            exit;
        }

        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
//        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/bootstrap.min.css'), 1);
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/custom.css'), 1);
        foreach ($ogatResultData as $row) {
            $data['userInfo'] = $userData;
            $data['gatResult'] = $row;
            $data['userImage'] = $userImage;
            $data['className'] = $oAdmissionOfferModel->findByPK($row['offerId'], 'cCode, className');
            $HTML = $this->getHTML('gatResult', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function ugtResultAction() {
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }
        $oUGTResultModel = new \models\UGTResultModel();
        $oUGTResultData = $oUGTResultModel->findByFieldData1('userId', $userData['userId']);
        $obj = new \mihaka\formats\MihakaPDF();

        if (empty($oUGTResultData)) {
            $obj->setHTML('<h1>Your Test Schedule is Not Available.</h1>');
            $obj->browse();
            exit;
        }
        $i = 0;

        //$obj->setFooter("Page# {PAGENO} of {nbpg} ");
        //$obj->setFooter("Reference#: " . $userData['userId'] . " Page# {PAGENO} of {nbpg} ");
        //$obj->setFooter("<table><tr><td>Reference#: " . $userData['userId'] . " </td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
        //$obj->setFooter("<table><tr><td>Reference#: " . $userData['userId'] . " </td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
        $obj->getPDFObject()->SetHTMLFooter('
<table width="100%">
    <tr style ="border-top: 1px solid black;">
        <td width="50%" style="text-align: left; border-top:#000 solid thin;" >Page# {PAGENO} of {nbpg}</td>
        <td width="50%" style="text-align: right; border-top:#000 solid thin;">Printed Date: ' . date("d-m-Y") . '</td>
    </tr>
</table>');

        $obj->getPDFObject()->SetHeader($obj->getHeader());
        //$obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/custom.css'), 1);
        foreach ($oUGTResultData as $row) {
            $data['userInfo'] = $userData;
            $data['gatResult'] = $row;
            $data['userImage'] = $userImage;
            $HTML = $this->getHTML('ugtResult', $data);
//            $HTML = $this->getHTML('interResult', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    private function generateChallan($get) {
        $oChallansModel = new \models\ChallansModel();
        $userData = $this->state()->get('userInfo');
        $oChallansModel->byUserId($userData['userId'], $get['appId']);
//        $oChallansModel->byUserIdAndOfferId($userData['userId'], $get['appId']);
    }

    public function challanFormAction() {
        $get = $this->get()->all();
        $this->generateChallan($get);
        if (!empty($get['challan'])) {
            $userDataAdmin = $this->state()->get('userInfo');
            $secKey = \mihaka\helpers\MString::decrypt($get['challan']);
            if ($secKey == $userDataAdmin['userId']) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $appUserId = $oApplicationsModel->findByPK($get['appId'], 'userId');
                $oUserModel = new \models\UsersModel();
                $userData = $oUserModel->findByPK($appUserId['userId']);

                $data['userImage'] = $oUserModel->getUserImgURLByUserData($userData);
//                         print_r($appUserId);exit;
            }
        } else {
            $oApplicationsModel = new \models\ApplicationsModel();
            $appUserId = $oApplicationsModel->findByPK($get['appId'], 'userId');
            $oUserModel = new \models\UsersModel();
            $userData = $oUserModel->findByPK($appUserId['userId']);
//            $userData = $this->state()->get('userInfo');
            $data['userImage'] = USER_IMG;
        }
//        var_dump($data['major']);        die();
        $data['userInfo'] = $userData; //$oUserModel->findByPK($userId);
        $heads = ["BANK COPY", "UNIVERSITY COPY", "CANDIDATE'S COPY"];
//        $heads = ["UNIVERSITY COPY", "CANDIDATE'S COPY", "BANK COPY"];
        $data['heads'] = $heads;
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['application'] = $oApplicationsModel->findByPK($get['appId']);
//        var_dump($data['application']);        die();

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $data['base'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['baseId'], $data['application']['majId'], $userData['gender']);
        $obj = new \mihaka\formats\MihakaPDF();
        if ($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4 || $data['application']['cCode'] == 8) {
//        if ($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4) && ($data['application']['baseId']==9)
//            if ($data['application']['majId'] != 80 || $data['application']['majId'] != 132) {
//                $oGatResultModel = new \models\gatResultModel();
//                $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userData['userId'], $data['application']['majId']);
//                if (empty($data['gatResult'])) {
//                    $obj->setHTML('<h1>You are not eligible to apply because of GAT Result.</h1>');
//                    $obj->browse();
//                    exit;
//                }
//            }
            $oResearchObjectiveModel = new \models\ResearchObjectiveModel();
            $data['researchObjective'] = $oResearchObjectiveModel->findOneByField('userId', $userData['userId']);
            if ($loggedInUserId['userId'] != 1) {
                if (empty($data['researchObjective'])) {
                    $obj->setHTML('<h1>Please Provide Research Objective Information to proceede.</h1>');
                    $obj->browse();
                    exit;
                }
            }

            $oAcademicReferenceModel = new \models\AcademicReferencesModel();
            $data['references'] = $oAcademicReferenceModel->findByField('userId', $userData['userId']);
            if ($loggedInUserId['userId'] != 1) {
                if (empty($data['references']) || count($data['references']) < 2) {
                    $obj->setHTML('<h1>Please Add two References to proceede.</h1>');
                    $obj->browse();
                    exit;
                }
            }
        }

        if ($data['application']['cCode'] != 100) {

            if (empty($data['userInfo']['districtId']) && ($data['application']['cCode']) != 100) {
//            if (empty($data['userInfo']['districtId']) && ($data['application']['cCode']) != 100) {
                $obj->setHTML('<h1>Please update your profile information.</h1>');
                $obj->browse();
                exit;
            }
        }

        if ($data['application']['cCode'] == 19 && $data['application']['majId'] == 27) {

            if (($data['userInfo']['experience'] == 'NA' || empty($data['userInfo']['experience'])) && ($data['application']['cCode']) == 19 && $data['application']['majId'] == 27) {
                $obj->setHTML('<h1>Please update Applicant Profile Information for experience.</h1>');
                $obj->browse();
                exit;
            }
        }

        if ($data['application']['baseId'] == 15 || $data['application']['baseId'] == 46) {
            $oKinshipDetailModel = new \models\KinshipDetailModel();
            $data['kinDetail'] = $oKinshipDetailModel->byUserId($userData['userId']);

            if (empty($data['kinDetail'])) {

                $this->redirect(SITE_URL . 'dashboard/kinshipDetail');
                exit;
            }
        }

        $oDistrictModel = new \models\DistrictModel();
        if (!empty($data['userInfo']['districtId'])) {
            $data['district'] = $oDistrictModel->findByPK($data['userInfo']['districtId'], 'distnm');
        }
        $oMajors = new \models\MajorsModel();
        $data['major'] = $oMajors->getMajorByOfferIdClassIdAndMajorId($data['application']['offerId'], $data['application']['cCode'], $data['application']['majId']);
        $oAdmissionOFfer = new \models\AdmissionOfferModel();
        $data['admissionOffer'] = $oAdmissionOFfer->findByPK($data['application']['offerId'], 'endDate,accNo,feePurpose,cCode,className,preReq, testCity, testStream, challansAllowed');
        $ouserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
        $extensionData = $ouserAdmissionOfferModel->exist($userData['userId'], $data['application']['offerId']);
        $data['admissionOffer']['endDate'] = $data['major']['endDate'];
        if (!empty($extensionData)) {
            $data['admissionOffer']['endDate'] = $extensionData['endDate'];
        }
        $oUsersModel = new \models\UsersModel();
        $userCityData = $oUsersModel->findByPK($userData['userId']);
        $oBaseClass = new \models\BaseClassModel();
        $data['testBase'] = $oBaseClass->getTestBaseByClassIdAndBaseId($data['application']['cCode'], $data['application']['baseId']);
//        print_r($data['testBase']['test']);exit;
        if ($data['testBase']['test'] == 'YES') {
            if (empty($userCityData['testCity']) && ($data['admissionOffer']['testCity'] == 'YES')) {
                $this->redirect(SITE_URL . 'dashboard/testCentre');
                exit;
            }
        }
        $oEducationModel = new \models\EducationModel();
        $data['eduId'] = $oEducationModel->preReq($userData['userId'], $data['admissionOffer']['preReq']);
//        $data['eduId'] = $oApplicationsModel->applicationPrerequisite($userData['userId']);
//        $HTML = $this->getHTML('challanForm', $data);
//        $this->validate($data);
        $HTML = $this->getHTML($this->getChallanViewName($data['application']['cCode']), $data);

        if (empty($userData['picture'])) {
            $obj->setHTML('<h1>Please upload your image to print challan.</h1>');
        } else if (empty($data['eduId'])) {
            $obj->setHTML('<h1>Please complete your academic record.</h1>');
        } else if ($data['major']['dues'] == 0) {
            die();
        } else {
            $obj->setHTML($HTML);
        }

        if ($data['admissionOffer']['challansAllowed'] == 3) {
            $parentAppId = substr($data['application']['chalId'], 2);
            if ($parentAppId != $data['application']['appId']) {
                if ($data['application']['userId'] == 1) {
                    $allMajors = $oApplicationsModel->getAllMajorIdsByUserIdAndChallanId($data['application']['userId'], $data['application']['chalId']);
                    $data['major']['name'] = $allMajors;
                    $data['major']['dues'] = 0;
                    $data['major']['duesInWord'] = ' ';
                    $HTML = $this->getHTML($this->getChallanViewName($data['application']['cCode']), $data);
                    $obj->setHTML($HTML);
                } else {
                    $obj->setHTML('<h1>This Challan is Associated with Parent Challan.</h1>');
                }
            }
        }
        $obj->browse();
    }

    private function postChallanPic($appId) {
        /* Picture upload code */
        $bucketId = \helpers\Common::generateBucket();
        $target_dir = UPLOAD_PATH . $bucketId . '/';
        $target_file = $target_dir . basename($_FILES["uimg"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileName = 'ch' . md5($appId) . '.' . $imageFileType;
        $target_file = $target_dir . $fileName;
// Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["uimg"]["tmp_name"]);
        if ($check === false) {
            return $this->getJsonResponse(false, ['msg' => 'File is not an image.']);
        } else if ($_FILES["uimg"]["size"] > 35000) {// Check file size 50kb
//            die('xyz');
            return $this->getJsonResponse(false, ['msg' => 'Sorry, your file is too large.']);
        }
// Allow certain file formats
        else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return $this->getJsonResponse(false, ['msg' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $imageFileType]);
        } else if (move_uploaded_file($_FILES["uimg"]["tmp_name"], $target_file)) {
            $oPostArray['picture'] = $fileName;
            $oPostArray['picBucket'] = $bucketId;
            $oPostArray['picExt'] = $imageFileType;
            $oPostArray['lastUpdate'] = date("Y-m-d H:i:s");
            $oApplicationsModel = new \models\ApplicationsModel();
            $cCode = $oApplicationsModel->findByPK($appId, 'cCode');
//            if ($cCode['cCode'] == 200) {
//                $oPostArray['isPaid'] = 'Y';
//            }
            if ($oApplicationsModel->saveImage($appId, $oPostArray, $bucketId, $fileName)) {
                return $this->getJsonResponse(true, ['msg' => 'Image uploaded successfully.']);
            } else {
                return $this->getJsonResponse(false, ['msg' => "Sorry, there was an internal error uploading your file. Please try again."]);
            }
        } else {
            return $this->getJsonResponse(false, ['msg' => "Sorry, there was an error uploading your file. Please try again."]);
        }
    }
}
