<?php

/**
 * Description of KinshipDetailModel
 *
 * @author SystemAnalyst
 */

namespace models;

class KinshipDetailModel extends SuperModel {

    protected $table = 'kinshipDetail';
    protected $pk = 'kinId';
    protected $fields = [
        "relation" => ['id' => 'kinRelation', 'label' => 'Choose Relation :'],
        "kinName" => ['id' => 'kinName', 'label' => 'Name (Kinship Relative Name):'],
        "fatherName" => ['id' => 'kinfName', 'label' => 'Father\'s Name :'],
        "cnic" => ['id' => 'kinCnic', 'label' => 'CNIC No.'],
        "degree" => ['id' => 'degreeName', 'label' => 'Select Degree / Program :'],
        "passYear" => ['id' => 'passYear', 'label' => 'Pass Year :']
    ];

    public function addKinshipDetail($data, $userId) {
        $kinId = $data->kinId;
        $postArr = [
            "relation" => $data->kinRelation,
            "kinName" => $data->kinName,
            "fatherName" => $data->kinfName,
            "cnic" => $data->kinCnic,
            "degree" => $data->degreeName,
            "passYear" => $data->passYear,
            "userId" => $userId
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }
        if (empty($kinId) && !empty($this->isKinshipDetailExist($userId, $data->kinRelation))){
            return ['status' => false, 'msg' => $this->fields['kinRelation']['label'].' Record Already Exists for this Kinship', 'id' => $this->fields['kinRelation']['id']];
        }
        $out = $this->upsert($postArr, $kinId);
        if ($out) {
            return ['status' => true, 'msg' => 'Kinship Detail Record Submited Successfully.'];
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
                        ->findAll();
    }

    public function deleteByKinId($kinId, $userId) {
        $kinData = $this->findByPKAndUserId($kinId, $userId, 'relation,userId');
        $status = $this->deleteByPK($kinId);
        return ['status' => $status, 'kinData' => $kinData];
    }

    public function findByPKAndUserId($kinId, $userId, $fields = '*') {
        $data = $this->findByPK($kinId, $fields);
        if ($data['userId'] === $userId) {
            return $data;
        } else {
            return false;
        }
    }

    public function isKinshipDetailExist($userId, $relation) {
    $oSQLBuilder = $this->getSQLBuilder();
    return $oSQLBuilder->select('kinId')
                       ->from($this->table)
                       ->where('userId', $userId)
                       ->where('relation', $relation)
                       ->find();
    }
}
