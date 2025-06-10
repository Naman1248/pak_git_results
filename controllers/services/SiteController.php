<?php

/**
 * Description of SiteController
 *
 * @author SystemAnalyst
 */

namespace controllers\services;

class SiteController extends SuperController {

//    public function deleteApplicationAction() {
//        $appId = $this->post()->appId;
//        $oApplicationsModel = new \models\ApplicationsModel();
//        $oApplicationsModel->isApplicationExist('')
//    }
//
    public function loadMajorsAction() {
        $post = $this->post()->all();
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $result['majors'] = $oMajorsModel->getAllMajorByOfferIdClassId($post['offerId'], $offerData['cCode']);
        echo json_encode($result);
    }
    
    public function loadMajorsPerTestStreamAction() {
        $post = $this->post()->all();
        print_r($post);exit;
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $result['majors'] = $oMajorsModel->getAllMajorByOfferIdClassId($post['offerId'], $offerData['cCode']);
        echo json_encode($result);
    }

    public function resetPasswordAction() {
        $post = $this->post()->all();
        if (empty($post['psswrd']) || empty($post['cpsswrd'])) {
            echo $this->getJsonResponse(false, ['msg' => 'Pleae enter new password/confirm password.']);
            exit;
        } elseif ($post['psswrd'] !== $post['cpsswrd']) {
            echo $this->getJsonResponse(false, ['msg' => 'Password and Confirm password should be same.']);
            exit;
        }
        $data = json_decode(\mihaka\helpers\MString::decrypt($post['id']));
        if (isset($data->email) && isset($data->id)) {
            $oUsersModel = new \models\UsersModel();
            $forgotData = $oUsersModel->validateForgotKey($data->email, $data->id,'ph1');
            if (!empty($forgotData)) {
                //echo md5($post['psswrd']);
                $out = $oUsersModel->upsert(['updatedOn' => date('Y-m-d H:i:s'), 'paswrd' => md5($post['psswrd'])], $forgotData['userId']);
                if ($out) {
                    echo $this->getJsonResponse(true, ['msg' => 'Password updated successfully. Please login to proceed.']);
                    exit;
                } else {
                    echo $this->getJsonResponse(false, ['msg' => 'Seems your link expired.']);
                    exit;
                }
            }
        }
    }

