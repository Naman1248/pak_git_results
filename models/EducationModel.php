<?php

/**
 * Description of ApplicationsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class EducationModel extends SuperModel {

    protected $table = 'education';
    protected $pk = 'eduId';
    protected $fields = [
        "examLevel" => ['id' => 'preExam', 'label' => 'Exam Level'],
        "examClass" => ['id' => 'preClass', 'label' => 'Exam Level Class'],
        "degStatus" => ['id' => 'StatusDeg', 'label' => 'Degree Status'],
        "examNature" => ['id' => 'natureDeg', 'label' => 'Exam Nature'],
        "brdUni" => ['id' => 'boardUni', 'label' => 'Board / University'],
        "passYear" => ['id' => 'degYear', 'label' => 'Year of Award of Degree'],
        "regNo" => ['id' => 'regNumber', 'label' => 'Registration No.'],
        "rollNo" => ['id' => 'rollNumber', 'label' => 'Roll No.'],
        "marksTot" => ['id' => 'totalNo', 'label' => 'Total Marks / CGPA'],
        "marksObt" => ['id' => 'awardedNo', 'label' => 'Obtained Marks / CGPA'],
        "divGrade" => ['id' => 'division', 'label' => 'Division/Grade'],
        "majSub" => ['id' => 'elecSub', 'label' => 'Major Subject'],
        "schoolName" => ['id' => 'instName', 'label' => 'Institute Name'],
        "schoolStatus" => ['id' => 'instType', 'label' => 'Institute Category'],
    ];

    public function isEducationExist($userId, $examLevel) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('eduId')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('examLevel', $examLevel)
                        ->find();
    }

    public function addEducation($data, $userId) {

//        if ($data->preClass == 'ECAT' && $data->statusDeg == 'Completed') {
//            
//            if (!is_numeric($data->awardedNo) || !is_numeric($data->totalNo)) {
//                    return ['status' => false, 'msg' => 'Enter Valid Marks for UET' . $data->marksObt, 'id' => $this->fields['marksTot']['id']];
//                }
//                else
//                {
//                    $per = round(($data->awardedNo / $data->totalNo * 100), 2);
//                    if ($per < 33)
//                    {
//                       return ['status' => false, 'msg' => 'You are Ineligible to apply due to low ECAT percentage.' , 'id' => $this->fields['marksTot']['id']];
//                    }
//                }
//        }

        $eduId = $data->eduId;

        $postArr = [
            "examLevel" => $data->preExam,
            "examClass" => $data->preClass,
            "degStatus" => $data->statusDeg,
            "examNature" => $data->natureDeg,
            "brdUni" => $data->boardUni,
            "passYear" => $data->degYear,
            "regNo" => $data->regNumber,
            "rollNo" => $data->rollNumber,
//            "marksTot" => $data->totalNo,
//            "marksObt" => $data->awardedNo,
//            "divGrade" => $data->division,
            "majSub" => $data->elecSub,
            "schoolName" => $data->instName,
            "schoolName" => $data->instName,
            "schoolStatus" => $data->instType,
            "userId" => $userId
//            "addedOn" => date("Y-m-d"),
        ];

        if ($postArr['degStatus'] == 'Completed') {
////
////            print_r($data);
            if ($data->preClass == 'O-Level' || $data->preClass == 'A-Level') {
//                $postArr["marksTot"] = 'NA';
//                $postArr["marksObt"] = 'NA';
                $postArr["divGrade"] = 'NA';

                $oApplicationsModel = new \models\ApplicationsModel();
                $oaLevelData = $oApplicationsModel->saveOALevel($data, $userId);
                if (!$oaLevelData['status']) {

                    return $oaLevelData;
                } else {
                    $postArr["marksTot"] = $oaLevelData['grandTotal'];
                    $postArr["marksObt"] = $oaLevelData['total'];
                }
            } else {
                $postArr["marksTot"] = $data->totalNo;
                $postArr["marksObt"] = $data->awardedNo;
                $postArr["divGrade"] = $data->division;

                if (!is_numeric($postArr['marksObt']) || !is_numeric($postArr['marksTot'])) {
                    return ['status' => false, 'msg' => 'Enter Valid Marks', 'id' => $this->fields['marksTot']['id']];
                }

                if ($postArr['marksObt'] > $postArr['marksTot']) {
                    return ['status' => false, 'msg' => $this->fields['marksObt']['label'] . ' Cannot Greater Than Total Marks.', 'id' => $this->fields['marksObt']['id']];
                }
            }
        } else {

            $postArr["marksTot"] = 'NA';
            $postArr["marksObt"] = 'NA';
            $postArr["divGrade"] = 'NA';
        }

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }

        if (empty($eduId) && !empty($this->isEducationExist($userId, $data->preExam))) {
            return ['status' => false, 'msg' => $this->fields['examLevel']['label'] . ' Record Already Exists for This Class.', 'id' => $this->fields['examLevel']['id']];
        }
//        print_r($this->state()->get('userInfo')['cCode']);
//        exit;
//        
        if ($postArr['examLevel'] == 1 && $this->state()->get('userInfo')['cCode'] == 1) {
            if ($postArr['passYear'] < 2020) {
                return ['status' => false, 'msg' => $details['label'] . ' You cannot apply because of .', 'id' => $details['id']];
            }
        }

        $startDate = [1 => date("Y", strtotime("-5 YEAR"))];

        $this->upsert($postArr, $eduId);

        return ['status' => true, 'msg' => 'Education Record Submited Successfully.'];
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }

    public function getLast($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('eduId,a.brdUni,b.boardId,examLevel,degStatus,boardNm,passYear,rollNo,marksObt,marksTot,divGrade,majSub,schoolName,schoolStatus')
                        ->from($this->table . ' a', 'board b')
                        ->join('a.brdUni', 'b.boardId')
                        ->where('a.userId', $userId)
                        ->where('examLevel', 7, '<')
                        ->orderBy('examLevel', 'DESC')
                        ->find();
    }

    public function byUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('eduId,a.brdUni,b.boardId,examLevel,degStatus,boardNm,passYear,rollNo,marksObt,marksTot,divGrade,majSub,schoolName,schoolStatus,examClass')
                        ->from($this->table . ' a', 'board b')
                        ->join('a.brdUni', 'b.boardId')
                        ->where('a.userId', $userId)
//                        ->where('examLevel', 7, '<')
                        ->orderBy('examLevel', 'ASC')
                        ->findAll();
    }

    public function byUserIdWithoutNA($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('eduId,a.brdUni,b.boardId,examLevel,degStatus,boardNm,passYear,rollNo,marksObt,marksTot,divGrade,majSub,schoolName,schoolStatus,examClass, examNature')
                        ->from($this->table . ' a', 'board b')
                        ->join('a.brdUni', 'b.boardId')
                        ->where('a.userId', $userId)
                        ->where('marksObt', 0, '>')
                        ->where('marksTot', 0, '>')
                        ->findAll();
    }

    public function byUserIdAllEducation($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('eduId,a.brdUni,b.boardId,examLevel,degStatus,boardNm,passYear,rollNo,marksObt,marksTot,divGrade,majSub,schoolName,schoolStatus,examClass')
                ->from($this->table . ' a', 'board b')
                ->join('a.brdUni', 'b.boardId')
                ->where('a.userId', $userId)
                ->orderBy('examLevel', 'ASC')
                ->findAll();
    }

    public function byUserIdAndExamLevel($userId, $examLevel) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('eduId,a.brdUni,b.boardId,examLevel,degStatus,boardNm,passYear,rollNo,marksObt,marksTot,divGrade,majSub,schoolName,schoolStatus,examClass')
                        ->from($this->table . ' a', 'board b')
                        ->join('a.brdUni', 'b.boardId')
                        ->where('a.userId', $userId)
                        ->where('a.examLevel', $examLevel)
                        ->findAll();
    }

    public function byUserIdAndExamLevelMarks($userId, $examLevel) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('marksObt, marksTot, degStatus')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('examLevel', $examLevel)
//                        ->where('degStatus', 'Completed')
                        ->find();
    }

    public function deleteByEduId($eduId, $userId) {
        $eduData = $this->findByPKAndUserId($eduId, $userId, 'examClass,userId, examLevel');
//        $validate = $this->validForDelete($userId, $eduData['examLevel']);
//        if ($validate) {
            if ($eduData['examClass'] == 'A-Level' || $eduData['examClass'] == 'O-Level') {
                $ooaLevelModel = new \models\OalevelModel();
                $ooaLevelModel->deleteByUserIdAndExamLevel($eduData['userId'], $eduData['examClass']);
            }
            $status = $this->deleteByPK($eduId);
//        }
        return ['status' => $status, 'eduData' => $eduData];
    }

    public function findByPKAndUserId($eduId, $userId, $fields = '*') {
        $data = $this->findByPK($eduId, $fields);
        if ($data['userId'] === $userId) {
            return $data;
        } else {
            return false;
        }
    }

    private function validForDelete($userId, $examLevel) {
        $oApplicationsModel = new \models\ApplicationsModel();
        $preEducation = $oApplicationsModel->allPreReqByUserId($userId);
        if (empty($preEducation)) {
            return true;
        }
        $arr1 = explode(',', $preEducation['preEdu1']);
        $arr2 = explode(',', $preEducation['preEdu2']);
        $arr = array_merge($arr1, $arr2);
        foreach ($arr as $pre) {
            if ($pre == $examLevel) {
                return false;
            }
        }
    }

    public function preReq($userId, $preReq) {
        $oSqlBuilder = $this->getSQLBuilder();
        $examLevel = $oSqlBuilder->select('GROUP_CONCAT(examLevel) examClasses')
                ->from($this->table)
                ->where('userId', $userId)
                ->find();
        $arr = explode(',', $examLevel['examClasses']);
        $currentPreReq = explode(',', $preReq);
        foreach ($currentPreReq as $pre) {
            if (!in_array($pre, $arr)) {
                return false;
            }
        }
        return true;
    }

    public function preReqDiff($userId, $preReq) {
        $oSqlBuilder = $this->getSQLBuilder();
        $examLevel = $oSqlBuilder->select('GROUP_CONCAT(examLevel) examClasses')
                ->from($this->table)
                ->where('userId', $userId)
                ->find();
        $arr = explode(',', $examLevel['examClasses']);
//        exit;

        $currentPreReq = explode(',', $preReq);
//        print_r($preReq);
//        exit;
        $diff = [];
        foreach ($currentPreReq as $pre) {
            if (!in_array($pre, $arr)) {
                $diff[] = \helpers\Common::getClassById($pre);
            }
        }
        $tot = count($diff);
        if ($tot == 0) {
            return '';
        }
        if ($tot == 1) {
            return implode(', ', $diff) . ' is missing';
        } else {
            return implode(', ', $diff) . ' are missing';
        }
    }
}
