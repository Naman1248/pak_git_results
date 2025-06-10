<?php

/**
 * Description of TestInfoModel
 *
 * @author SystemAnalyst
 */

namespace models;

class TestInfoModel extends SuperModel {

    protected $table = 'testInfo';
    protected $pk = 'id';
    protected $fields = [
        "conductedBy" => ['id' => 'conductedBy', 'label' => 'Test Conducted By:'],
        "obtScore" => ['id' => 'obtScore', 'label' => 'Test Obtained Score:'],
        "totalScore" => ['id' => 'totalScore', 'label' => 'Test Total Score:'],
        "percentile" => ['id' => 'percentile', 'label' => 'Percentile Score:'],
        "rollNo" => ['id' => 'rollNo', 'label' => 'Roll Number:'],
        "subject" => ['id' => 'subject', 'label' => 'Subject:'],
        "testDate" => ['id' => 'testDate', 'label' => 'Test Date (dd-mm-yyyy):'],
        "validUpto" => ['id' => 'validUpto', 'label' => 'Test Valid Upto (dd-mm-yyyy):']
    ];

    public function addTestInfo($data, $userId) {
        $id = $data->testInfoId;
        $postArr = [
            "conductedBy" => $data->conductedBy,
            "obtScore" => $data->obtScore,
            "totalScore" => $data->totalScore,
            "percentile" => $data->percentile,
            "rollNo" => $data->rollNo,
            "subject" => strtoupper($data->subject),
            "testDate" => $data->testDate,
            "validUpto" => $data->validUpto,
            "userId" => $userId,
            "addedOn" => date("Y-m-d")
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }
        
        $upperCase = preg_match('@[A-Z]@', $data->testDate);
        $lowerCase = preg_match('@[a-z]@', $data->testDate);

        if ($upperCase || $lowerCase || strlen($data->testDate) >10) {
            return ['status' => false, 'msg' => 'Date Should Be in Valid Format.', 'id' => $this->fields['testDate']['id']];
        }

        if (!is_numeric($postArr['obtScore']) || !is_numeric($postArr['totalScore'])) {
            return ['status' => false, 'msg' => 'Enter Valid Marks', 'id' => $this->fields['totalScore']['id']];
        }
        if ($postArr['obtScore'] > $postArr['totalScore']) {
            return ['status' => false, 'msg' => 'Enter Valid Marks', 'id' => $this->fields['totalScore']['id']];
        }

        $out = $this->upsert($postArr, $id);
        if ($out) {
            return ['status' => true, 'msg' => 'Test Record Submited Successfully.'];
        } else {
            return ['status' => false, 'msg' => 'Record could not save, Try again......'];
        }
    }

    private function getFieldLabel($field) {

        return $this->fields[$field];
    }

    public function byUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('*')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->find();
    }

}
