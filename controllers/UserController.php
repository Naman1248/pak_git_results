<?php

namespace controllers;

/**
 * Description of UserController
 *
 * @author SystemAnalyst
 */
class UserController extends SuperController {

    public function tempAction() {
        \components\Eocean::sendSms('03214532781', 'Testing message to test sms');
        exit;
        /* $contents = 'Dear Student,'
          . '<br><br> Please <a href="#">click here</a> to confirm your account.<br>'
          . '<br><br>'
          . 'System Analyst Office'
          . '<br>'
          . 'GCU  Lahore'
          . '<br>'
          . '<br><br><br><br>'
          . 'You are receiving this email because you have registered on this website for admission. Click here for <a href="' . SITE_URL . 'user/unsubscribe/id/' . $id . '">Unsubscribe</a>';
          $subject = 'Account Confirmation Email';
          $oMailer = $this->email();
          $oMailer->isHTML();
          $oMailer->from('GCU LAHORE', 'systemanalyst@gcu.edu.pk');
          $oMailer->replyTo('GCU LAHORE', 'no-reply@gcu.edu.pk');
          $oMailer->send('hadia01waseem@gmail.com', $subject, $contents);
          die('end'); */
        $obj = new \components\MailGun();
        $html = '';
        $tag = 'GAT Admissions-1 2021';
        $replyto = 'no-reply@gcuonline.pk';
        $text = 'Congratulations AMBER NOSHEEN, you just sent an email with Mailgun!  You are truly awesome!"})

# You can see a record of this email in your logs: https://app.mailgun.com/app/logs.

# You can send up to 300 emails/day from this sandbox server.
# Next, you should add your own domain so you can send 10000 emails/month for free.';
        $obj->sendMail('kaiserwaseem@hotmail.com', 'Amber Nosheen', 'Test email', $html, $text, $tag, $replyto);
    }

    public function loginAction() {
        $this->moveToDashbardIfUser();
        $this->render('login', []);
    }

    public function unsubscribeAction() {
        //$this->get()->id;
        echo "Unsubscription successfully completed.";
        //$this->redirect(SITE_URL);
    }

    public function logoutAction() {
        $oUserModel = new \models\UsersModel();
        $oUserModel->logout();
        exit();
    }

    public function postLoginAction() {
        $post = $this->post()->all();
        if (empty($post['username']) || empty($post['passwrd'])) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please! Enter complete information.']);
        }
        $oUserModel = new \models\UsersModel();
        $out = $oUserModel->login($post['username'], $post['passwrd']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Invalid User Name / Password']);
        }
    }

    public function signUpAction() {
//        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
//        $offerings = $oAdmmissionOfferModel->getCurrentOpenings();
//        if (empty($offerings)) {
//            $this->redirect(SITE_URL . 'home/alert');
//            exit;
//        }
        $this->moveToDashbardIfUser();
        $oCityModel = new \models\CityModel();
        $data['cities'] = $oCityModel->getCities('cityID,cityName');
        $this->render('signup', $data);
    }

//    public function loadGroupsAction() {
//
//        $post = $this->post();
//        $oAdmissionOffer = new \models\AdmissionOfferModel();
//        $offerData = $oAdmissionOffer->findByPK($post->offerId, 'cCode');
//        $oBaseClass = new \models\BaseClassModel();
////        $result['bases'] = $oBaseClass->findByField('cCode', $offerData['cCode']);
//        $result['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
//        $oMajorsModel = new \models\MajorsModel();
//        $result['majors'] = $oMajorsModel->getMajorByOfferIdClassId($post->offerId, $offerData['cCode']);
////        $result['majors'] = $oMajorsModel->findByField('cCode', $offerData['cCode'], 'majId,name');
//        echo json_encode($result);
//    }
    public function loadGroupsAction() {

        $post = $this->post()->all();
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $result['majors'] = $oMajorsModel->getMajorByOfferIdClassId($post['offerId'], $offerData['cCode']);
        if (isset($post['loadBases'])) {
            $oBaseClass = new \models\BaseClassModel();
            $result['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
        }
        echo json_encode($result);
    }

    public function loadBoardsAction() {
        $post = $this->post()->all();
        $oBoardModel = new \models\BoardModel();
        $result['boards'] = $oBoardModel->getBoards($post['preExam']);
//        var_dump($result);exit;
        $oExamLevelClassModel = new \models\ExamLevelClassModel();
        $result['examClasses'] = $oExamLevelClassModel->getClassesByExam($post['preExam']);
        echo json_encode($result);
    }

    public function postSignUpAction() {
        $post = $this->post();
        $oUserModel = new \models\UsersModel();
        echo json_encode($oUserModel->signup($post));
    }

    public function postApplyAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        echo json_encode($oApplicationsModel->apply($post, $userId));
    }
    public function postApplyMultiBaseCtgryAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        echo json_encode($oApplicationsModel->applyMultiBaseCtgry($post, $userId));
    }

    public function postApplyAdminAction() {
        $post = $this->post()->all();

        $userId = $post['userid'];

        $oApplicationsModel = new \models\ApplicationsModel();
        echo json_encode($oApplicationsModel->applyAdmin($post, $userId));
    }

    public function postApplyTestStreamAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        echo json_encode($oApplicationsModel->applyTestStream($post, $userId));
    }

    public function postEduAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post();
        $oEducationModel = new \models\EducationModel();
        echo json_encode($oEducationModel->addEducation($post, $userId));
    }

    public function postKinshipAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post();
        $oKinshipDetailModel = new \models\KinshipDetailModel();
        echo json_encode($oKinshipDetailModel->addKinshipDetail($post, $userId));
    }

    public function postProfileAction() {
        $post = $this->post()->all();
        $userId = $this->state()->get('userInfo')['userId'];
        $cCode = $this->state()->get('userInfo')['cCode'];
        $oUserModel = new \models\UsersModel();
        echo json_encode($oUserModel->profileUpdate($post, $userId, $cCode));
    }

    public function postInternationalInfoAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
