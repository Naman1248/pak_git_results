<?php

/**
 * Description of PublicationsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class PublicationsModel extends SuperModel {

    protected $table = 'publications';
    protected $pk = 'id';
    protected $fields = [
        "researchTitle" => ['id' => 'resTitle', 'label' => 'Research Title'],
        "journalName" => ['id' => 'journalnm', 'label' => 'Journal Name'],
        "publicationYear" => ['id' => 'pubYear', 'label' => 'Publication Year'],
        "volume" => ['id' => 'vol', 'label' => 'Volume'],
        "pageNo" => ['id' => 'pageNo', 'label' => 'Page Number']
    ];

    public function addPublication($data, $userId) {
//        print_r($data); exit;
        
        $id = $data['id'];
        $postArr = [
            "researchTitle" => $data['resTitle'],
            "journalName" => $data['journalnm'],
            "publicationYear" => $data['pubYear'],
            "volume" => $data['vol'],
            "pageNo" => $data['pageNo']
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }
        $postArr["userId"] = $userId;
        $postArr['addedOn'] = date("Y-m-d H:i:s");
        if ($this->upsert($postArr, $id)) {
            return ['status' => true, 'msg' => 'Publication Record Submitted Successfully.'];
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
    