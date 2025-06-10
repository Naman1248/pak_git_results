<?php
/**
 * Description of ResearchWorkModel
 *
 * @author SystemAnalyst
 */
namespace models;

class ResearchWorkModel extends SuperModel {

    protected $table = 'researchWork';
    protected $pk = 'id';
    protected $fields = [
        "thesisTitle" => ['id' => 'titleProject', 'label' => 'Title of Project / Thesis'],
        "researchYear" => ['id' => 'thesisYear', 'label' => 'Thesis Year'],
        "institution" => ['id' => 'inst', 'label' => 'Institution'],
    ];

    public function addResearchWork($data, $userId) {
//        print_r($data); exit;
        $id = $data['id'];
        $postArr = [
            "thesisTitle" => $data['titleProject'],
            "researchYear" => $data['thesisYear'],
            "institution" => $data['inst']
        ];
//        print_r($postArr); exit;
        
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
    