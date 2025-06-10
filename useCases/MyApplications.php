<?php

/**
 * Description of MyApplications
 *
 * @author SystemAnalyst
 */

namespace useCases;

class MyApplications extends \mihaka\MihakaUseCase {

    private $forms = [
        '100' => ['form' => 'gatForm'],
        '81' => ['form' => 'diplomaForm'],
        '78' => ['form' => 'diplomaForm'],
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
        '1' => ['form' => 'intermediateForm', 'undertakings' => ['include/intermediate/undertakingIntermediate1', 'include/intermediate/undertakingIntermediate2']]
    ];
//    private $specialForms = ['16' => ['sportsForm','include/specialCategory/sportsUndertaking'],
    private $specialForms = [
        '1' => ['specialCategoryForm'],
        '3' => ['specialCategoryForm'],
        '5' => ['specialCategoryForm'],
        '6' => ['specialCategoryForm'],
        '7' => ['specialCategoryForm'],
        '13' => ['specialCategoryForm'],
        '15' => ['specialCategoryForm'],
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
        '81' => 'bachelorChallanForm'
    ];

    public function challanForm($get) {
        if (!empty($get['challan'])) {
            $userDataAdmin = $this->state()->get('userInfo');
            $secKey = \mihaka\helpers\MString::decrypt($get['challan']);
            if ($secKey == $userDataAdmin['userId']) {
                $oApplicationsModel = new \models\ApplicationsModel();
                $appUserId = $oApplicationsModel->findByPK($get['appId'], 'userId');
                $oUserModel = new \models\UsersModel();
                $userData = $oUserModel->findByPK($appUserId['userId']);

                $data['userImage'] = $oUserModel->getUserImgURLByUserData($userData);
            }
        } else {
            $userData = $this->state()->get('userInfo');
            $data['userImage'] = USER_IMG;
        }
        $data['userInfo'] = $userData; //$oUserModel->findByPK($userId);
        $heads = ["UNIVERSITY COPY", "CANDIDATE'S COPY", "BANK COPY"];
        $data['heads'] = $heads;
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['application'] = $oApplicationsModel->findByPK($get['appId']);

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $data['base'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['baseId'], $data['application']['majId'], $userData['gender']);

        $obj = new \mihaka\formats\MihakaPDF();

        if ($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4) {

            $oGatResultModel = new \models\gatResultModel();
            $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userData['userId'], $data['application']['majId']);
            if (empty($data['gatResult'])) {
                $obj->setHTML('<h1>You are not eligible to apply because of GAT Result.</h1>');
                $obj->browse();
                exit;
            }
        }
        if (empty($data['userInfo']['districtId'])) {
            $obj->setHTML('<h1>Please update your profile information.</h1>');
            $obj->browse();
            exit;
        }
        $oDistrictModel = new \models\DistrictModel();
        $data['district'] = $oDistrictModel->findByPK($data['userInfo']['districtId'], 'distnm');
        $oMajors = new \models\MajorsModel();
        $data['major'] = $oMajors->getMajorByOfferIdClassIdAndMajorId($data['application']['offerId'], $data['application']['cCode'], $data['application']['majId']);
        $oAdmissionOFfer = new \models\AdmissionOfferModel();
        $data['admissionOffer'] = $oAdmissionOFfer->findByPK($data['application']['offerId'], 'endDate,accNo,feePurpose,cCode');
        $data['eduId'] = $oApplicationsModel->applicationPrerequisite($userData['userId']);
//        $HTML = $this->getHTML('challanForm', $data);
        $HTML = $this->getHTML($this->getChallanViewName($data['application']['cCode']), $data);
//        echo $HTML; exit();

