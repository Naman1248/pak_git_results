<?php

/**
 * Description of ApplicationsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ApplicationsModel extends SuperModel {

    protected $table = 'applications';
    protected $pk = 'appId';
    protected $fields = [
        "offerId" => ['id' => 'offerId', 'label' => 'Class'],
        "majId" => ['id' => 'majorId', 'label' => 'Major'],
        "setNo" => ['id' => 'setNumber', 'label' => 'Choose Set'],
        "baseId" => ['id' => 'admissionBase', 'label' => 'Admission Base'],
        "childBase" => ['id' => 'admissionBaseChild', 'label' => 'Base Category'],
    ];

    public function rules() {
        return[
            'name,fatherName,cnic,gender,dob,add1,cityID,ph1,email,paswrd' => 'require'
        ];
    }

    public function applicationPrerequisite($userId) {
//$userData = $this->state()->get('userInfo');
        $oEducationModel = new \models\EducationModel();
        $data = $oEducationModel->findByField('userId', $userId, 'eduId');
        if (!empty($data)) {
            return true;
        } else {
            return false;
        }
    }

    public function apply($data, $userId) {

        $endDate = $this->state()->get('userInfo')['endDate'];
        $gender = $this->state()->get('userInfo')['gender'];
        $baseId = $data['admissionBase'] ?? 0;
        $childBase = $data['admissionBaseChild'] ?? 0;

        $postArr = ["offerId" => $data['offerId'],
            "majId" => $data['majorId'],
            "setNo" => $data['setNumber'],
            "baseId" => $baseId,
            "userId" => $userId,
            "addedOn" => date("Y-m-d"),
            "endDate" => $endDate
        ];

        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($data['offerId'], 'cCode, testStream, attemptNo');
        if ($offerData['cCode'] == 221) {
            die('NA');
        }
        $postArr['cCode'] = $offerData['cCode'];
        $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
        $baseData = $oClassBaseMajorModel->getBasesByClassParentBaseMajorGender($offerData['cCode'], $data['majorId'], $gender, $baseId);

        if (!empty($baseData)) {
            $postArr["childBase"] = $childBase;
        }

        $oFieldsModel = new \models\FieldsModel();
        $fields = $oFieldsModel->getFieldsOnly(FRM_APPLY, $postArr['cCode']);

        foreach ($postArr as $key => $value) {
            if (empty($value) && !in_array($key, $fields)) {
                $details = $this->getFieldLabel($key);

                return ['status' => false, 'msg' => $details['label'] . ' cannot left blank.', 'id' => $details['id']];
            } elseif (in_array($key, $fields)) {
                unset($postArr[$key]);
            }
        }
//        if ($postArr['majId'] != 80 || $postArr['majId'] != 132) {
//            if ($postArr['cCode'] == 50 || $postArr['cCode'] == 4) {
////        if ($postArr['cCode'] == 50 || $postArr['cCode'] == 4) && ($postArr['baseId'] == 9) {
//
//                $oGatResultModel = new \models\gatResultModel();
//                $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userId, $postArr['majId']);
//                if (empty($data['gatResult'])) {
//                    return ['status' => false, 'msg' => 'You are not eligible to apply because of GAT Result.', 'id' => 'errL'];
//                }
//            }
//        }
//        if (($offerData['testStream'] == 'YES') && (!empty($this->isApplicationExistByOfferId($data['offerId'], $userId)))) {
//            if (empty($this->isApplicationExistForTestStream($data['offerId'], $userId, $postArr['cCode'], $postArr['majId']))) {
//                return ['status' => false, 'msg' => 'You Can Only Apply For One Test Stream.', 'id' => 'errL'];
//            }
//        }
        if ($baseId != 20) {
            $oMajorsModel = new \models\MajorsModel();
            $majorEligibility = $oMajorsModel->getMajorEligibilityByOfferIdAndMajorId($data['offerId'], $postArr['majId']);
            if (!empty($majorEligibility)) {

                if (!$this->educationEligibility($userId, $majorEligibility, $baseId)) {
                    return ['status' => false, 'msg' => 'You cannot apply due to less percentage OR invalid Total Marks.', 'id' => 'errL'];
                }
            }
        }

        if (!empty($this->isApplicationExist($data['offerId'], $userId, $postArr['cCode'], $postArr['majId'], $baseId, $childBase))) {
            return ['status' => false, 'msg' => 'Already Applied for This Major.', 'id' => 'errL'];
        }
//        $notAllowedSets = [13, 15, 42, 43];
//        if ($baseId == 5 && !in_array($postArr['setNo'], $notAllowedSets)) {
//            return ['status' => false, 'msg' => 'You can not choose this set with Fine Arts Base.', 'id' => 'errL'];
//        }
        $all = $data; //->all();
        if (($baseId == 16 || $baseId == 17) && empty($all['performanceDetail'])) {

            return ['status' => false, 'msg' => 'Please select previous performance.', 'id' => 'errL'];
        } else {
            if (!empty($all['performanceDetail'])) {
                $postArr['baseTypeDet'] = implode(',', $all['performanceDetail']);
            }
        }
        $kinshipData = $this->handleKinship($data, $baseId, $userId);
        if (!$kinshipData['status']) {
            return $kinshipData;
        }

        $employeeBaseData = $this->handleGCUEmployeeBase($data, $baseId, $userId);
        if (!$employeeBaseData['status']) {
            return $employeeBaseData;
        }

        $overseasData = $this->handleOverseas($data, $baseId, $userId);
        if (!$overseasData['status']) {
            return $overseasData;
        }

        $postArr['version'] = $offerData['attemptNo'];
        $appId = $this->upsert($postArr);

        $chalId = 'SA' . $appId;
        $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);

        if ($pk) {
            return ['status' => true, 'msg' => 'Application submtted successfully.', 'id' => 'errL'];
        } else {
            return ['status' => false, 'msg' => 'Something went wrong, please try again.', 'id' => 'errL'];
        }
    }

    public function applyMultiBaseCtgry($data, $userId) {
//        print_r($data);exit;
        $endDate = $this->state()->get('userInfo')['endDate'];
        $gender = $this->state()->get('userInfo')['gender'];
        $baseId = $data['admissionBase'] ?? 0;
        $childBase = $data['admissionBaseChild'] ?? 0;

        $postArr = ["offerId" => $data['offerId'],
            "majId" => $data['majorId'],
            "setNo" => $data['setNumber'],
            "baseId" => $baseId,
            "userId" => $userId,
            "addedOn" => date("Y-m-d"),
            "endDate" => $endDate
        ];

        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($data['offerId'], 'cCode, testStream, attemptNo, multipleSubCtgry');
        if ($offerData['cCode'] == 221) {
            die('NA');
        }
        $postArr['cCode'] = $offerData['cCode'];
        $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
        $baseData = $oClassBaseMajorModel->getBasesByClassParentBaseMajorGender($offerData['cCode'], $data['majorId'], $gender, $baseId);

        if (!empty($baseData)) {
            $postArr["childBase"] = $childBase;
        }

        $oFieldsModel = new \models\FieldsModel();
        $fields = $oFieldsModel->getFieldsOnly(FRM_APPLY, $postArr['cCode']);

        foreach ($postArr as $key => $value) {
            if (empty($value) && !in_array($key, $fields)) {
                $details = $this->getFieldLabel($key);

                return ['status' => false, 'msg' => $details['label'] . ' cannot left blank.', 'id' => $details['id']];
            } elseif (in_array($key, $fields)) {
                unset($postArr[$key]);
            }
        }
//        if ($postArr['majId'] != 80 || $postArr['majId'] != 132) {
//            if ($postArr['cCode'] == 50 || $postArr['cCode'] == 4) {
////        if ($postArr['cCode'] == 50 || $postArr['cCode'] == 4) && ($postArr['baseId'] == 9) {
//
//                $oGatResultModel = new \models\gatResultModel();
//                $data['gatResult'] = $oGatResultModel->getPassResultByUserId($userId, $postArr['majId']);
//                if (empty($data['gatResult'])) {
//                    return ['status' => false, 'msg' => 'You are not eligible to apply because of GAT Result.', 'id' => 'errL'];
//                }
//            }
//        }
//        if (($offerData['testStream'] == 'YES') && (!empty($this->isApplicationExistByOfferId($data['offerId'], $userId)))) {
//            if (empty($this->isApplicationExistForTestStream($data['offerId'], $userId, $postArr['cCode'], $postArr['majId']))) {
//                return ['status' => false, 'msg' => 'You Can Only Apply For One Test Stream.', 'id' => 'errL'];
//            }
//        }
        if ($baseId != 20) {
            $oMajorsModel = new \models\MajorsModel();
            $majorEligibility = $oMajorsModel->getMajorEligibilityByOfferIdAndMajorId($data['offerId'], $postArr['majId']);
            if (!empty($majorEligibility)) {

                if (!$this->educationEligibility($userId, $majorEligibility, $baseId)) {
                    return ['status' => false, 'msg' => 'You cannot apply due to less percentage OR invalid Total Marks.', 'id' => 'errL'];
                }
            }
        }

        if ($baseId == 16 || $baseId == 17 || $baseId == 3 || $baseId == 36) {
            if (!empty($this->isApplicationExistMulti($data['offerId'], $userId, $postArr['cCode'], $postArr['majId'], $baseId))) {
                return ['status' => false, 'msg' => 'Already Applied for This Major.', 'id' => 'errL'];
            }
        } else {

            if (!empty($this->isApplicationExist($data['offerId'], $userId, $postArr['cCode'], $postArr['majId'], $baseId, $childBase))) {
                return ['status' => false, 'msg' => 'Already Applied for This Major.', 'id' => 'errL'];
            }
        }
//        $notAllowedSets = [13, 15, 42, 43];
//        if ($baseId == 5 && !in_array($postArr['setNo'], $notAllowedSets)) {
//            return ['status' => false, 'msg' => 'You can not choose this set with Fine Arts Base.', 'id' => 'errL'];
//        }
        $all = $data; //->all();
        if (($baseId == 16 || $baseId == 17) && empty($all['performanceDetail'])) {

            return ['status' => false, 'msg' => 'Please select previous performance.', 'id' => 'errL'];
        } else {
            if (!empty($all['performanceDetail'])) {
                $postArr['baseTypeDet'] = implode(',', $all['performanceDetail']);
            }
        }
        $kinshipData = $this->handleKinship($data, $baseId, $userId);
        if (!$kinshipData['status']) {
            return $kinshipData;
        }

        $employeeBaseData = $this->handleGCUEmployeeBase($data, $baseId, $userId);
        if (!$employeeBaseData['status']) {
            return $employeeBaseData;
        }

        $overseasData = $this->handleOverseas($data, $baseId, $userId);
        if (!$overseasData['status']) {
            return $overseasData;
        }

        $postArr['version'] = $offerData['attemptNo'];

        if (($baseId == 16 || $baseId == 17 || $baseId == 3 )) {
//        if (($baseId == 16 || $baseId == 17 || $baseId == 3 || $baseId == 36)) {
            $childBasesNew = $postArr['childBase'];
            $i = 0;
            unset($postArr['childbase']);
            foreach ($childBasesNew as $value) {
                unset($postArr['childBase']);
                $postArr['childBase'] = $value;
                if ($i == 0) {
                    $appId = $this->upsert($postArr);
                    $chalId = 'SA' . $appId;
                    $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);
                } else {
                    $appId = $this->upsert($postArr);
                    $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);
                }
                $i++;
            }
        } else {
            $appId = $this->upsert($postArr);

            $chalId = 'SA' . $appId;
            $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);
        }
        if ($pk) {
            return ['status' => true, 'msg' => 'Application submtted successfully.', 'id' => 'errL'];
        } else {
            print_r($postArr);exit;
            return ['status' => false, 'msg' => 'Something went wrong, please try again.', 'id' => 'errL'];
        }
    }

    private function educationEligibility($userId, $majorEligibility, $baseId) {
        $eligible = false;
        $reqPer = (float) ($majorEligibility[1] . (float) $majorEligibility[2]);
        $oEducationModel = new \models\EducationModel();
        $educationData = $oEducationModel->byUserIdAndExamLevelMarks($userId, $majorEligibility[0]);

//        if ($educationData['marksTot'] < 800 && ($userEduPer >= $reqPer)) {
//            $eligible = true;
//        }
        if ($educationData['degStatus'] == 'Completed') {
            $userEduPer = round(($educationData['marksObt'] / $educationData['marksTot'] * 100), 2);

            if ($baseId == 16 || $baseId == 17 || $baseId == 3 || $baseId == 36) {
                if ($userEduPer > 32) {
                    $eligible = true;
                }
            } else {
                if ($userEduPer >= $reqPer) {
                    $eligible = true;
                }
            }
        } else {
            $eligible = true;
        }

        return $eligible;
    }

    public function applyAdmin($data, $userId) {
        $baseId = $data['admissionBase'] ?? 0;
        $childBase = $data['admissionBaseChild'] ?? 0;

        $postArr = ["offerId" => $data['offerId'],
            "majId" => $data['majorId'],
            "setNo" => $data['setNumber'],
            "baseId" => $baseId,
            "userId" => $userId,
            "addedOn" => date("Y-m-d")
        ];

        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($data['offerId'], 'endDate, cCode, testStream, attemptNo');
        $postArr['cCode'] = $offerData['cCode'];
        $postArr['endDate'] = $offerData['endDate'];
        $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
        $baseData = $oClassBaseMajorModel->getBasesByClassParentBaseMajorGender($offerData['cCode'], $data['majorId'], 'Male', $baseId);

        if (!empty($baseData)) {
            $postArr["childBase"] = $childBase;
        }

        $oFieldsModel = new \models\FieldsModel();
        $fields = $oFieldsModel->getFieldsOnly(FRM_APPLY, $postArr['cCode']);

        foreach ($postArr as $key => $value) {
            if (empty($value) && !in_array($key, $fields)) {
                $details = $this->getFieldLabel($key);

                return ['status' => false, 'msg' => $details['label'] . ' cannot left blank.', 'id' => $details['id']];
            } elseif (in_array($key, $fields)) {
                unset($postArr[$key]);
            }
        }

        if (!empty($this->isApplicationExist($data['offerId'], $userId, $postArr['cCode'], $postArr['majId'], $baseId, $childBase))) {
            return ['status' => false, 'msg' => 'Already Applied for This Major.', 'id' => 'errL'];
        }
        $all = $data; //->all();
        if (($baseId == 16 || $baseId == 17) && empty($all['performanceDetail'])) {

            return ['status' => false, 'msg' => 'Please select previous performance.', 'id' => 'errL'];
        } else {
            if (!empty($all['performanceDetail'])) {
                $postArr['baseTypeDet'] = implode(',', $all['performanceDetail']);
            }
        }
        $kinshipData = $this->handleKinship($data, $baseId, $userId);
        if (!$kinshipData['status']) {
            return $kinshipData;
        }

        $employeeBaseData = $this->handleGCUEmployeeBase($data, $baseId, $userId);
        if (!$employeeBaseData['status']) {
            return $employeeBaseData;
        }

        $overseasData = $this->handleOverseas($data, $baseId, $userId);
        if (!$overseasData['status']) {
            return $overseasData;
        }

        $postArr['version'] = $offerData['attemptNo'];
        $appId = $this->upsert($postArr);
        $chalId = 'SA' . $appId;
        $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);

        if ($pk) {
            return ['status' => true, 'msg' => 'Application submtted successfully.', 'id' => 'errL'];
        } else {
            return ['status' => false, 'msg' => 'Something went wrong, please try again.', 'id' => 'errL'];
        }
    }

    public function applyTestStream($data, $userId) {
        $endDate = $this->state()->get('userInfo')['endDate'];
        $gender = $this->state()->get('userInfo')['gender'];
        $baseId = $data['admissionBase'] ?? 0;
        $childBase = $data['admissionBaseChild'] ?? 0;

        $postArr = ["offerId" => $data['offerId'],
            "majId" => $data['majorId'],
            "setNo" => $data['setNumber'],
            "baseId" => $baseId,
            "userId" => $userId,
            "addedOn" => date("Y-m-d"),
            "endDate" => $endDate
        ];
//        print_r($data['offerId']);exit;
        $oAdmissionOffer = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOffer->findByPK($data['offerId'], 'cCode, testStream, attemptNo');
        $postArr['cCode'] = $offerData['cCode'];
        $oClassBaseMajorModel = new \models\ClassBaseMajorModel();
        $baseData = $oClassBaseMajorModel->getBasesByClassParentBaseMajorGender($offerData['cCode'], $data['majorId'], $gender, $baseId);

        if (!empty($baseData)) {
            $postArr["childBase"] = $childBase;
        }

        $oFieldsModel = new \models\FieldsModel();
        $fields = $oFieldsModel->getFieldsOnly(FRM_APPLY, $postArr['cCode']);

        foreach ($postArr as $key => $value) {
            if (empty($value) && !in_array($key, $fields)) {
                $details = $this->getFieldLabel($key);

                return ['status' => false, 'msg' => $details['label'] . ' cannot left blank.', 'id' => $details['id']];
            } elseif (in_array($key, $fields)) {
                unset($postArr[$key]);
            }
        }

        if (!empty($this->isApplicationExist($data['offerId'], $userId, $postArr['cCode'], $postArr['majId'], $baseId, $childBase))) {
            return ['status' => false, 'msg' => 'Already Applied for This Major.', 'id' => 'errL'];
        }

        $baseClassModel = new \models\BaseClassModel();
        $data['test'] = $baseClassModel->getTestBaseByClassIdAndBaseId($offerData['cCode'], $baseId);

        $streamOfferIds = $this->state()->get('userInfo')['testStreamOfferIds'];
        $offerIds = explode(",", $streamOfferIds);

        if ($data['test']['test'] == 'YES') {

            $data['testStreamMajId'] = $this->findTestStreambyUserIdAndOfferIds($userId, $offerIds);
            $oGATResultModel = new \models\gatResultModel();
            $data['result'] = $oGATResultModel->getPassByOfferIdsAndMajorIdAndUserId($offerIds, $data['testStreamMajId'], $userId);
            if (empty($data['result'])) {
                return ['status' => false, 'msg' => 'You are fail and ineligible to apply.', 'id' => 'errL'];
            }
        }

        if ($baseId == 16 || $baseId == 17) {
            $performence = $this->getPreviousPerformanceByOfferIdsAndBaseIdAndUserId($offerIds, $baseId, $childBase, $userId);
            $postArr['baseTypeDet'] = $performence['baseTypeDet'];
        }

        $postArr['version'] = $offerData['attemptNo'];
        $appId = $this->upsert($postArr);
        $chalId = 'SA' . $appId;
        $pk = $this->upsert(['formNo' => $appId . date("y"), 'chalId' => $chalId], $appId);

        if ($pk) {
            return ['status' => true, 'msg' => 'Application submtted successfully.', 'id' => 'errL'];
        } else {
            return ['status' => false, 'msg' => 'Something went wrong, please try again.', 'id' => 'errL'];
        }
    }

    public function saveOALevel($_data, $userId) {
        $examLevel = $_data->preClass;
        $data = $_data->all();
//        print_r($data);exit;
        $postArr = [];
        $total = 0;
        if ($examLevel == 'O-Level') {
            $oaLevel = ['Islamic_Studies' => 'islamicStudies',
                'Pakistan_Studies' => 'pakistanStudies',
                'Urdu' => 'urdu',
                'English' => 'english',
                'Mathematics' => 'math',
                'opt1' => 'sub1',
                'opt1Grade' => 'sub1Grade',
                'opt2' => 'sub2',
                'opt2Grade' => 'sub2Grade',
                'opt3' => 'sub3',
                'opt3Grade' => 'sub3Grade'
            ];

            $grandTotal = 720;
        }

        if ($examLevel == 'A-Level') {
            $oaLevel = [
                'opt1' => 'sub1',
                'opt1Grade' => 'sub1Grade',
                'opt2' => 'sub2',
                'opt2Grade' => 'sub2Grade',
                'opt3' => 'sub3',
                'opt3Grade' => 'sub3Grade'
            ];
            $grandTotal = 270;
        }
        $totalOLevelMarks = 0;
        $olevelGradesArr = ['A*' => 90, 'A' => 85, 'B' => 75, 'C' => 65, 'D' => 55, 'E' => 45];
//        print_r($data);exit;
        foreach ($oaLevel as $k => $v) {
            if (empty($data[$k])) {
                if ($examLevel == 'O-Level') {
                    return ['status' => false, 'msg' => 'Please Enter All Information for Subjects and Grades...' . $data[$k], 'id' => 'errL'];
                }
            }
            if (!empty($data[$k])) {
                $postArr[$v] = $data[$k];
//                    echo "$data[$k]" . "_";
                if (array_key_exists($data[$k], $olevelGradesArr)) {
                    $totalOLevelMarks += $olevelGradesArr[$data[$k]];
                }
            }
        }
        $postArr['examClass'] = $examLevel;
        $postArr['total'] = $totalOLevelMarks;
        $postArr['grandTotal'] = $grandTotal;
        $postArr['userId'] = $userId;

        $oOALevelModel = new \models\OalevelModel();
        $id = $oOALevelModel->isExist($userId, $examLevel);
        if (empty($id)) {
            $postArr['addedOn'] = date("Y-m-d H:i:s");
        } else {
            $postArr['updatedOn'] = date("Y-m-d H:i:s");
        }
        $out = $oOALevelModel->upsert($postArr, $id);
        return ['status' => true, 'total' => $totalOLevelMarks, 'grandTotal' => $grandTotal];
    }

    public function handleOALEvel($_data, $baseId, $userId) {

        $data = $_data; //->all();

        if ($baseId == 1 || $baseId == 20) {

            $postArr = [];
            $total = 0;
            if ($baseId == 1) {
                $oaLevel = ['Islamic_Studies' => 'islamicStudies',
                    'Pakistan_Studies' => 'pakistanStudies',
                    'Urdu' => 'urdu',
                    'English' => 'english',
                    'Mathematics' => 'math',
                    'opt1' => 'sub1',
                    'opt1Grade' => 'sub1Grade',
                    'opt2' => 'sub2',
                    'opt2Grade' => 'sub2Grade',
                    'opt3' => 'sub3',
                    'opt3Grade' => 'sub3Grade'
                ];
            }

            if ($baseId == 20) {
                $oaLevel = [
                    'opt1' => 'sub1',
                    'opt1Grade' => 'sub1Grade',
                    'opt2' => 'sub2',
                    'opt2Grade' => 'sub2Grade',
                    'opt3' => 'sub3',
                    'opt3Grade' => 'sub3Grade'
                ];
            }
            $totalOLevelMarks = 0;
            $olevelGradesArr = ['A*' => 90, 'A' => 85, 'B' => 75, 'C' => 65, 'D' => 55, 'E' => 45];
            foreach ($oaLevel as $k => $v) {
                if (empty($data[$k])) {
                    return ['status' => false, 'msg' => 'Please Enter All Information for Subjects and Grades.', 'id' => 'errL'];
                }
                if (!empty($data[$k])) {
                    $postArr[$v] = $data[$k];
//                    echo "$data[$k]" . "_";
                    if (array_key_exists($data[$k], $olevelGradesArr)) {
                        $totalOLevelMarks += $olevelGradesArr[$data[$k]];
                    }
                }
            }
            $postArr['total'] = $totalOLevelMarks;
            $postArr['userId'] = $userId;
            $postArr['addedOn'] = date("Y-m-d H:i:s");
            $oOALevelModel = new \models\OalevelModel();
            $oOALevelModel->deleteByPK($userId);
            $out = $oOALevelModel->insert($postArr);
//var_dump($out);
        }
//exit;
        return ['status' => true];
    }

    private function handleOverseas($_data, $baseId, $userId) {
        $data = $_data;
        if ($baseId == 11) {
            $postArr = [];
            $postArr['userId'] = $userId;
            $postArr['relation'] = $data['relationOverseas'];
            $postArr['name'] = $data['relName'];
            $postArr['country'] = $data['country'];
            $postArr['passportNo'] = $data['passportNo'];
            $postArr['visaExpiryDate'] = $data['visaExpiry'];
            $postArr['addedOn'] = date("Y-m-d H:i:s");

            if (empty($postArr)) {
                return ['status' => false, 'msg' => 'Please Enter Complete Overseas information.', 'id' => 'errL'];
            }
            $oOverseasModel = new \models\OverseasModel();
            $oOverseasModel->deleteByPK($userId);
            $out = $oOverseasModel->insert($postArr);
        }
        return ['status' => true];
    }

    private function handleGCUEmployeeBase($_data, $baseId, $userId) {
        $data = $_data;
        if ($baseId == 13) {
            $postArr = [];
            $postArr['userId'] = $userId;
            $postArr['relation'] = $data['relation'];
            $postArr['employeeName'] = strtoupper($data['employeeName']);
            $postArr['depttName'] = strtoupper($data['depttName']);
            $postArr['designation'] = strtoupper($data['desig']);
            $postArr['jobStatus'] = $data['jobNature'];
            $postArr['addedOn'] = date("Y-m-d H:i:s");

            if (empty($postArr)) {
                return ['status' => false, 'msg' => 'Please Enter complete GCU Employee information.', 'id' => 'errL'];
            }
            $oEmployeeBaseModel = new \models\employeeBaseModel();
            $oEmployeeBaseModel->deleteByPK($userId);
            $out = $oEmployeeBaseModel->insert($postArr);
        }
        return ['status' => true];
    }

    private function handleKinship($_data, $baseId, $userId) {
        $data = $_data; //->all();
        if ($baseId == 15) {
            $postArr = [];
            $total = 0;
            $kin = ['kin_gmother' => 'gmMarks', 'kin_gfather' => 'gfMarks', 'kin_mother' => 'mrMarks', 'kin_father' => 'frMarks', 'kin_sister' => 'sisMarks', 'kin_brother' => 'brMarks'];
            foreach ($kin as $k => $v) {
                if (!empty($data[$k])) {
                    $postArr[$v] = $data[$k];
                    $total += $data[$k];
                }
            }
            if (empty($postArr)) {
                return ['status' => false, 'msg' => 'Please select kinship information.', 'id' => 'errL'];
            }
            if ($total > 10) {
                $total = 10;
            }
            $postArr['totalMarks'] = $total;
            $postArr['userId'] = $userId;
            $postArr['addedOn'] = date("Y-m-d H:i:s");
            $oKinshipModel = new \models\KinshipModel();
            $oKinshipModel->deleteByPK($userId);
            $out = $oKinshipModel->insert($postArr);
//            if (!$out){
//                return ['status' => false, 'msg' => 'Please Try Again.', 'id' => 'errL'];
//            }
        }
        return ['status' => true];
    }

    public function isApplicationExistForAdmission($offerId, $majId, $baseId, $formNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId, userId, childBase, isPaid, setNo, baseId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majId)
                        ->where('baseId', $baseId)
                        ->where('formNo', $formNo)
                        ->find();
    }

    public function isApplicationPaid($offerId, $majId, $baseId, $formNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId, userId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majId)
                        ->where('baseId', $baseId)
                        ->where('formNo', $formNo)
                        ->where('isPaid', 'Y')
                        ->find();
    }

    public function getPreviousPerformanceByOfferIdsAndBaseIdAndUserId($offerIds, $baseId, $childBase, $userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('baseTypeDet')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('baseId', $baseId)
                        ->where('childBase', $childBase)
                        ->where('userId', $userId)
                        ->where('isPaid', 'Y')
                        ->find();
    }

    public function isApplicationExistByOfferId($offerId, $userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId, userId')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('userId', $userId)
                        ->find();
    }

    public function updateApplicationsEndDateByOfferIdAndUserId($offerId, $userId, $endDate) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->set('endDate', $endDate . ' 19:00:00')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('userId', $userId)
                        ->update();
    }

    private function isApplicationExist(...$params) {
        $arr = ['offerId', 'userId', 'cCode', 'majId', 'baseId', 'childBase'];
        $oSQLBuilder = $this->getSQLBuilder();

        $oSQLBuilder->select('appId')
                ->from($this->table);
        foreach ($params as $key => $field) {
            $oSQLBuilder->where($arr[$key], $field);
        }
        return $oSQLBuilder->find();
    }
    private function isApplicationExistMulti(...$params) {
        $arr = ['offerId', 'userId', 'cCode', 'majId', 'baseId'];
        $oSQLBuilder = $this->getSQLBuilder();

        $oSQLBuilder->select('appId')
                ->from($this->table);
        foreach ($params as $key => $field) {
            $oSQLBuilder->where($arr[$key], $field);
        }
        return $oSQLBuilder->find();
    }

    private function isApplicationExistForTestStream(...$params) {
        $arr = ['offerId', 'userId', 'cCode', 'majId'];
        $oSQLBuilder = $this->getSQLBuilder();

        $oSQLBuilder->select('appId')
                ->from($this->table);
        foreach ($params as $key => $field) {
            $oSQLBuilder->where($arr[$key], $field);
        }
        return $oSQLBuilder->find();
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }

    public function byOfferIdAndUserId($offerId, $userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId,a.offerId,a.majId,a.cCode,name,addedOn')
                        ->from($this->table . ' a', 'majors m')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->where('a.offerId', $offerId)
                        ->where('a.userId', $userId)
                        ->findAll();
    }

    public function kinshipbyUserIdAndOfferId($userId, $offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->whereIN('baseId', [15, 46])
                        ->findAll();
    }

    public function overseasApplicationsbyUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->whereIN('baseId', [11, 30])
                        ->findAll();
    }

    public function byUserIdAndOfferIdAndMajorId($userId, $offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->where('majId', $majId)
                        ->find();
    }

    public function byUserIdAndOfferIdAndMajorIdAndBaseId($userId, $offerId, $majId, $baseId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId,formNo,isPaid')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->where('majId', $majId)
                        ->where('baseId', $baseId)
                        ->find();
    }

    public function byUserIdAndOfferIdAndBaseId($userId, $offerId, $baseId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId,formNo,isPaid')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->where('baseId', $baseId)
                        ->find();
    }

    public function byUserIdAndBaseId($userId, $baseId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('appId,formNo,isPaid')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('baseId', $baseId)
                        ->find();
    }

    public function getBaseById($id) {
        $bases = [1 => 'O-Level',
            3 => 'Disabled',
            5 => 'Fine Arts',
            6 => 'Foreign National',
            7 => 'Hifz-e-Quran',
            9 => 'Open Merit',
            11 => 'Overseas Pakistanis',
            13 => 'GCU Employee Son / Daughter',
            15 => 'Kinship',
            16 => 'Sports',
            17 => 'Co-Curricular',
            18 => 'Old Gcu Student',
            20 => 'A-Level',
            30 => 'Overseas (O/A_Level)',
            31 => 'Category-A',
            32 => 'Category-D',
            36 => 'Minorities',
            37 => 'Category-E(GCU Employee Son / Daughter)',
            41 => 'Category-S',
            42 => 'Other Provinces (Sindh)',
            43 => 'Other Provinces (Balochistan)',
            44 => 'Other Provinces (KPK)',
            45 => 'Other Provinces (Gilgit)',
            46 => 'Kinship(O-Level)',
            47 => 'Hafiz-E-Quran(O-Level)',
            69 => 'Tribal Areas Of Dg Khan',
            72 => 'Ex - FATA'
        ];
        return $bases[$id];
    }

    public function ClassStatbyOfferIdAndMajorId($offerId, $majorId) {

        $bases = [1, 3, 5, 6, 7, 9, 11, 13, 15, 16, 17, 18, 20, 30, 31, 32, 36, 37, 41, 46, 47, 69];
        $paid = $this->ClassStatbyOfferIdAndMajorIdPaid($offerId, $majorId);
        $notPaid = $this->ClassStatbyOfferIdAndMajorIdnotPaid($offerId, $majorId);
        echo "<pre>";
        $data['paid'] = $paid;
        print_r($data['paid']);
        $data['notPaid'] = $notPaid;
        print_r($data['notPaid']);
        echo " <br>";
        $finalArr = [];
        foreach ($data['paid'] as $key1 => $row1) {
            echo "paid data..";
            if (in_array($data['paid'][$key1]['baseId'], $bases)) {
                print_r($data['paid'][$key1]['baseId']);
                print_r($data['paid'][$key1]['count(formNo)']);
                echo " <br>";
            }
        }
        foreach ($data['notPaid'] as $key1 => $row1) {
            echo "not paid data..";
            if (in_array($data['notPaid'][$key1]['baseId'], $bases)) {
                print_r($data['notPaid'][$key1]['baseId']);
                print_r($data['notPaid'][$key1]['count(formNo)']);
                echo " <br>";
            }
        }
        exit;
    }

    public function yearlyStat($startDate, $endDate) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(appId) total,cCode,isPaid')
                ->from($this->table)
                ->where('addedOn', $startDate . ' 00:00:00', '>=')
                ->where('addedOn', $endDate . ' 23:59:59', '<=')
//                ->whereNotIN('cCode', [100,200])
                ->groupBy('cCode,isPaid')
                ->findAll();
//        $oSQLBuilder->printQuery();exit;
        //  echo '<pre>';
        //var_dump($data);

        $oSQLBuilder = $this->getSQLBuilder();

        $picData = $oSQLBuilder->select('count(appId) total,cCode')
                ->from($this->table)
                ->where('addedOn', $startDate . ' 00:00:00', '>=')
                ->where('addedOn', $endDate . ' 23:59:59', '<=')
//                ->whereNotIN('cCode', [100,200])
                ->whereNotNull('picExt')
                ->groupBy('cCode')
                ->findAll();
        $arr = [];
        $oAmissionOfferModel = new \models\AdmissionOfferModel();
        foreach ($data as $row) {
            if ($row['isPaid'] == 'Y') {
                $arr[$row['cCode']]['paid'] = $row['total'];
            } else {
                $arr[$row['cCode']]['notPaid'] = $row['total'];
            }

            $arr[$row['cCode']]['name'] = \helpers\Common::getClassNameById($row['cCode']);
//            $arr[$row['cCode']['name']] = $row['cCode'];
//            $arr[$row['cCode']['name']] = $oAmissionOfferModel->findOneByField('cCode', $row['cCode'], 'className');
//            echo '<pre>';
            //var_dump($oAmissionOfferModel->findOneByField('cCode', $row['cCode'], 'className'));
//            var_dump($arr[$row['cCode']['name']]);
        }
//            exit;

        foreach ($picData as $pic) {
            $arr[$pic['cCode']]['picExt'] = $pic['total'];
        }
        return $arr;
    }

    public function ClassStatbyOfferIdAndMajorIdNotPaid($offerId, $majorId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,baseId,gender')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majorId)
                ->where('a.isPaid', 'N')
//                ->whereNull('a.picExt')
                ->groupBy('a.baseId,u.gender')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['baseId']][$row['gender']] = $row['tot'];
            $arr[$row['baseId']]['base'] = $this->getBaseById($row['baseId']);
        }
        return $arr;
    }

    public function ClassStatbyOfferIdAndMajorIdPaid($offerId, $majorId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,baseId,gender')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majorId)
                ->where('a.isPaid', 'Y')
//                ->whereNotNull('a.picExt')
                ->groupBy('a.baseId,u.gender')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['baseId']][$row['gender']] = $row['tot'];
            $arr[$row['baseId']]['base'] = $this->getBaseById($row['baseId']);
        }
        return $arr;
    }

    public function ClassAllStatbyOfferId($offerId, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];

        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('count(formNo) tot,a.majId,m.name,isPaid')
                ->from($this->table . ' a', 'majors m')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('a.offerId', $offerId);
        if ($userRole == 'deptt_admin') {
            $oSQLBuilder->where('dId', $dId);
        }
        $data = $oSQLBuilder->groupBy('a.majId,m.name,a.isPaid')
                ->orderBy('m.name', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        $alias = ['Y' => 'Paid', 'N' => 'Unpaid'];
        $oUGTResultModel = new \models\UGTResultModel();
        $testPassData = $oUGTResultModel->testPassApplicantsByOfferId($offerId);
        $interviewPassData = $oUGTResultModel->interviewPassApplicantsByOfferId($offerId);
        $meritListSelectedData = $oUGTResultModel->meritListSelectedApplicantsByOfferId($offerId);
        foreach ($data as $row) {
            $arr[$row['majId']][$alias[$row['isPaid']]] = $row['tot'];
            $arr[$row['majId']]['name'] = $row['name'];
            $arr[$row['majId']]['testPass'] = $testPassData[$row['majId']];
            $arr[$row['majId']]['interviewPass'] = $interviewPassData[$row['majId']];
            $arr[$row['majId']]['meritList'] = $meritListSelectedData[$row['majId']];
        }
        return $arr;
    }

    public function ClassStatbyOfferIdPaid($offerId, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];

        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('count(formNo) tot,a.majId,m.name,gender')
                ->from($this->table . ' a', 'users u', 'majors m')
                ->join('a.userId', 'u.userId')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('a.offerId', $offerId);
        if ($userRole == 'deptt_admin') {
            $oSQLBuilder->where('dId', $dId);
        }
        $data = $oSQLBuilder->where('a.isPaid', 'Y')
//                $data = $oSQLBuilder->whereNotNull('a.picExt')
                ->groupBy('a.majId,m.name,u.gender')
                ->orderBy('m.name', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']][$row['gender']] = $row['tot'];
            $arr[$row['majId']]['name'] = $row['name'];
        }
        return $arr;
    }

    public function BaseStatbyOfferIdPaid($offerId, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,a.baseId,b.name,gender')
                ->from($this->table . ' a', 'users u', 'baseClass b')
                ->join('a.userId', 'u.userId')
                ->join('a.baseId', 'b.baseId')
                ->join('a.cCode', 'b.cCode')
                ->where('a.offerId', $offerId)
                ->where('b.parentBaseId', 0)
                ->where('a.isPaid', 'Y')
                ->groupBy('a.baseId,b.name,u.gender')
                ->orderBy('b.name', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['baseId']][$row['gender']] = $row['tot'];
            $arr[$row['baseId']]['name'] = $row['name'];
        }
        return $arr;
    }

    public function TestCentrebyOfferId($offerId, $dId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct u.userId) tot,u.testCity, gender')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('a.offerId', $offerId)
                ->where('a.isPaid', 'Y')
                ->whereNotIN('baseId', [3, 16, 17])
                ->groupBy('u.testCity, u.gender')
                ->orderBy('u.testCity', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['testCity']][$row['gender']] = $row['tot'];
            $arr[$row['testCity']]['name'] = $row['testCity'];
        }
        return $arr;
    }

    public function TestCentreAllOfferIds($admissionYear) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct u.userId) tot,ao.offerId, u.testCity, ao.className')
                ->from($this->table . ' a', 'users u', 'admissionOffer ao')
                ->join('a.userId', 'u.userId')
                ->join('a.offerId', 'ao.offerId')
                ->where('a.isPaid', 'Y')
                ->whereNotIN('baseId', [16, 17])
                ->where('ao.testCenter', 'YES')
                ->where('ao.year', $admissionYear)
                ->groupBy('ao.offerId,ao.className, u.testCity')
                ->orderBy('ao.className', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        exit;
//        echo "<pre>";
//        print_r($data);
//        exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['offerId']][$row['testCity']] = $row['tot'];
            $arr[$row['offerId']]['className'] = $row['className'];
        }
//        echo "<pre>";
//        print_r($arr);exit;
        return $arr;
    }

    public function TestCentreByOfferIdPaid($offerId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct u.userId) tot,a.majId, u.testCity, m.name')
                ->from($this->table . ' a', 'users u', 'majors m')
                ->join('a.userId', 'u.userId')
                ->join('a.offerId', 'm.offerId')
                ->join('a.majId', 'm.majId')
                ->where('a.isPaid', 'Y')
                ->where('a.offerId', $offerId)
                ->whereNotIN('a.baseId', [1, 3, 16, 17])
//                ->whereNotIN('a.baseId', [1, 3, 16, 17])
                ->groupBy('a.majId, m.name, u.testCity')
                ->orderBy('m.majId', 'ASC')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']][$row['testCity']] = $row['tot'];
            $arr[$row['majId']]['name'] = $row['name'];
        }
        return $arr;
    }

    public function TestCentreByOfferIdNotPaid($offerId) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct u.userId) tot,a.majId, u.testCity, m.name')
                ->from($this->table . ' a', 'users u', 'majors m')
                ->join('a.userId', 'u.userId')
                ->join('a.offerId', 'm.offerId')
                ->join('a.majId', 'm.majId')
                ->where('a.isPaid', 'N')
                ->whereNotIN('baseId', [3, 16, 17])
                ->where('a.offerId', $offerId)
                ->groupBy('a.majId, m.name, u.testCity')
                ->orderBy('m.majId', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        exit;
//        echo "<pre>";
//        print_r($data);
//        exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']][$row['testCity']] = $row['tot'];
            $arr[$row['majId']]['name'] = $row['name'];
        }
//        echo "<pre>";
//        print_r($arr);exit;
        return $arr;
    }

    public function BaseStatbyOfferIdNotPaid($offerId, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,a.baseId,b.name,gender')
                ->from($this->table . ' a', 'users u', 'baseClass b')
                ->join('a.userId', 'u.userId')
                ->join('a.baseId', 'b.baseId')
                ->join('a.cCode', 'b.cCode')
                ->where('a.offerId', $offerId)
                ->where('b.parentBaseId', 0)
                ->where('a.isPaid', 'N')
                ->groupBy('a.baseId,b.name,u.gender')
                ->orderBy('b.name', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['baseId']][$row['gender']] = $row['tot'];
            $arr[$row['baseId']]['name'] = $row['name'];
        }
        return $arr;
    }

    public function ClassStatbyOfferIdNotPaid($offerId, $dId) {
        $userRole = $this->state()->get('depttUserInfo')['role'];
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('count(formNo) tot,a.majId,m.name,gender')
                ->from($this->table . ' a', 'users u', 'majors m')
                ->join('a.userId', 'u.userId')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('a.offerId', $offerId);
        if ($userRole == 'deptt_admin') {
            $oSQLBuilder->where('dId', $dId);
        }
        $data = $oSQLBuilder->where('a.isPaid', 'N')
//        $data = $oSQLBuilder->whereNull('a.picExt')
                ->groupBy('a.majId,m.name,u.gender')
                ->orderBy('m.name', 'ASC')
                ->findAll();
//        $oSQLBuilder->printQuery();
//        echo "<pre>";
//        print_r($data);exit;
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']][$row['gender']] = $row['tot'];
            $arr[$row['majId']]['name'] = $row['name'];
        }
        return $arr;
    }

    public function countEmailsTestCenterByOfferId($offerId, $majId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(DISTINCT(email)) tot')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('u.centreUpdated', 'N')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.isPaid', 'Y')
                ->whereNotIN('baseId', [3, 16, 17])
                ->find();
        return $data;
    }

    public function emailsTestCenterByOfferId($offerId, $majId, $offset, $perPage) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('DISTINCT(email) em', 'name', 'u.userId')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('u.centreUpdated', 'N')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.isPaid', 'Y')
                ->whereNotIN('baseId', [3, 16, 17])
                ->limit($offset, $perPage)
                ->findAll();
        return $data;
    }

    public function byUserId($userId, $offerId) {
        $table = 'admissionOffer';
        $endDate = date("Y-m-d H:i:s"); // . '22:01:00';
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('appId,a.offerId,a.majId,a.cCode,a.baseId,a.childBase,ao.year, ao.className,baseTypeDet,m.name,b.name baseName,a.addedOn,a.picture,a.picExt,a.picBucket,a.endDate endDate, isPaid, a.isChallanGenerated, a.formNo, a.chalId, u.name userName, u.cnic')
                ->from($this->table . ' a', 'majors m', 'baseClass b', $table . ' ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('b.cCode', 'a.cCode')
                ->join('u.userId', 'a.userId')
                ->join('b.baseId', 'a.baseId');
        return $oSQLBuilder->where('a.userId', $userId)
                        ->where('b.parentBaseId', 0)
                        ->orderBy('a.appId', 'ASC')
                        ->findAll();
    }

    public function allPreReqByUserId($userId) {
        $table = 'admissionOffer';
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('GROUP_CONCAT(distinct ao.preReq) preEdu1, GROUP_CONCAT(distinct ao.preReq1) preEdu2')
                ->from($this->table . ' a', $table . ' ao')
                ->join('a.offerId', 'ao.offerId');
        return $oSQLBuilder->where('a.userId', $userId)
                        ->where('transfer', 1)
                        ->find();
    }

    public function byUserIdAndOfferId($userId, $offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.majId')
                ->from($this->table . ' a')
                ->where('a.userId', $userId)
                ->where('a.offerId', $offerId)
                ->findAll();
        return $data;
    }

    public function findAllBasesbyUserIdAndOfferIds($userId, $offerIds) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('distinct a.baseId, name')
                ->from($this->table . ' a', 'baseClass b')
                ->join('a.baseId', 'b.baseId')
                ->join('a.cCode', 'b.cCode')
                ->where('a.userId', $userId)
                ->whereIN('a.offerId', $offerIds)
                ->where('isPaid', 'Y')
                ->where('b.parentBaseId', 0)
                ->findAll();
        return $data;
    }

    public function findTestStreambyUserIdAndOfferIds($userId, $offerIds) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('majId')
                ->from($this->table . ' a')
                ->where('a.userId', $userId)
                ->whereIN('a.offerId', $offerIds)
                ->where('isPaid', 'Y')
                ->find();

        return $data['majId'];
    }

    public function byUserIdAndOfferIdAndVersion($userId, $offerId, $versionId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('a.majId')
                ->from($this->table . ' a')
                ->where('a.userId', $userId)
                ->where('a.offerId', $offerId)
                ->where('a.version', $versionId)
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[] = $row['majId'];
        }
        return $arr;
    }

    public function byUserIdOld($userId, $offerId) {
        $ouserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
        $extensionData = $ouserAdmissionOfferModel->exist($userId, $offerId);
        if (!empty($extensionData)) {
            $table = 'userAdmissionOffer';
        } else {
            $table = 'admissionOffer';
        }
        $endDate = date("Y-m-d H:i:s"); // . '22:01:00';
//        $endDate = date("Y-m-d");
        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select('appId,a.offerId,a.majId,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,a.addedOn,picture,picExt,picBucket,m.endDate majorEndDate')
                ->from($this->table . ' a', 'majors m', 'baseClass b', $table . ' ao')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId');
        if (!empty($extensionData)) {
            $oSQLBuilder->join('a.userId', 'ao.userId');
        }
        return $oSQLBuilder->where('a.userId', $userId)
                        ->where('b.parentBaseId', 0)
//                        ->where('ao.startDate', $startDate,'>=')
                        ->where('ao.endDate', $endDate, '>=')
                        ->orderBy('a.addedOn', 'DESC')
                        ->findAll();
//        $oSQLBuilder->printQuery();exit;
    }

    public function allByFilter($offerId, $baseId, $majorId, $fields = 'u.picBucket uPicBucket, u.picture uPicture, a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId);
        if (!empty($majorId)) {
            $oSQLBuilder->where('a.majId', $majorId);
        }
        return $oSQLBuilder->where('b.parentBaseId', 0)
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allByFilterPaid($offerId, $baseId, $majorId, $fields = 'u.testCity, u.picBucket uPicBucket, u.picture uPicture, a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        $oSQLBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('a.offerId', $offerId)
                ->where('a.isPaid', 'Y');
        if (!empty($baseId)) {
            $oSQLBuilder->where('a.baseId', $baseId);
        }
        if (!empty($majorId)) {
            $oSQLBuilder->where('a.majId', $majorId);
        }
        return $oSQLBuilder->where('b.parentBaseId', 0)
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allByClassAndMajorPaid($offerId, $majorId, $fields = 'a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('b.parentBaseId', 0)
                        ->where('a.isPaid', 'Y')
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allByClassAndMajor($offerId, $majorId, $isPaid, $fields = 'a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('a.isPaid', $isPaid)
                        ->whereNotIN('a.baseId', [3, 16, 17])
                        ->where('b.parentBaseId', 0)
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allPaidByClassAndMajor($offerId, $majorId, $cityId, $fields = 'u.fatherName, u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('u.testCityId', $cityId)
                        ->where('a.isPaid', 'Y')
                        ->whereNotIN('a.baseId', [3, 16, 17])
                        ->whereNull('rn')
                        ->where('b.parentBaseId', 0)
                        ->orderBy('u.userId', 'ASC')
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allPaidByMultiOfferIdsAndMajor($offerIds, $majorId, $cityId, $fields = 'u.fatherName, u.name userName,cnic, u.ph1 contactNo, email, appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->whereIN('a.offerId', $offerIds)
                        ->where('a.majId', $majorId)
                        ->where('u.testCityId', $cityId)
                        ->where('a.isPaid', 'Y')
                        ->where('b.test', 'YES')
//                        ->whereNotIN('a.baseId', [1, 3, 16, 17])
                        ->whereNull('rn')
                        ->where('b.parentBaseId', 0)
                        ->orderBy('u.userId', 'ASC')
                        ->findAll();
    }

    public function allPaidByMultiOfferIdsAndMajorCS($offerIds, $majorId, $cityId, $fields = 'u.fatherName, u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u', 'education e')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('a.userId', 'e.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->whereIN('a.offerId', $offerIds)
                        ->where('a.majId', $majorId)
                        ->where('u.testCityId', $cityId)
                        ->where('a.isPaid', 'Y')
                        ->where('e.examLevel', 2)
                        ->whereIN('e.examClass', ['FSc (Pre Medical)'])
//                        ->whereNotIN('e.examClass', ['FSc (Pre Medical)'])
                        ->whereNotIN('a.baseId', [1, 16, 17])
                        ->whereNull('rn')
                        ->where('b.parentBaseId', 0)
                        ->orderBy('u.userId', 'ASC')
                        ->findAll();
    }

    public function allPaidByOfferIdAndMajorAndBase($offerId, $majorId, $baseId, $fields = 'u.fatherName, u.name userName, gender, ph1, ph2, fatherNic, email, dob, add1, cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid, shift, a.setNo, religion, testStreamOfferIds, b.test, a.shiftApplication') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->where('a.baseId', $baseId)
                        ->where('a.isPaid', 'Y')
                        ->where('b.parentBaseId', 0)
                        ->where('a.transfer', 0)
                        ->orderBy('u.userId', 'ASC')
                        ->findAll();
    }

    public function allPaidByOfferIdAndMajor($offerId, $majorId, $fields = 'u.fatherName, u.name userName, gender, ph1, ph2, fatherNic, email, dob, add1, cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid, shift, a.setNo, religion, testStreamOfferIds, b.test, shiftApplication') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.majId', $majorId)
                        ->whereNotIN('a.baseId', [3, 16, 17])
                        ->where('a.isPaid', 'Y')
                        ->where('b.parentBaseId', 0)
                        ->where('a.transfer', 0)
                        ->orderBy('a.baseId, u.userId', 'ASC')
                        ->findAll();
    }

    public function allPaidByOfferIdAndBaseId($offerId, $baseId, $fields = 'u.fatherName, u.name userName, gender, ph1, ph2, fatherNic, email, dob, add1, cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid, shift, a.setNo, religion, testStreamOfferIds, b.test, b.depttInterview, shiftApplication') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.offerId', $offerId)
                        ->where('a.baseId', $baseId)
                        ->where('a.isPaid', 'Y')
                        ->where('b.parentBaseId', 0)
                        ->where('a.transfer', 0)
                        ->orderBy('a.baseId, u.userId', 'ASC')
                        ->findAll();
    }

    public function paidApplicantByFormNo($formNo, $fields = 'u.fatherName, u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->where('a.formNo', $formNo)
//                        ->where('u.testCityId', $cityId)
                        ->where('a.isPaid', 'Y')
                        ->whereNotIN('a.baseId', [1, 3, 16, 17])
                        ->whereNull('rn')
                        ->where('b.parentBaseId', 0)
                        ->orderBy('a.offerId, u.userId', 'ASC')
                        ->find();
// $oSQLBuilder->printQuery();exit;
    }

    public function allByClassAndMajorAndVersion($offerId, $majorId, $isPaid, $version, $fields = 'a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select($fields)
                ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('ao.offerId', 'm.offerId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.userId', 'u.userId')
                ->join('b.cCode', 'a.cCode')
                ->join('b.baseId', 'a.baseId')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majorId)
                ->where('a.isPaid', $isPaid)
                ->where('a.version', $version)
                ->whereNotIN('a.baseId', [1, 3, 16, 17])
                ->where('b.parentBaseId', 0)
                ->findAll();
        $oSQLBuilder->printQuery();
        exit;
    }

    public function allByClassAndMajorMorningEvening($offerId, $majorId, $isPaid, $fields = 'a.picBucket,a.picture,u.name userName,cnic,appId,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.baseId')
                        ->whereIN('a.offerId', [$offerId, 66])
                        ->where('a.majId', $majorId)
                        ->where('a.isPaid', $isPaid)
                        ->whereNotIN('a.baseId', [3, 16, 17])
                        ->where('b.parentBaseId', 0)
                        ->findAll();
// $oSQLBuilder->printQuery();exit;
    }

    public function allByClassBase($offerId, $baseId, $childBase, $fields = 'a.picBucket,a.picture,appId,u.name userName,u.fatherName, u.cnic,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.childBase')
                        ->where('a.offerId', $offerId)
                        ->where('a.baseId', $baseId)
                        ->where('a.childBase', $childBase)
                        ->where('b.parentBaseId', $baseId)
                        ->orderBy('a.baseId', 'ASC')
                        ->findAll();

// $oSQLBuilder->printQuery();exit;
    }

    public function allByOfferIdAndBaseAndChildbase($offerId, $baseId, $childBase, $isPaid, $fields = 'a.appId,a.picBucket,a.picture,u.name userName,u.cnic,a.offerId,a.userId,a.majId,a.formNo,a.cCode,a.baseId,a.childBase,ao.className,baseTypeDet,m.name,b.name baseName,addedOn,isPaid') {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($fields)
                        ->from($this->table . ' a', 'majors m', 'baseClass b', 'admissionOffer ao', 'users u')
                        ->join('a.cCode', 'm.cCode')
                        ->join('a.majId', 'm.majId')
                        ->join('ao.offerId', 'm.offerId')
                        ->join('a.offerId', 'ao.offerId')
                        ->join('a.userId', 'u.userId')
                        ->join('b.cCode', 'a.cCode')
                        ->join('b.baseId', 'a.childBase')
                        ->where('a.offerId', $offerId)
                        ->where('a.baseId', $baseId)
                        ->where('a.childBase', $childBase)
                        ->where('b.parentBaseId', $baseId)
                        ->where('a.isPaid', $isPaid)
                        ->orderBy('a.formNo', 'ASC')
                        ->findAll();

// $oSQLBuilder->printQuery();exit;
    }

    public function saveImage($appId, $oPostArray, $bucketId, $fileName) {
        if ($this->upsert($oPostArray, $appId)) {
//if (!empty($userData['picBucket']) && $userData['picture']) {
//unlink(UPLOAD_PATH . $userData['picBucket'] . '/' . $userData['picture']);
//}
            return true;
        }
        return false;
    }

    public function ClassStatbyDepttId($dId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,u.gender, m.name')
                ->from($this->table . ' a', 'admissionOffer ao', 'majors m', 'users u')
                ->join('a.userId', 'u.userId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('ao.year', date("Y"))
                ->where('a.isPaid', 'Y')
//                ->whereNotNull('a.picExt')
                ->where('m.dId', $dId)
                ->groupBy('m.name,u.gender')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['name']][$row['gender']] = $row['tot'];
            $arr[$row['name']]['name'] = $row['name'];
        }
        return $arr;
//        print_r($arr);exit;
    }

    public function ClassStatbyDepttIdUnPaid($dId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot,u.gender, m.name')
                ->from($this->table . ' a', 'admissionOffer ao', 'majors m', 'users u')
                ->join('a.userId', 'u.userId')
                ->join('a.offerId', 'ao.offerId')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('ao.year', date("Y"))
                ->where('a.isPaid', 'N')
//                ->whereNull('a.picExt')
                ->where('m.dId', $dId)
                ->groupBy('m.name,u.gender')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['name']][$row['gender']] = $row['tot'];
            $arr[$row['name']]['name'] = $row['name'];
        }
//        print_r($arr);exit;
        return $arr;
    }

    public function isChallanExist($challanId) {

        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('appId, offerId, userId, majId, cCode, formNo, isPaid, endDate, branchCode, transactionId')
                        ->from($this->table)
                        ->where('chalId', $challanId)
                        ->find();
    }

    public function getFormNobyChallanNo($challanId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('formNo, userId, appId')
                        ->from($this->table . ' a')
                        ->where('chalId', $challanId)
                        ->find();
    }

    public function getApplicationInfoByFormNo($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('formNo, userId, appId, isPaid, majId, baseId, rn, offerId, cCode, chalId')
                        ->from($this->table . ' a')
                        ->where('formNo', $formNo)
                        ->find();
    }

    public function getApplicationInfoByFormNoForUpdateBase($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('formNo, userId, appId, isPaid, majId, baseId, rn, offerId, cCode')
                        ->from($this->table . ' a')
                        ->where('formNo', $formNo)
                        ->whereIN('offerId', [108, 114])
                        ->whereNotIN('baseId', [3, 16, 17])
                        ->find();
    }

    public function getChallanByDate($date) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('a.userId,name,chalId,formNo,a.updatedOn')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('a.updatedOn', $date . ' 00:00:00', '>=')
                ->where('a.updatedOn', $date . ' 23:59:59', '<=')
//                ->whereNotIN('cCode', [100])
                ->where('isPaid', 'Y')
                ->findAll();
//        $oSqlBuilder->printQuery();
        return $data;
    }

    public function updatePaymentStatus($appId, $chStatus) {

        return ($this->upsert(['isPaid' => $chStatus, 'updatedOn' => date('Y-m-d H:i:s'), 'updatedBy' => 22], $appId));
//        return ($this->upsert(['isPaid' => 'Y', 'updatedOn' => date('Y-m-d H:i:s'),'updatedBy' => 99], $appId));
    }

    public function updateApplication($post) {
        echo "<pre>";
        print_r($post);
        if ($post['paid'] != $post['oldPaid']) {
            $oChallansModel = new \models\ChallansModel();
            $isExistChallan = $oChallansModel->isChallanExistByChalId($post['chalId']);
            if (!empty($isExistChallan)) {
                $oChallansModel->upsert(['isPaid' => $post['paid']], $isExistChallan['id']);
            }
            $applicationsPayment = $this->updateByUserIdAndChallanId($post['userid'], $post['chalId'], $post['paid']);
            return ($this->upsert(
                            [
                                'isPaid' => $post['paid'],
                            ], $post['appId']));
        }

        if (!empty($post['newBaseId'])) {
            return ($this->upsert(
                            [
                                'baseId' => $post['newBaseId'],
                            ], $post['appId']));
        }

        if ($post['paid'] == "Y") {
            if ($post['rn'] != $post['newRn'] && (!empty($post['newRn']) && (!empty($post['rn'])) )) {
                $post['rn'] = $post['newRn'];
                $oGatSlipModel = new \models\GatSlipModel();
                $slipData = $oGatSlipModel->findOneByField('formNo', $post['fno'], 'id');
                $out = $oGatSlipModel->upsert(
                        [
                            'rn' => $post['rn'],
                            'award' => "NO",
                            'rollNo' => $post['fRno']
                        ], $slipData['id']);
                if ($out) {

                    $oGatResultModel = new \models\gatResultModel();
                    $resultData = $oGatResultModel->findOneByField('formNo', $post['fno'], 'id');
                    if (!empty($resultData)) {
                        $del = $oGatResultModel->deleteByPK($resultData['id']);
                    }

                    return ($this->upsert(
                                    [
                                        'rn' => $post['rn'],
                                    ], $post['appId']));
                }
            }
        }

        if (($post['oldMajorId'] != $post['newMajorId']) && !empty($post['newMajorId'])) {

            $oGatSlipModel = new \models\GatSlipModel();
            $slipData = $oGatSlipModel->findOneByField('formNo', $post['fno'], 'id, offerId');

            if (!empty($slipData)) {
                $oMajorsModel = new \models\MajorsModel();
                $majorName = $oMajorsModel->getMajorNameByOfferIdAndMajorId($slipData['offerId'], $post['newMajorId']);
                $out = $oGatSlipModel->upsert(
                        [
                            'rn' => $post['newRn'],
                            'rollNo' => $post['fRno'],
                            'majId' => $post['newMajorId'],
                            'award' => "NO",
                            'major' => $majorName
                        ], $slipData['id']);

                if ($out) {

                    $oGatResultModel = new \models\gatResultModel();
                    $resultData = $oGatResultModel->findOneByField('formNo', $post['fno'], 'id');

                    if (!empty($resultData)) {
                        $del = $oGatResultModel->deleteByPK($resultData['id']);
                    }

                    return ($this->upsert(
                                    [
                                        'rn' => $post['newRn'],
                                        'majId' => $post['newMajorId'],
                                    ], $post['appId']));
                }
            }
        }
    }

    public function updateBase($post) {
        if (($post['oldBaseId'] != $post['newBaseId']) && !empty($post['newBaseId'])) {

//            $oGatSlipModel = new \models\GatSlipModel();
//            $slipData = $oGatSlipModel->findOneByField('formNo', $post['fno'], 'id, offerId');
//            if (!empty($slipData)) {
//                $oBaseClassModel = new \models\BaseClassModel();
//                $baseName = $oBaseClassModel->getBaseByClassIdAndBaseId($post['cCode'], $post['newBaseId']);
//                $out = $oGatSlipModel->upsert(
//                        [
//                            'baseId' => $post['newBaseId'],
//                            'base' => $baseName
//                        ], $slipData['id']);
//
//                if ($out) {

            return ($this->upsert(
                            [
                                'baseId' => $post['newBaseId']
                            ], $post['appId']));
//                }
//            }//slipData
        }// if empty
    }

    public function updatePaymentStatusBank($appId, $apiAppId, $post) {
        return ($this->upsert(['isPaid' => 'Y', 'updatedOn' => date('Y-m-d H:i:s'), 'updatedBy' => $apiAppId, 'transactionId' => $post['trans_id'], 'branchCode' => $post['branch_code'], 'depositDate' => $post['paid_date'], 'paidOn' => date('Y-m-d H:i:s')], $appId));
//        return ($this->upsert(['isPaid' => 'Y', 'updatedOn' => date('Y-m-d H:i:s'),'updatedBy' => 99], $appId));
    }

    public function updatePaymentByChalId($userId, $chalId, $post, $apiAppid) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('isPaid', 'Y')
                ->set('updatedOn', date('Y-m-d H:i:s'))
                ->set('updatedBy', $apiAppid)
                ->set('transactionId', $post['trans_id'])
                ->set('depositDate', $post['paid_date'])
                ->set('paidOn', date('Y-m-d H:i:s'))
                ->from($this->table)
                ->where('userId', $userId)
                ->where('chalId', $chalId)
                ->where('isPaid', 'N')
                ->update();
        return $updateResponse;
    }

    public function updateByUserIdAndChallanId($userId, $chalId, $challanStatus) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('isPaid', $challanStatus)
                ->set('updatedOn', date('Y-m-d H:i:s'))
                ->set('paidOn', date('Y-m-d H:i:s'))
                ->from($this->table)
                ->where('userId', $userId)
                ->where('chalId', $chalId)
//                ->where('isPaid', 'N')
                ->update();
        return $updateResponse;
    }

    public function resetRNByOfferIdAndMajorId($offerId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('rn', NULL)
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->update();
        return $updateResponse;
    }

    public function resetRNByMultiOfferIdsAndMajorId($offerIds, $majId, $slotId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('a.rn', NULL)
                ->from($this->table . ' a', 'gatSlip g')
                ->join('a.userId', 'g.userId')
                ->join('a.offerId', 'g.offerId')
                ->join('a.majId', 'g.majId')
                ->join('a.rn', 'g.rn')
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('g.cityId', $cityId)
                ->where('g.slotId', $slotId)
                ->update();
        return $updateResponse;
    }

    public function totalRNByOfferIdAndMajorId($offerId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $totalApplicants = $oSqlBuilder->select('count(rn) total')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majId)
                ->whereNotNull('rn')
                ->find();
        return $totalApplicants['total'];
    }

    public function totalRNByMultiOfferIdsAndMajorId($offerIds, $majId, $slotId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $totalApplicants = $oSqlBuilder->select('count(a.rn) total')
                ->from($this->table . ' a', 'gatSlip g')
                ->join('a.offerId', 'g.offerId')
                ->join('a.majId', 'g.majId')
                ->join('a.baseId', 'g.baseId')
                ->join('a.userId', 'g.userId')
                ->join('a.rn', 'g.rn')
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('g.cityId', $cityId)
                ->where('g.slotId', $slotId)
                ->whereNotNull('a.rn')
                ->find();
        return $totalApplicants['total'];
    }

    public function paidApplicantsWORNByOfferId($offerId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(appId) total, majId')
                ->from($this->table . ' a', ' users u')
                ->join('a.userId', 'u.userId')
                ->where('offerId', $offerId)
                ->where('testCityId', $cityId)
                ->where('isPaid', 'Y')
                ->whereNotIN('baseId', [1, 3, 16, 17])
//                ->whereNull('rn')
                ->groupBy('majId')
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function getTotalApplicationsAgainstChallan($chalId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->from($this->table)
                        ->where('chalId', $chalId)
                        ->count('appId');
    }

    public function getAllMajorIdsByUserIdAndChallanId($userId, $chalId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('group_concat(m.shortName) shortName')
                ->from($this->table . ' a', 'majors m')
                ->join('a.cCode', 'm.cCode')
                ->join('a.majId', 'm.majId')
                ->join('a.offerId', 'm.offerId')
                ->where('a.userId', $userId)
                ->where('a.chalId', $chalId)
                ->find();
        return $data['shortName'];
    }

    public function paidApplicantsWORNByMultiOfferIds($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(appId) total, majId')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->whereIN('offerId', $offerIds)
                ->where('u.testCityId', $cityId)
                ->where('isPaid', 'Y')
                ->whereNotIN('baseId', [1, 16, 17])
//                ->whereNull('rn')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }
}
