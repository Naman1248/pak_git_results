<?php

/**
 * Description of AcademicReferencesModel
 *
 * @author SystemAnalyst
 */

namespace models;

class AcademicReferencesModel extends SuperModel {

    protected $table = 'academicReferences';
    protected $pk = 'id';
    protected $fields = [
        "name" => ['id' => 'refName', 'label' => 'Name'],
        "designation" => ['id' => 'desig', 'label' => 'Designation'],
        "address" => ['id' => 'addr', 'label' => 'Address'],
        "contact" => ['id' => 'ph', 'label' => 'Contact No.'],
        "email" => ['id' => 'refEmail', 'label' => 'Email']
    ];

    public function addReference($data, $userId) {
//        print_r($data); exit;

        $id = $data['id'];
        $postArr = [
            "name" => $data['refName'],
            "designation" => $data['desig'],
            "address" => $data['addr'],
            "contact" => $data['ph'],
            "email" => $data['refEmail']
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }

        if (!filter_var($data['refEmail'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'id' => 'refEmail', 'msg' => 'This is invalid email format.'];
        }
        $postArr["userId"] = $userId;
        $postArr['addedOn'] = date("Y-m-d H:i:s");
        if ($this->upsert($postArr, $id)) {
            return ['status' => true, 'msg' => 'Academic Reference Record Submitted Successfully.'];
        } else {
            return ['status' => false, 'msg' => 'Some Internal Error Occured, Please Try Later.'];
        }
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }

    public function findByPKAndUserId($id, $userId, $fields = '*') {
        $data = $this->findByPK($id, $fields);
        if ($data['userId'] === $userId) {
            return $data;
        } else {
            return false;
        }
    }

}
