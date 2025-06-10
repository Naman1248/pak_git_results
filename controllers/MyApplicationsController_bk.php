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

    private function getFormViewName($cCode) {
        return $this->forms[$cCode]['form'];
    }

    private function getUndertakingViewName($cCode) {
        return $this->forms[$cCode]['undertakings'];
    }

    private function getChallanViewName($cCode) {
        return $this->challan[$cCode];
    }

    public function indexAction() {
        if (!empty($_FILES)) {
            $postImgData = $this->post()->all();
            $data['picStatus'] = $this->postChallanPic($postImgData['appId']);
        }
        $userData = $this->state()->get('userInfo');
        $oApplications = new \models\ApplicationsModel();
        $data['applications'] = $oApplications->byUserId($userData['userId']);

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
        } else if (empty($data['userInfo']['religion'])) {
            $obj->setHTML('<h1>Please update your profile information.</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['eduId'])) {
            $obj->setHTML('<h1>Please upload your academic record (Specially Last Degree).</h1>');
            $obj->browse();
            exit;
        } else if (empty($data['application']['picExt'])) {
            $obj->setHTML('<h1>Please upload your paid challan.</h1>');
            $obj->browse();
            exit;
        }
    }
    public function gatSlipAction() {
        $get = $this->get()->all();
        $userData = $this->state()->get('userInfo');
        if (empty(USER_IMG) || strpos(USER_IMG, 'not_available.jpg')) {
            $userImage = '';
        } else {
            $userImage = USER_IMG;
        }

        $oGatSlipModel = new \models\GatSlipModel();
        $gatSlipData = $oGatSlipModel->findByField('userId', $userData['userId']);
        if (empty($gatSlipData)) {
            $this->redirect(SITE_URL);
        }
        $obj = new \mihaka\formats\MihakaPDF();
        $i = 0;
        $obj->setFooter("Page# {PAGENO} of {nbpg} ");
        $obj->getPDFObject()->SetHeader($obj->getHeader());
        $obj->getPDFObject()->SetFooter($obj->getFooter());
        $obj->getPDFObject()->WriteHTML(file_get_contents(ASSET_URL . 'ss/gatForm.css'), 1);
        foreach ($gatSlipData as $row) {
            $data['userInfo'] = $userData;
            $data['gatSlip'] = $row;
            $data['userImage'] = $userImage;
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

//        var_dump($ogatResultData);
//        die();
        $obj = new \mihaka\formats\MihakaPDF();
        if (empty($ogatResultData)) {
            $this->redirect(SITE_URL . 'home/errors');
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
            $HTML = $this->getHTML('gatResult', $data);
            if ($i > 0) {
                $obj->addPage();
            }
            $obj->getPDFObject()->WriteHTML($HTML, 2);
            $i++;
        }
        $obj->getPDFObject()->output();
    }

    public function challanFormAction() {
        $get = $this->get()->all();
        $oMyApplications = new \useCases\MyApplications();
        $oMyApplications->challanForm($get);
    }
    
    public function printFormAction() {
        $get = $this->get()->all();
        $oMyApplications = new \useCases\MyApplications();
        $oMyApplications->printForm($get);
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
        } else if ($_FILES["uimg"]["size"] > 20000) {// Check file size 50kb
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
