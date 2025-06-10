<?php

/**
 * Description of ResearchObjectiveModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ResearchObjectiveModel extends SuperModel {

    protected $table = 'researchObjective';
    protected $pk = 'id';
    protected $fields = [
        "career" => ['id' => 'careerResearch', 'label' => 'Career Objectives'],
        "gist" => ['id' => 'gistResearch', 'label' => 'Gist of Research']
    ];

    public function addResearchObjective($data, $userId) {
        $postArr = [
            "career" => $data['careerResearch'],
            "gist" => $data['gistResearch']
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }

        $postArr["userId"] = $userId;
        $reserachObjectiveData = $this->findOneByField('userId', $userId, 'id');
        if (empty($reserachObjectiveData)) {
            if ($this->insert($postArr)) {
                return ['status' => true, 'msg' => 'Research Objective Record Submitted Successfully.'];
            } else {
                return ['status' => false, 'msg' => 'Some Internal Error Occured, Please Try Later.'];
            }
        } else {
//            print_r($professionData['id']); exit;
            if ($this->upsert($postArr, $reserachObjectiveData['id'])) {
                return ['status' => true, 'msg' => 'Research Objective Record Submitted Successfully.'];
            } else {
                return ['status' => false, 'msg' => 'Some Internal Error Occured, Please Try Later.'];
            }
        }
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }
}