        if (empty($userData['picture'])) {
            $obj->setHTML('<h1>Please upload your image to print challan.</h1>');
        } else if (empty($data['eduId'])) {
            $obj->setHTML('<h1>Please upload your academic record (Specially Last Degree).</h1>');
        } else if ($data['major']['dues'] == 0) {
            die();
        } else {
            $obj->setCSS(ASSET_URL . 'ss/pdfForm.css');
            $obj->setHTML($HTML);
        }
        $obj->browse();
    }

    private function getChallanViewName($cCode) {
        return $this->challan[$cCode];
    }

    public function printForm($userData) {
        //
        //echo "<pre>";print_r($userData);exit;
        /* if (!empty($get['form'])) {

          $oApplicationsModel = new \models\ApplicationsModel();
          echo "<br><br>";var_dump($get['appId']);exit;
          $appUserId = $oApplicationsModel->findByPK($get['appId'], 'userId');
          echo "<pre>";var_dump($appUserId);exit;
          $oUserModel = new \models\UsersModel();
          $userData = $oUserModel->findByPK($appUserId['userId']);
          $data['userImage'] = $oUserModel->getUserImgURLByUserData($userData);
          } else {
          $userData = $this->state()->get('userInfo');
          $data['userImage'] = USER_IMG;
          } */
        $oUserModel = new \models\UsersModel();
        $data['userInfo'] = $oUserModel->findByPK($userData['userId']);
        $oApplicationsModel = new \models\ApplicationsModel();
        $data['eduId'] = $oApplicationsModel->applicationPrerequisite($userData['userId']);
        $data['application'] = $oApplicationsModel->findByPK($userData['appId'], 'offerId,majId,formNo,cCode,baseId,setNo,picExt,childBase,baseTypeDet');
        $loggedInUserId = $this->state()->get('userInfo');
        //echo "<pre>";print_r($data);exit;
        $this->validate($data);
        $oEducationModel = new \models\EducationModel();
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
        $data['province'] = $oProvinceModel->findByPK($data['userInfo']['provinceId'], 'provinceName');

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
        $data['tehsil'] = $oThsilModel->findByPk($data['userInfo']['tehsilId'], 'tehNm');
//        print_r($data); exit;
        $oCityModel = new \models\CityModel();
        $data['city'] = $oCityModel->findByPK($data['userInfo']['cityId'], 'cityName');

        $oMajors = new \models\MajorsModel();
        $data['major'] = $oMajors->getMajorByOfferIdClassIdAndMajorId($data['application']['offerId'], $data['application']['cCode'], $data['application']['majId']);
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $data['admissionOffer'] = $oAdmissionOffer->findByPk($data['application']['offerId'], 'year,className');

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $data['base'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['baseId'], $data['application']['majId'], $data['userInfo']['gender']);
        if ($data['application']['baseId'] == 15) {
            $oKinship = new \models\KinshipModel();
            $data['kinship'] = $oKinship->findByPK($userData['userId']);
        }
        if ($data['application']['baseId'] == 1 || $data['application']['baseId'] == 20) {
            $oOalevelModel = new \models\OalevelModel();
            $data['oalevel'] = $oOalevelModel->findByPK($userData['userId']);
        }
        if (!empty($data['application']['childBase'])) {
            $data['childBase'] = $oClassBaseMajor->getBaseByClassIdAndBaseIdAndMajor($data['application']['cCode'], $data['application']['childBase'], $data['application']['majId'], $data['userInfo']['gender'], $data['application']['baseId']);
        } else {
            $data['childBase'] = [];
        }

        //NEW CODE
        if ($data['application']['cCode'] == 50 || $data['application']['cCode'] == 4) {
            $oGatResultModel = new \models\gatResultModel();
            $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userData['userId'], $data['application']['majId']);
            if (empty($data['gatResult'])) {
                $response['status'] = false;
                $response['msg'] = '<h1>You are not eligible to apply because of GAT Result.</h1>';
                return $response;
            }

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
                    $response['status'] = false;
                    $response['msg'] = '<h1>Please Provide Rsearch Objective Information to proceede.</h1>';
                    return $response;
                }
            }
            $oAcademicReferenceModel = new \models\AcademicReferencesModel();
            $data['references'] = $oAcademicReferenceModel->findByField('userId', $userData['userId']);
            if ($loggedInUserId['userId'] != 1) {
                if (empty($data['references']) || count($data['references']) < 2) {
                    $response['status'] = false;
                    $response['msg'] = '<h1>Please Add two References to proceede.</h1>';
                    return $response;
                }
            }
        }
        if (!empty($data['application']['setNo'])) {
            $oSubjectCombination = new \models\SubjectCombinationModel();
            $data['subjects'] = $oSubjectCombination->getSubjectsByClassAndMajorAndSetNo($data['application']['cCode'], $data['application']['majId'], $data['application']['setNo']);
        }
        return [
            'data' => $data,
            'formName' => $this->getFormViewName(
                    $data['application']['cCode']
            ),
            'undertakings' => $this->getUndertakingViewName($data['application']['cCode']),
            'specialForms' => $this->getSpecialFormsViewName($data['application']['cCode']),
        ];
    }

    public function makePrintFormPDF($data, $HTML, $undertakingHtml, $specialFormsHtml) {
        $obj = new \mihaka\formats\MihakaPDF();
        //$HTML = $this->getHTML($this->getFormViewName($data['application']['cCode']), $data);
        //else 
//            $obj->setCSS(ASSET_URL . 'ss/pdfForm');
//            $obj->setHTML($HTML);
        $i = 0;
        $obj->setFooter("<table><tr><td>Reference#: " . $data['userInfo']['userId'] . " </td><td align='right'>Page# {PAGENO} of {nbpg}</td><td align='right'>Printed Date: " . date("d-m-Y") . "</td></tr></table> ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/pdfForm.css'), 1);
        $obj->getPDFObject()->WriteHTML($HTML, 2);

        //$undertakings = $this->getUndertakingViewName($data['application']['cCode']);
        foreach ($undertakings as $undertaking) {
            $obj->addPage();
            //$HTML = $this->getHTML($undertaking, $data);
            $obj->getPDFObject()->WriteHTML($undertakingHtml, 2);
        }
//            print_r($undertaking);
        if (array_key_exists($data['application']['baseId'], $this->specialForms)) {
            //$specialForms = $this->specialForms[$data['application']['baseId']];
            foreach ($specialForms as $specialForm) {
                $obj->addPage();
                //$HTML = $this->getHTML($specialForm, $data);
                $obj->getPDFObject()->WriteHTML($specialFormsHtml, 2);
            }
        }
        $obj->getPDFObject()->output();

        //$obj->setFooter('Printed Date : ' . date('d-m-Y'));
        //$obj->browse();
    }

    private function getFormViewName($cCode) {
        return $this->forms[$cCode]['form'];
    }

    private function getUndertakingViewName($cCode) {
        return $this->forms[$cCode]['undertakings'];
    }

    private function getSpecialFormsViewName($data) {
        if (array_key_exists($data['application']['baseId'], $this->specialForms)) {
            return $this->specialForms[$data['application']['baseId']];
        }
        return '';
    }

    private function validate($data) {
        $obj = new \mihaka\formats\MihakaPDF();
        if (empty($data['userInfo']['picture'])) {
            $obj->setHTML('<h1>Please upload your picture to print form.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['userInfo']['religion'])) {
            $obj->setHTML('<h1>Please update your profile information.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['eduId'])) {
            $obj->setHTML('<h1>Please upload your academic record (Specially Last Degree).</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['application']['picExt'])) {
//            $obj->setHTML('<h1>Please upload your paid challan.</h1>');
//            $obj->browse();
//            exit;
        }
    }

}
