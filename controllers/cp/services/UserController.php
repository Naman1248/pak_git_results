<?php

/**
 * Description of UserController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp\services;

class UserController extends \controllers\cp\SuperControlller {

    private $aggregateUGTFunction = [
        '3' => 'UGTAggregateSpecial',
        '7' => 'UGTAggregateHafiz',
        '9' => 'UGTAggregateGeneral',
        '16' => 'UGTAggregateWOTest',
        '17' => 'UGTAggregateWOTest',
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

    private function getAggregateName($baseId) {
        return $this->aggregateUGTFunction[$baseId];
    }

    public function saveAllMarksUGTAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'interTotal' => !empty($post['interTot']) ? $post['interTot'] : 0,
                        'interObt' => !empty($post['interObt']) ? $post['interObt'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $oUGTResultModel = new \models\UGTResultModel();
                $data = $oUGTResultModel->findByPK($post['appId']);
                $oUGTResultModel->UGTAggregate($data['offerId'], $data['majId'], $data['baseId'], $data['formNo']);
                $data = $oUGTResultModel->findByPK($post['appId']);
                if (!empty($data['totAgg'])) {
                    $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
                } else {
                    $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
                }
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveAllMarksInterAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $oUGTResultModel = new \models\UGTResultModel();
                $data = $oUGTResultModel->findByPK($post['appId']);
                $oUGTResultModel->InterAggregate($data['offerId'], $data['majId'], $data['baseId'], $data['formNo']);
                $data = $oUGTResultModel->findByPK($post['appId']);
                $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveAllMarksUGTWOTestAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\withoutTest\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'interTotal' => !empty($post['interTot']) ? $post['interTot'] : 0,
                        'interObt' => !empty($post['interObt']) ? $post['interObt'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $data = $oUGTResultModel->findByPK($post['appId']);
                $oUGTResultModel->UGTAggregate($data['offerId'], $data['majId'], $data['baseId'], $data['formNo']);
                $data = $oUGTResultModel->findByPK($post['appId']);
                if (!empty($data['totAgg'])) {
                    $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
                } else {
                    $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
                }
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveAllMarksInterWOTestAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\withoutTest\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $data = $oUGTResultModel->findByPK($post['appId']);
                $oUGTResultModel->InterAggregate($data['offerId'], $data['majId'], $data['baseId'], $data['formNo']);
                $data = $oUGTResultModel->findByPK($post['appId']);
                if (!empty($data['totAgg'])) {
                    $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
                } else {
                    $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
                }
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveKinshipMarksAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $out = $oUGTResultModel->upsert(
                [
                    'kinRelationTotal' => !empty($post['kinRelation']) ? $post['kinRelation'] : 0,
                    'kinInterviewTotal' => !empty($post['kinInterviewTotal']) ? $post['kinInterviewTotal'] : 0,
                    'kinInterviewObt' => !empty($post['kinInterviewObt']) ? $post['kinInterviewObt'] : 0,
                    'updatedBy' => $id,
                    'updatedOn' => date("Y-m-d H:i:s"),
                ], $post['appId']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Kinship Marks Updated.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function UGTAggregateAction() {
//    public function UGTAggregateAction($offerId, $majId, $baseId) {
        $offerId = 26;
        $majId = 1;
        $baseId = 7;
        $oUgtResultModel = new \models\UGTResultModel();
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId);
//        $oUgtResultModel->calculateMSAggregate(39, 3, 9, 11899321);
//        $oUgtResultModel->calculateMSAggregate(26, 3, 9);
    }

    public function saveReservedMeritListAction() {
        $post = $this->post()->all();
//        print_r($post);
    }

    public function saveInterviewMarksAction() {
        $post = $this->post()->all();
        $totalMarks = \helpers\Common::getTotalMarks('interview');
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUserMarksModel = new \models\UserMarksModel();

        if (empty($post['marks'])) {
            $uout = $oUserMarksModel->upsert(
                    [
                        'interviewMarks' => 0,
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $post['appId']);
            if ($uout) {
                $this->printAndDieJsonResponse(false, ['msg' => 'Successfully removed']);
            }
        } else {
            if ($post['marks'] > $totalMarks) {
                $this->printAndDieJsonResponse(false, ['msg' => 'Marks cannot greater than : ' . $totalMarks]);
            }
        }

        $out = $oUserMarksModel->upsert(
                [
                    'interviewMarks' => $post['marks'],
                    'updatedOn' => date("Y-m-d H:i:s"),
                    'updatedBy' => $id
                ], $post['appId']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Score Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveMeritListAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $data['applications'] = $oUGTResultModel->meritListByOfferIdAndByMajorIdAndBaseId($post['offerId'], $post['majorId'], $post['baseId'], $post['totalSrNo']);
        $i = 0;
        $oMeritListInfoModel = new \models\MeritListInfoModel();
        $arr = [
            'offerId' => $post['offerId'],
            'majId' => $post['majorId'],
            'baseId' => $post['baseId'],
            'meritList' => $post['meritList'],
            'totalApplicants' => $post['totalSrNo'],
            'meritListCtgry' => $post['ctgry'],
            'createdOn' => date("Y-m-d H:i:s"),
            'createdBy' => $id
        ];
        $meritListId = $oMeritListInfoModel->insert($arr);
        foreach ($data['applications'] as $row) {
            $i++;
            $out = $oUGTResultModel->upsert(
                    [
                        'meritListId' => $meritListId,
                        'meritList' => $post['meritList'],
                        'meritListCtgry' => $post['ctgry'],
                        'srNo' => $i,
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $row['appId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Merit List Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveAllMarksMSAction() {
        $post = $this->post()->all();
//        print_r($post);
//        exit;
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'interTotal' => !empty($post['interTot']) ? $post['interTot'] : 0,
                        'interObt' => !empty($post['interObt']) ? $post['interObt'] : 0,
                        'bsHonsObt' => !empty($post['bsHonsObt']) ? $post['bsHonsObt'] : 0,
                        'bsHonsTot' => !empty($post['bsHonsTot']) ? $post['bsHonsTot'] : 0,
                        'bsHonsAgg' => !empty($post['bsHonsAgg']) ? $post['bsHonsAgg'] : 0,
                        'baObt' => !empty($post['baObt']) ? $post['baObt'] : 0,
                        'baTotal' => !empty($post['baTot']) ? $post['baTot'] : 0,
                        'baDiv' => !empty($post['baDiv']) ? $post['baDiv'] : 0,
                        'baAgg' => !empty($post['baAgg']) ? $post['baAgg'] : 0,
                        'masterObt' => !empty($post['masterObt']) ? $post['masterObt'] : 0,
                        'masterTotal' => !empty($post['masterTot']) ? $post['masterTot'] : 0,
                        'masterDiv' => !empty($post['masterDiv']) ? $post['masterDiv'] : 0,
                        'masterAgg' => !empty($post['masterAgg']) ? $post['masterAgg'] : 0,
                        'baMasterAgg' => !empty($post['baMasterAgg']) ? $post['baMasterAgg'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $oUGTResultModel = new \models\UGTResultModel();
                $oUGTResultModel->calculateMSAggregatebyAppId($post['appId']);
                $data = $oUGTResultModel->findByPK($post['appId']);
//            print_r($data);
                $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveAllMarksPHDAction() {
        $post = $this->post()->all();
//        print_r($post);
//        exit;
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $meritList = $oUGTResultModel->findByPK($post['appId'], 'meritList');
        if (empty($meritList['meritList'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'matricTotal' => !empty($post['matricTot']) ? $post['matricTot'] : 0,
                        'matricObt' => !empty($post['matricObt']) ? $post['matricObt'] : 0,
                        'interTotal' => !empty($post['interTot']) ? $post['interTot'] : 0,
                        'interObt' => !empty($post['interObt']) ? $post['interObt'] : 0,
                        'bsHonsObt' => !empty($post['bsHonsObt']) ? $post['bsHonsObt'] : 0,
                        'bsHonsTot' => !empty($post['bsHonsTot']) ? $post['bsHonsTot'] : 0,
                        'bsHonsAgg' => !empty($post['bsHonsAgg']) ? $post['bsHonsAgg'] : 0,
                        'baObt' => !empty($post['baObt']) ? $post['baObt'] : 0,
                        'baTotal' => !empty($post['baTot']) ? $post['baTot'] : 0,
                        'baDiv' => !empty($post['baDiv']) ? $post['baDiv'] : 0,
                        'baAgg' => !empty($post['baAgg']) ? $post['baAgg'] : 0,
                        'masterObt' => !empty($post['masterObt']) ? $post['masterObt'] : 0,
                        'masterTotal' => !empty($post['masterTot']) ? $post['masterTot'] : 0,
                        'masterDiv' => !empty($post['masterDiv']) ? $post['masterDiv'] : 0,
                        'masterAgg' => !empty($post['masterAgg']) ? $post['masterAgg'] : 0,
                        'baMasterAgg' => !empty($post['baMasterAgg']) ? $post['baMasterAgg'] : 0,
                        'msObt' => !empty($post['msObt']) ? $post['msObt'] : 0,
                        'msTot' => !empty($post['msTot']) ? $post['msTot'] : 0,
                        'msAgg' => !empty($post['msAgg']) ? $post['msAgg'] : 0,
                        'updatedBy' => $id,
                        'updatedOn' => date("Y-m-d H:i:s"),
                    ], $post['appId']);
            if ($out) {
                $oUGTResultModel = new \models\UGTResultModel();
                $oUGTResultModel->calculatePHDAggregatebyAppId($post['appId']);
                $data = $oUGTResultModel->findByPK($post['appId']);
//            print_r($data);
                $this->printAndDieJsonResponse(true, ['msg' => 'Marks Updated', 'data' => $data]);
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function manageUserRightsAction() {
        $post = $this->post()->all();

        $oDepttUserMenuModel = new \models\cp\DepttUserMenuModel();
        $id = $oDepttUserMenuModel->findByMenuIdByDIdAndUserId($post['menuId'], $post['dId'], $post['user']);
        if ($post['verify'] == 'YES') {
            $out = $oDepttUserMenuModel->upsert(
                    [
                        'menuId' => $post['menuId'],
                        'dId' => $post['dId'],
                        'dUserId' => $post['user']
            ]);
            if ($out) {
                $this->printAndDieJsonResponse(true, ['msg' => 'Record Added', 'data' => $data]);
            } else {
                $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
            }
        } else if ($post['verify'] == 'NO' && (!empty($id))) {
            $oDepttUserMenuModel->deleteByPK($id['id']);
        }
    }

    public function insertDeleteClassBaseMajorAction() {
        $post = $this->post()->all();
        $ClassBaseMajorModel = new \models\ClassBaseMajorModel();
        if ($post['insert'] == 'YES') {
            $out = $ClassBaseMajorModel->upsert(
                    [
                        'cCode' => $post['cCode'],
                        'baseId' => $post['baseId'],
                        'name' => $post['baseName'],
                        'parentBaseId' => 0,
                        'majId' => $post['majId'],
                        'gender' => $post['gender'],
                        'year' => $post['year'],
                        'active' => 'Yes'
            ]);
            if ($out) {
                $this->printAndDieJsonResponse(true, ['msg' => 'Record Added', 'data' => $out]);
            } else {
                $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
            }
        } else if ($post['insert'] == 'NO') {
            $id = $ClassBaseMajorModel->getidByBaseByClassAndBaseAndMajorAndGender($post['cCode'], $post['baseId'], $post['majId'], $post['gender']);
            $out = $ClassBaseMajorModel->deleteByPK($id['id']);
            if ($out) {
                $this->printAndDieJsonResponse(true, ['msg' => 'Record Deleted', 'data' => $out]);
            } else {
                $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
            }
        }
    }

    public function verifyMarksAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        if ($post['verify'] == 'NO') {
            $out = $oUGTResultModel->upsert(
                    [
                        'isVerified' => $post['verify'],
                        'verifiedOn' => date("Y-m-d H:i:s"),
                        'verifiedBy' => $id
                    ], $post['appId']);
        } else {
            $agg = $oUGTResultModel->findByPK($post['appId'], 'totAgg');
            if (!empty($agg['totAgg'])) {
                $out = $oUGTResultModel->upsert(
                        [
                            'isVerified' => $post['verify'],
                            'verifiedOn' => date("Y-m-d H:i:s"),
                            'verifiedBy' => $id
                        ], $post['appId']);
            }
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Verified Status Updated to ' . $post['verify']]);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function searchFormAction() {

        $post = $this->post()->all();
        $oUserMarksModel = new \models\UserMarksModel();
        $out = $oUserMarksModel->byOfferIdAndBaseAndMajorIdFormNo($post['offerId'], $post['majorId'], $post['baseId'], $post['formNo']);
        if (empty($out)) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Invalid Form Number']);
        } else {
            $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
        }
    }

    public function getUserAction() {
        $post = $this->post()->all();
        $oUserModel = new \models\UsersModel();
        $userData = $oUserModel->findByPK($post['userid'], 'name,cnic,fatherName');
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $userData]);
    }

    public function getGATResultAction() {
        $post = $this->post()->all();
        $oGatResultModel = new \models\gatResultModel();
        $resultData = $oGatResultModel->getGATResultByRollNo($post['rn']);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $resultData]);
    }

    public function getUGTResultAction() {
        $post = $this->post()->all();
        $oUGTResultModel = new \models\UGTResultModel();
        $ugtResultData = $oUGTResultModel->getUGTResultByRollNo($post['rn']);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $ugtResultData]);
    }

    public function getFormInfoAction() {
        $post = $this->post()->all();
        $oUGTResultModel = new \models\UGTResultModel();
        $formData = $oUGTResultModel->getByFormNo($post['formNo']);
        $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $formData]);
    }

    public function getNameFatherNameByFormNoAction() {
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $appData = $oApplicationsModel->isApplicationExistForAdmission($post['offerId'], $post['majId'], $post['baseId'], $post['formNo']);
        if (empty($appData)) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Invalid Form Number.']);
        } else {
            $oUsersModel = new \models\UsersModel();
            $data = $oUsersModel->findByPK($appData['userId'], 'name,fatherName');
            $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $data]);
        }
    }

    public function getNameByFormAction() {

        $post = $this->post()->all();
        $oUGTResultModel = new \models\UGTResultModel();
        $out = $oUGTResultModel->findOneByField('formNo', $post['formNo'], 'name, fatherName, userId, appId');
        if (empty($out)) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Invalid Form Number']);
        } else {
            $this->printAndDieJsonResponse(true, ['msg' => '', 'data' => $out]);
        }
    }

    public function verifyFormMeritListInfoAction() {
        $post = $this->post()->all();
        $oUgtResultModel = new \models\UGTResultModel();
        $data = $oUgtResultModel->getMeritListByFormNo($post['formNo']);
        if (empty($data)) {
            $this->printAndDieJsonResponse(FALSE, ['msg' => 'Invalid Form Number : ' . $post['formNo']]);
        } else {
            $formStatus = $oUgtResultModel->getMeritListStatusByForm($post['formNo']);
            if (empty($formStatus)) {
                $this->printAndDieJsonResponse(TRUE, ['msg' => 'Form Number      : ' . $post['formNo'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Applicant Name : ' . $data['name']]);
            } else {
                $this->printAndDieJsonResponse(FALSE, ['msg' => 'Form Number      : ' . $post['formNo'] . ' Merit List Info does not exist.']);
            }
        }
    }

    public function verifyChallanAction() {
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $data = $oApplicationsModel->getFormNobyChallanNo($post['noch']);
        if (empty($data)) {
            $this->printAndDieJsonResponse(FALSE, ['msg' => 'Invalid Challan Number : ' . $post['noch']]);
        } else {
            $oUsersModel = new \models\UsersModel();
            $userName = $oUsersModel->findByPK($data['userId'], 'name');
//            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Form Number : ' . $data['formNo']]);
//            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Applicant Name : ' . $userName['name']]);
            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Form Number      : ' . $data['formNo'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Applicant Name : ' . $userName['name']]);
        }
    }

    public function updateChallanAction() {
        $post = $this->post()->all();
        $oApplicationsModel = new \models\ApplicationsModel();
        $data = $oApplicationsModel->getFormNobyChallanNo($post['noch']);
        $chData = $oApplicationsModel->updatePaymentStatus($data['appId'], $post['chStatus']);

        if (empty($chData)) {
            $this->printAndDieJsonResponse(FALSE, ['msg' => 'Some Internal Error Occured, Please Try Again.']);
        } else {
//            $oUsersModel = new \models\UsersModel();
//            $phoneData = $oUsersModel->findByPK($data['userId'], 'ph1');
//            foreach ($phoneData as $row) {
//                $msg = 'Dear Candidate, Your Processing Fee for GCU Online Admission has been Received.';
//                $oSmsQueueModel = new \models\cp\SmsQueueModel();
//                $row['ph1'] = '0' . ltrim($row['ph1'], 0);
//                $oSmsQueueModel->insertPhones($row['ph1'], $msg);
//            }
            $chAllData = $oApplicationsModel->updateByUserIdAndChallanId($data['userId'], $post['noch'], $post['chStatus']);
            $oChallansModel = new \models\ChallansModel();
            $challanStatus = $oChallansModel->isChallanExistByChalId($post['noch']);
            if (!empty($challanStatus)) {
                $oChallansModel->updatePaymentStatus($challanStatus['id'], $post['chStatus']);
            }
            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Challan No. ' . $post['noch'] . ' Updated Successfully.']);
//            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Challan No. ' . $post['noch'] . ' Updated Successfully for payment = ' .$post['chStatus']]);
        }
    }

    public function resetMeritListByFormNoAction() {
        $post = $this->post()->all();
        $oUgtResultModel = new \models\UGTResultModel();
        $data = $oUgtResultModel->getMeritListByFormNo($post['formNo']);
        $data = $oUgtResultModel->resetMeritInfo($data['appId'], $post['locMeritList']);
        if (empty($data)) {
            $this->printAndDieJsonResponse(FALSE, ['msg' => 'Some Internal Error Occured, Please Try Again.']);
        } else {
            $this->printAndDieJsonResponse(TRUE, ['msg' => 'Form No. ' . $post['formNo'] . ' Updated Successfully.']);
        }
    }

    private function checkValidMarks($obt, $tot) {

        if ($obt > $tot) {
            $this->printAndDieJsonResponse(false, ['msg' => 'Obtained Marks is Greater Than Total Marks.']);
        }
    }

    public function saveInterviewMarksInfoAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        if (!empty($post['interviewObt']) && !empty($post['interviewTot'])) {
            $this->checkValidMarks($post['interviewObt'], $post['interviewTot']);
            if (($post['interviewObt'] < ($post['interviewTot'] / 2))) {
//            if ($post['cCode'] == 21 && ($post['interviewObt'] < ($post['interviewTot'] / 2))) {
                $interviewResult = 0;
            } else {
                $interviewResult = 1;
            }
            $out = $oUGTResultModel->upsert(
                    [
                        'interviewObt' => $post['interviewObt'],
                        'interviewTot' => $post['interviewTot'],
                        'interviewResult' => $interviewResult,
                        'interviewAgg' => $post['interviewObt'],
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $post['appId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Interview Marks Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveTrialResultAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();

        if (($post['trialObt'] != '') && !empty($post['trialTotal'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'trialObt' => $post['trialObt'],
                        'trialTotal' => $post['trialTotal'],
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $post['appId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Trial Marks Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again for Trial Marks.']);
        }
    }

    public function updateSubjectCombinationAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oSubectCombinationModel = new \models\SubjectCombinationModel();
        if (!empty($post['sub1']) && !empty($post['sub2']) && !empty($post['sub3']) && !empty($post['setNo'])) {
            $out = $oSubectCombinationModel->upsert(
                    [
                        'sub1' => $post['sub1'],
                        'sub2' => $post['sub2'],
                        'sub3' => $post['sub3'],
                        'setNo' => $post['setNo'],
                        'stYear' => $post['stYear'],
                        'endYear' => $post['endYear']
                    ], $post['setId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Record Updated.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function addClassWiseMajorAction() {
        $post = $this->post()->all();
        var_dump($post);
        exit;
        $id = $this->state()->get('depttUserInfo')['id'];
        $oMajorsModel = new \models\MajorsModel();
    }

    public function addSubjectCombinationAction() {
        $post = $this->post()->all();
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOfferModel->findByPK($post['offerId']);
        $id = $this->state()->get('depttUserInfo')['id'];
        $oSubectCombinationModel = new \models\SubjectCombinationModel();
        if (!empty($post['sub1']) && !empty($post['sub2']) && !empty($post['sub3'])) {
//        if (!empty($post['sub1']) && !empty($post['sub2']) && !empty($post['sub3']) && !empty($post['setNo'])){
            $out = $oSubectCombinationModel->insert(
                    [
                        'sub1' => strtoupper($post['sub1']),
                        'sub2' => strtoupper($post['sub2']),
                        'sub3' => strtoupper($post['sub3']),
                        'setNo' => $post['setNo'],
                        'stYear' => $post['stYear'],
                        'endYear' => $post['endYear'],
                        'cCode' => $offerData['cCode'],
                        'gCode' => $post['gCode'],
                        'addedBy' => $id
            ]);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Record Added.']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveInterviewDateAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $contact = $oUGTResultModel->findByPK($post['appId'], 'contactNo');
        if (!empty($post['interviewDate']) && !empty($post['interviewTime']) && !empty($post['interviewVenue'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'interviewDate' => $post['interviewDate'],
                        'interviewTime' => $post['interviewTime'],
                        'interviewVenue' => $post['interviewVenue'],
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $post['appId']);
            $smsMsg = 'GCU Interview Schedule Dated : ' . $post['interviewDate'] . ' Time : ' . $post['interviewTime'] . ' Venue : ' . $post['interviewVenue'] . '. Please login for further details.';
        }
        if ($out) {
            if ($post['shiftApplication'] == 0) {
                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $oSmsQueueModel->insertPhones($contact['contactNo'], $smsMsg);
                $this->printAndDieJsonResponse(true, ['msg' => 'Interview Date Updated']);
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveInterviewDateVersion1Action() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $contact = $oUGTResultModel->findByPK($post['appId'], 'contactNo');
        $out = $oUGTResultModel->upsert(
                [
                    'interviewDate' => $post['interviewDate'],
                    'interviewTime' => $post['interviewTime'],
                    'interviewVenue' => $post['interviewVenue'],
                    'updatedOn' => date("Y-m-d H:i:s"),
                    'updatedBy' => $id
                ], $post['appId']);
        if ($out) {
            if (!empty($post['interviewDate']) && !empty($post['interviewTime']) && !empty($post['interviewVenue'])) {
                $smsMsg = 'GCU Interview Schedule Dated : ' . $post['interviewDate'] . ' Time : ' . $post['interviewTime'] . ' Venue : ' . $post['interviewVenue'] . '. Please login for further details.';
                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $oSmsQueueModel->insertPhones($contact['contactNo'], $smsMsg);
            }
            $this->printAndDieJsonResponse(true, ['msg' => 'Interview Date Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function resetInterviewDateAction() {
        $post = $this->post()->all();
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();
        $out = $oUGTResultModel->upsert(
                [
                    'interviewDate' => NULL,
                    'interviewTime' => NULL,
                    'interviewVenue' => NULL,
                    'updatedOn' => date("Y-m-d H:i:s"),
                    'updatedBy' => $id
                ], $post['appId']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Interview Date Deleted']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveTrialInfoAction() {
        $post = $this->post()->all();
//        print_r($post);exit;
        $id = $this->state()->get('depttUserInfo')['id'];
        $oUGTResultModel = new \models\UGTResultModel();

        if (!empty($post['trialDate']) && !empty($post['trialTime'])) {
            $out = $oUGTResultModel->upsert(
                    [
                        'trialDate' => $post['trialDate'],
                        'trialTime' => $post['trialTime'],
                        'trialVenue' => $post['trialVenue'],
                        'updatedOn' => date("Y-m-d H:i:s"),
                        'updatedBy' => $id
                    ], $post['appId']);
        }
        if ($out) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Trial Information Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function saveTestMarksAction() {
        $post = $this->post()->all();
        $totalMarks = \helpers\Common::getTotalMarks('test');
        $id = $this->state()->get('depttUserInfo')['id'];
        $oApplicationsModel = new \models\ApplicationsModel();
        $data = $oApplicationsModel->findByPK($post['appId'], 'userId,cCode,baseId,majId,offerId,childBase,formNo');
        $oUserMarksModel = new \models\UserMarksModel();
        $dataMarks = $oUserMarksModel->findByPK($post['appId'], 'interviewMarks');
        if (empty($dataMarks) && empty($post['marks'])) {
            $this->printAndDieJsonResponse(false, ['msg' => '']);
        }
        if (empty($post['marks']) && empty($dataMarks['interviewMarks'])) {
            $out = $oUserMarksModel->upsert([
                'testMarks' => 0,
                'updatedOn' => date("Y-m-d H:i:s"),
                'updatedBy' => $id
                    ], $post['appId']);

            if ($out) {
                $this->printAndDieJsonResponse(false, ['msg' => 'Successfully removed']);
            }
        } else {
            if ($post['marks'] > $totalMarks) {
                $this->printAndDieJsonResponse(false, ['msg' => 'Marks cannot greater than : ' . $totalMarks]);
            }
        }

        $oUserMarksModel->upsert([
            'appId' => $post['appId'],
            'userId' => $data['userId'],
            'cCode' => $data['cCode'],
            'baseId' => $data['baseId'],
            'majId' => $data['majId'],
            'offerId' => $data['offerId'],
            'childBase' => $data['childBase'],
            'formNo' => $data['formNo'],
            'testMarks' => $post['marks'],
            'updatedOn' => date("Y-m-d H:i:s"),
            'updatedBy' => $id
                ], $post['appId']);
        if ($oUserMarksModel->findByPK($post['appId'])) {
            $this->printAndDieJsonResponse(true, ['msg' => 'Score Updated']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Please try again.']);
        }
    }

    public function postLoginAction() {
        $post = $this->post()->all();
        $oDepttUserModel = new \models\cp\DepttUserModel();
        $out = $oDepttUserModel->login($post['username'], $post['passwrd']);
        if ($out) {
            $this->printAndDieJsonResponse(true, ['']);
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Invalid User Name / Password']);
        }
    }

    public function postResetPasswordAction() {
        $post = $this->post()->all();
        if (!empty($post['oldPass']) && !empty($post['newPass']) && !empty($post['newConfirmPass'])) {
            $data['id'] = $this->state()->get('depttUserInfo')['id'];
            $data['email'] = $this->state()->get('depttUserInfo')['email'];
            $DepttUserModel = new \models\cp\DepttUserModel();
            $out = $DepttUserModel->checkValidUser($data['email'], $post['oldPass']);
            if ($data['id'] == $out['id']) {
                if ($post['newPass'] != $post['newConfirmPass']) {
                    $this->printAndDieJsonResponse(false, ['msg' => 'Password/Confirm Password are not same.']);
                } else {
                    $upperCase = preg_match('@[A-Z]@', $post['newPass']);
                    $lowerCase = preg_match('@[a-z]@', $post['newPass']);
                    $number = preg_match('@[0-9]@', $post['newPass']);
                    if (!$upperCase || !$lowerCase || !$number || strlen($post['newPass']) < 8) {
                        $this->printAndDieJsonResponse(false, ['msg' => 'Password shouble be atleast 8 characters long and should contain at least one upper case letter, one lower case letter and one number.']);
                    }
                    $pk = $DepttUserModel->upsert(['paswrd' => md5($post['newPass'])], $out['id']);
                    if ($out) {
                        $this->printAndDieJsonResponse(true, ['msg' => 'Password Updated Successfully.']);
                    } else {
                        $this->printAndDieJsonResponse(false, ['msg' => 'Some Internal Error, please try again.']);
                    }
                }
            } else {

                $this->printAndDieJsonResponse(false, ['msg' => 'Invalid Old Password.']);
            }
        } else {
            $this->printAndDieJsonResponse(false, ['msg' => 'Enter Complete Information Please.']);
        }
    }

    public function loadClassesAction() {
        $post = $this->post()->all();
        $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oAdmmissionOfferModel = new \models\AdmissionOfferModel();
        $result = $oAdmmissionOfferModel->getClassesByDeptt($data['dId'], $post['yearAdmission']);
        echo json_encode($result);
    }

    public function loadInterviewDatesAction() {
        $post = $this->post()->all();
        $oUgtResultModel = new \models\UGTResultModel();
        $result['interviewDates'] = $oUgtResultModel->findInterviewDatesByOfferIdAndByMajorId($post['offerId'], $post['majorId']);
        echo json_encode($result);
    }

    public function loadTrialDatesAction() {
        $post = $this->post()->all();
        $oUgtResultModel = new \models\UGTResultModel();
        $result['trialDates'] = $oUgtResultModel->findTrialDatesByOfferIdAndByBaseId($post['offerId'], $post['baseId']);
        echo json_encode($result);
    }

    public function loadGroupsAction() {

        $post = $this->post()->all();
        $dId = $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
        $oMajorsModel = new \models\MajorsModel();
        $result['majors'] = $oMajorsModel->getMajorByOfferIdClassIdAndDId($post['offerId'], $offerData['cCode'], $dId);
//        var_dump($result);
        if (isset($post['loadBases'])) {
            $oBaseClass = new \models\BaseClassModel();
            $result['depttBases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
            $result['bases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode']);
        }
        if (isset($post['loadTestSlots'])) {
            $oTestScheduleModel = new \models\TestScheduleModel();
            $result['slots'] = $oTestScheduleModel->byOfferIdAndCityId($post['offerId'], $post['testCity']);
        }


        echo json_encode($result);
    }

    public function loadDepttBasesAction() {

        $post = $this->post()->all();
        $dId = $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode, year');

        if (isset($post['loadBases'])) {
            $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
            $result['bases'] = $oClassBaseMajorModel->getBasesByMajorDepartment($offerData['cCode'], $post['majorId'], $dId);
        }
        echo json_encode($result);
    }

    public function loadBasesAction() {

        $post = $this->post()->all();
        $dId = $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');

        if (isset($post['loadBases'])) {
            $oBaseClass = new \models\BaseClassModel();
            $result['bases'] = $oBaseClass->getBasesByClassIdAndParentBaseAndDId($dId, $offerData['cCode']);
        }
        echo json_encode($result);
    }

    public function loadScheduleAction() {

        $post = $this->post()->all();
//        $dId = $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oTestScheduleModel = new \models\TestScheduleModel();
        $result['slots'] = $oTestScheduleModel->byOfferId($post['offerId'], $post['cityId']);
        echo json_encode($result);
    }

    public function loadVenuesAction() {

        $post = $this->post()->all();
        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $result['venues'] = $oRoomsAllocationModel->getAllottedRoomsbyOfferIdAndSlotNo($post['offerId'], $post['slotNo']);
//        echo '<pre>';
//        print_r($result);exit;
        echo json_encode($result);
    }

    public function loadVenuesBySlotIdAction() {

        $post = $this->post()->all();
        $oRoomsAllocationModel = new \models\RoomsAllocationModel();
        $result['venues'] = $oRoomsAllocationModel->getAllottedRoomsbySlotId($post['slotNo']);
        echo json_encode($result);
    }

    public function loadDepartmentUsersAction() {
        $post = $this->post()->all();
        $oDepttUsersModel = new \models\cp\DepttUserModel();
        $result['depttUsers'] = $oDepttUsersModel->findByField('dId', $post['dId'], 'id,name');
//        print_r($result);exit;
        echo json_encode($result);
    }

    public function loadChildBasesAction() {

        $post = $this->post()->all();
//        var_dump($post); exit;
        $dId = $data['dId'] = $this->state()->get('depttUserInfo')['dId'];
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($post['offerId'], 'cCode');
        if (isset($post['loadBases'])) {
            $oBaseClass = new \models\BaseClassModel();
//            $result['childBases'] = $oBaseClass->getBasesByClassIdAndParentBase($offerData['cCode'], $post['admissionBase']);
            $result['childBases'] = $oBaseClass->getBasesByOfferIdAndClassIdAndParentBase($post['offerId'], $offerData['cCode'], $post['admissionBase']);
//            print_r($result['childBases']);exit;
        }
        echo json_encode($result);
    }
}