    public function forgotAction() {
        die('NA');
        if ($this->isPost()) {
            $oUserModel = new \models\UsersModel();
            $data = $oUserModel->validUserByEmail($this->post()->email);

            //var_dump($data);exit;
            if (empty($data)) {
                echo $this->getJsonResponse(false, ['msg' => 'No such email register.']);
                exit;
            } else {

                $oEmailQueueModel = new \models\EmailQueueModel();
                $forgotEmailCount = $oEmailQueueModel->totalEmailByEmailAndSubject($this->post()->email, 'Forgot Password');

                if ($forgotEmailCount['Total'] > 0) {
                    echo $this->getJsonResponse(false, ['msg' => 'You had attempted maximum chances of forgot email.']);
                    exit;
                } else {
                    $randId = \mihaka\helpers\MString::randomString();
                    //echo \mihaka\helpers\MString::decrypt("VJ8DpBcWNfEjtd7T72o9GKe7gywFVe4M4NPjyIQolFox3ksef7dy7r0JaFRpw0LvCcVQyleqq5zUZI9SWQ==");
                    //exit;
                    $oUserModel->upsert(
                            [
                        'forgotKey' => $randId,
                        'forgotKeyExpiry' => date('Y-m-d H:i:s', strtotime("+1 DAY"))
                            ], $data['userId']);
                    $linkData = json_encode(['email' => $data['email'], 'id' => $randId]);
                    $id = \mihaka\helpers\MString::encrypt($linkData);
                    $link = SITE_URL . 'user/newPassword/id/' . $id;
                    $contents = 'Dear Student,'
                            . '<br><br> Please <a href="' . $link . '">click here</a> to reset your password.<br>'
                            . '<br><br>'
                            . 'System Analyst'
                            . '<br>'
                            . 'GCU  Lahore'
                            . '<br>'
                            . '<br><br><br><br>'
                            . 'You are receiving this email because you have registered on this website for admission. Click here for <a href="' . SITE_URL . 'user/unsubscribe/id/' . $id . '">Unsubscribe</a>';
                    $subject = 'Forgot Password';
                    $oEmailQueueModel = new \models\EmailQueueModel();
                    $oEmailQueueModel->upsert([
                        'email' => $data['email'],
                        'subject' => $subject,
                        'contents' => $contents,
                        'added_on' => date('Y-m-d H:i:s')
                    ]);
                    $oMailGun = new \components\MailGun();
                    $tag = 'Forgot Password';
                    $text = str_ireplace('<br>', "\r\n", $contents);
                    $oMailGun->sendMail($data['email'], $data['name'], $subject, $contents, $text, $tag);
                    /*  $oMailer = $this->email();
                      $oMailer->isHTML();
                      $oMailer->from('GCU LAHORE', 'no-reply@gcu.edu.pk');
                      $oMailer->send($data['email'], $subject, $contents); */
                    echo $this->getJsonResponse(true, ['msg' => 'Please check your email to proceed.']);
                }
            }
        }
    }
    public function forgotSmsAction() {
        if ($this->isPost()) {
            $oUserModel = new \models\UsersModel();
            $data = $oUserModel->validUserByMobile($this->post()->mobile);

            //var_dump($data);exit;
            if (empty($data)) {
                echo $this->getJsonResponse(false, ['msg' => 'No such mobile registered.']);
                exit;
            } else {

                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $forgotSmsCount = $oSmsQueueModel->totalSmsByMobileAndTag($this->post()->mobile, 'Forgot'.date('Ym'));

                if ($forgotSmsCount['Total'] > 5) {
                    echo $this->getJsonResponse(false, ['msg' => 'You had attempted maximum chances of forgot sms.']);
                    exit;
                } else {
                    $randId = \mihaka\helpers\MString::randomString();
                    $oUserModel->upsert(
                            [
                        'forgotKey' => $randId,
                        'forgotKeyExpiry' => date('Y-m-d H:i:s', strtotime("+1 DAY"))
                            ], $data['userId']);
                    $linkData = json_encode(['email' => $this->post()->mobile, 'id' => $randId]);
                    $id = \mihaka\helpers\MString::encrypt($linkData);
                    $link = SITE_URL . 'user/newPassword/id/' . $id;
                    $contents = $link;
                            
                    $oSmsQueueModel->insertPhones(
                            $this->post()->mobile,
                        $contents,
                        'Forgot'.date('Ym')
                    );
                    
                    echo $this->getJsonResponse(true, ['msg' => 'Please check sms on your mobile to proceed.']);
                }
            }
        }
    }
    public function forgotCNICAction() {
        if ($this->isPost()) {
//            $data_post = $this->post()->all();
//            print_r($data_post);exit;
            $oUserModel = new \models\UsersModel();
            $data = $oUserModel->validUserByCNIC($this->post()->cnic);
//            print_r($data);exit;

//            var_dump($data);exit;
            if (empty($data)) {
                echo $this->getJsonResponse(false, ['msg' => 'No such cnic exist.']);
                exit;
            } else {

                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $forgotSmsCount = $oSmsQueueModel->totalSmsByMobileAndTag($this->post()->mobile, 'Forgot'.date('Ym'));
                if ($forgotSmsCount['Total'] > 5) {
                    echo $this->getJsonResponse(false, ['msg' => 'You had attempted maximum chances of forgot sms.']);
                    exit;
                } else {
                    $randId = \mihaka\helpers\MString::randomString();
                    $oUserModel->upsert(
                            [
                        'forgotKey' => $randId,
                        'forgotKeyExpiry' => date('Y-m-d H:i:s', strtotime("+1 DAY"))
                            ], $data['userId']);
                    $linkData = json_encode(['email' => $this->post()->mobile, 'id' => $randId]);
                    $id = \mihaka\helpers\MString::encrypt($linkData);
                    $link = SITE_URL . 'user/newPassword/id/' . $id;
                    $contents = $link;
                    $oSmsQueueModel->insertPhones(
                            $this->post()->mobile,
                        $contents,
                        'Forgot'.date('Ym')
                    );
                    
                    echo $this->getJsonResponse(true, ['msg' => 'Please check sms on your mobile to proceed.']);
                }
            }
        }
    }
    public function forgotCNICSMSAction() {
        if ($this->isPost()) {
            $oUserModel = new \models\UsersModel();
            $data = $oUserModel->validUserByCNIC($this->post()->cnic);
//            print_r($data);exit;

//            var_dump($data);exit;
            if (empty($data)) {
                echo $this->getJsonResponse(false, ['msg' => 'No such cnic exist.']);
                exit;
            } else {

                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $forgotSmsCount = $oSmsQueueModel->totalSmsByMobileAndTag($this->post()->mobile, 'Forgot'.date('Ym'));
                if ($forgotSmsCount['Total'] > 5) {
                    echo $this->getJsonResponse(false, ['msg' => 'You had attempted maximum chances of forgot sms.']);
                    exit;
                } else {
                    $randId = \mihaka\helpers\MString::randomString();
                    $oUserModel->upsert(
                            [
                        'forgotKey' => $randId,
                        'forgotKeyExpiry' => date('Y-m-d H:i:s', strtotime("+1 DAY"))
                            ], $data['userId']);
                    $linkData = json_encode(['email' => $this->post()->mobile, 'id' => $randId]);
                    $id = \mihaka\helpers\MString::encrypt($linkData);
                    $link = SITE_URL . 'user/newPassword/id/' . $id;
                    $contents = $link;
                    var_dump($contents);exit;
                    $oSmsQueueModel->insertPhones(
                            $this->post()->mobile,
                        $contents,
                        'Forgot'.date('Ym')
                    );
                    
                    echo $this->getJsonResponse(true, ['msg' => 'Please check sms on your mobile to proceed.']);
                }
            }
        }
    }

