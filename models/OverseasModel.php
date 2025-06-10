<?php

/**
 * Description of OverseasModel
 *
 * @author SystemAnalyst
 */

namespace models;

class OverseasModel extends SuperModel {

    protected $table = 'overseas';
    protected $pk = 'id';
        protected $fields = [
        "relation" => ['id' => 'relationOverseas', 'label' => 'Choose Relation :'],
        "name" => ['id' => 'relName', 'label' => 'Name:'],
        "country" => ['id' => 'country', 'label' => 'Choose Country:'],
        "passportNo" => ['id' => 'passportNo', 'label' => 'Passport No.:'],
        "visaExpiryDate" => ['id' => 'visaExpiry', 'label' => 'Visa Expiry Date (dd-mm-yyyy):']
    ];

        public function addOverseasDetail($data, $userId) {
            $id = $data->overseasId;
            $postArr = [
            "relation" => $data->relationOverseas,
            "name" => $data->relName,
            "country" => $data->country,
            "passportNo" => $data->passportNo,
            "visaExpiryDate" => $data->visaExpiry,
            "userId" => $userId,
            "addedOn" => date("Y-m-d")
        ];
        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }
        
        $out = $this->upsert($postArr, $id);
        if ($out) {
            return ['status' => true, 'msg' => 'Overseas Record Submited Successfully.'];
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