//        var_dump($post);exit;
        $oInternationalModel = new \models\internationalInfoModel();
        echo json_encode($oInternationalModel->addInternationalDetail($post, $userId));
    }

    public function postOverseasAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post();
        $oOverseasModel = new \models\OverseasModel();
        echo json_encode($oOverseasModel->addOverseasDetail($post, $userId));
    }

    public function postTestInfoAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post();
        $oTestInfoModel = new \models\TestInfoModel();
        echo json_encode($oTestInfoModel->addTestInfo($post, $userId));
    }

    public function postProfessionAction() {
        $userId = $this->state()->get('userInfo')['userId'];
//        $post = $this->post();
        $post = $this->post()->all();
        $oProfessionInfoModel = new \models\ProfessionInfoModel();
        echo json_encode($oProfessionInfoModel->addProfession($post, $userId));
    }

    public function postPublicationAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oPublicationsModel = new \models\PublicationsModel();
        echo json_encode($oPublicationsModel->addPublication($post, $userId));
    }

    public function postResearchWorkAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oResearchWorkModel = new \models\ResearchWorkModel();
        echo json_encode($oResearchWorkModel->addResearchWork($post, $userId));
    }

    public function postReferenceAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oAcademicReferencesModel = new \models\AcademicReferencesModel();
        echo json_encode($oAcademicReferencesModel->addReference($post, $userId));
    }

    public function postResearchObjectiveAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $post = $this->post()->all();
        $oResearchObjectiveModel = new \models\ResearchObjectiveModel();
        echo json_encode($oResearchObjectiveModel->addResearchObjective($post, $userId));
    }

    public function postTestCentreAction() {
        $post = $this->post()->all();
        $userId = $this->state()->get('userInfo')['userId'];
        $oUserModel = new \models\UsersModel();
        echo json_encode($oUserModel->updateTestCentre($post, $userId));
    }

//    public function postTestStreamAction() {
//        $post = $this->post()->all();
//        $userId = $this->state()->get('userInfo')['userId'];
//        $oUserModel = new \models\UsersModel();
//        echo json_encode($oUserModel->updateTestStream($post, $userId));
//    }

    public function forgotAction() {
        //$this->render('forgot');
//        $this->render('forgotSMS');
        $this->render('forgotCNIC');
    }
    
    public function newPasswordAction() {
        if ($this->get()->id) {
            $data = json_decode(\mihaka\helpers\MString::decrypt($this->get()->id));
            if (isset($data->email) && isset($data->id)) {
                $oUsersModel = new \models\UsersModel();
                $forgotData = $oUsersModel->validateForgotKey($data->email, $data->id, 'ph1');
                if (!empty($forgotData)) {
                    $forgotData['id'] = $this->get()->id;
                    $this->render('newPassword', $forgotData);
                    exit;
                }
            }
        }
        $this->render('newPassword', ['msg' => 'Forgot Link expired or invalid']);
    }

    public function newPasswordSMSAction() {
        if ($this->get()->id) {
            $data = json_decode(\mihaka\helpers\MString::decrypt($this->get()->id));
            if (isset($data->email) && isset($data->id)) {
                $oUsersModel = new \models\UsersModel();
                $forgotData = $oUsersModel->validateForgotKey($data->email, $data->id);
                if (!empty($forgotData)) {
                    $forgotData['id'] = $this->get()->id;
                    $this->render('newPassword', $forgotData);
                    exit;
                }
            }
        }
        $this->render('newPasswordSMS', ['msg' => 'Forgot Link expired or invalid']);
    }

    public function confirmAction() {
        $post = $this->get()->all();

        $data = json_decode(\mihaka\helpers\MString::decrypt($post['id']));
        if (isset($data->email) && isset($data->id)) {
            $oUsersModel = new \models\UsersModel();
            $confrimData = $oUsersModel->validateConfirmKey($data->email, $data->id);
            if (!empty($confrimData)) {
                $out = $oUsersModel->upsert(['isConfirmed' => 'Y', 'confirmedOn' => date('Y-m-d H:i:s')], $confrimData['userId']);
                if ($out) {
                    $this->render('confirm', ['msg' => 'Account activated successfully. Click <a href="' . SITE_URL . 'user/login">here</a> to login.']);
                }
            }
        }
    }

    public function indexAction() {
        $this->render('index');
    }
}