    public function loadChildBasesAction() {
        $gender = $this->state()->get('userInfo')['gender'];
        $post = $this->post();
//        var_dump($post);
//        if (!is_null($this->post()->email)) {
//            $oUsersModel = new \models\UsersModel();
//            $userData = $oUsersModel->validUserByEmail($this->post()->email);
//            $gender = $userData['gender'];
//        }
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post->offerId, 'cCode');

        $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
        $result['childBases'] = $oClassBaseMajorModel->getBasesByClassParentBaseMajorGender($offerData['cCode'], $post->majorId, $gender, $post->parentBaseId);

        echo json_encode($result);
    }

    public function loadChildBasesPerTestStreamAction() {
        $gender = $this->state()->get('userInfo')['gender'];
        $userId = $this->state()->get('userInfo')['userId'];
        
        $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
        $offerIds = explode(",", $streamOfferIds);
        
        $post = $this->post();

        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post->offerId, 'cCode');

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $result['childBases'] = $oClassBaseMajor->getBasesByClassParentBaseMajorGenderTestStream($offerData['cCode'], $post->majorId, $gender, $offerIds, $userId, $post->parentBaseId);

        echo json_encode($result);
    }
    public function loadSetsAction() {
        $gender = $this->state()->get('userInfo')['gender'];
//        if (!is_null($this->post()->email)) {
//            $oUsersModel = new \models\UsersModel();
//            $userData = $oUsersModel->validUserByEmail($this->post()->email);
//            $gender = $userData['gender'];
//        }
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($this->post()->cCode, 'cCode');

        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $result['sets'] = $oSubjectCombinationModel->findByClassAndGroup($offerData['cCode'], $this->post()->gCode);

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $result['bases'] = $oClassBaseMajor->getBasesByClassParentBaseMajorGender($offerData['cCode'], $this->post()->gCode, $gender);
//        print_r($gender);exit;

        echo json_encode($result);
    }
    
    public function loadSetsPerTestStreamAction() {
        
        $gender = $this->state()->get('userInfo')['gender'];
        $userId = $this->state()->get('userInfo')['userId'];
        
        $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
        $offerIds = explode(",", $streamOfferIds);
        
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($this->post()->cCode, 'cCode');

        $oSubjectCombinationModel = new \models\SubjectCombinationModel();
        $result['sets'] = $oSubjectCombinationModel->findByClassAndGroup($offerData['cCode'], $this->post()->gCode);

        $oClassBaseMajor = new \models\ClassBaseMajorModel();
        $result['bases'] = $oClassBaseMajor->getBasesByClassParentBaseMajorGenderTestStream($offerData['cCode'], $this->post()->gCode, $gender, $offerIds, $userId);

        echo json_encode($result);
    }

    public function loadDistrictAction() {
        $proviceId = $this->get()->id;
        $oDistrictModel = new \models\DistrictModel();
        echo json_encode($oDistrictModel->findByField('provinceid', $proviceId));
    }

    public function loadTehsilAction() {
        $districtId = $this->get()->id;
        $oTehsilModel = new \models\TehsilModel();
        echo json_encode($oTehsilModel->findByField('distId', $districtId));
    }

    public function deleteEducationAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oEducationModel = new \models\EducationModel();
        if ($this->post()->exists('eduId')) {
            $out = $oEducationModel->deleteByEduId($this->post()->eduId, $userId);
        }
        $this->printAndDieJsonResponse($out['status'], $out['eduData']);
    }

    public function deleteKinshipDetailAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oKinshipDetailModel = new \models\KinshipDetailModel();
        if ($this->post()->exists('kinId')) {
            $out = $oKinshipDetailModel->deleteByKinId($this->post()->kinId, $userId);
        }
        $this->printAndDieJsonResponse($out['status'], $out['kinData']);
    }

    public function deleteProfessionAction() {

        $userId = $this->state()->get('userInfo')['userId'];
        $oProfessionInfoModel = new \models\ProfessionInfoModel();
        if ($this->post()->exists('id')) {
            $profData = $oProfessionInfoModel->findByPKAndUserId($this->post()->id, $userId, 'id,userId');
            if (!empty($profData)) {
                $status = $oProfessionInfoModel->deleteByPK($profData['id']);
            }
        }
        $this->printAndDieJsonResponse($status, $profData);
    }

    public function deletePublicationAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oPublicationsModel = new \models\PublicationsModel();
        if ($this->post()->exists('id')) {
            $pubData = $oPublicationsModel->findByPKAndUserId($this->post()->id, $userId, 'id,userId');
            if (!empty($pubData)) {
                $status = $oPublicationsModel->deleteByPK($pubData['id']);
            }
        }
        $this->printAndDieJsonResponse($status, $pubData);
    }

    public function deleteResearchWorkAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oResearchWorkModel = new \models\ResearchWorkModel();
        if ($this->post()->exists('id')) {
            $researchData = $oResearchWorkModel->findByPKAndUserId($this->post()->id, $userId, 'id,userId');
            if (!empty($researchData)) {
                $status = $oResearchWorkModel->deleteByPK($researchData['id']);
            }
        }
        $this->printAndDieJsonResponse($status, $researchData);
    }

    public function deleteAcademicReferenceAction() {
        $userId = $this->state()->get('userInfo')['userId'];
        $oAcademicReferencesModel = new \models\AcademicReferencesModel();
        if ($this->post()->exists('id')) {
            $refData = $oAcademicReferencesModel->findByPKAndUserId($this->post()->id, $userId, 'id,userId');
            if (!empty($refData)) {
                $status = $oAcademicReferencesModel->deleteByPK($refData['id']);
            }
        }
        $this->printAndDieJsonResponse($status, $refData);
    }

}
