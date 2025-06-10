<?php

/**
 * Description of ProfessionInfoModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ProfessionInfoModel extends SuperModel {

    protected $table = 'professionalInfo';
    protected $pk = 'id';
    protected $fields = [
        "occupation" => ['id' => 'occup', 'label' => 'Applicant\'s Occupation'],
        "organization" => ['id' => 'org', 'label' => 'Name of Organization'],
        "designation" => ['id' => 'desig', 'label' => 'Designation'],
        "income" => ['id' => 'salary', 'label' => 'Monthly Income'],
        "joining" => ['id' => 'doj', 'label' => 'Date of Joinging'],
        "bps" => ['id' => 'payScale', 'label' => 'BPS / Salary Package'],
        "nature" => ['id' => 'jobnature', 'label' => 'Job Nature'],
        "type" => ['id' => 'jobtype', 'label' => 'Job Type'],
        "currentJobExperience" => ['id' => 'currentExp', ''],
        "totalExperience" => ['id' => 'totalExp', 'label' => 'Total Experience'],
        "officeAddress" => ['id' => 'offaddr', 'label' => 'Office Address'],
        "reference" => ['id' => 'ref', 'label' => 'Professional Reference'],
        "officePhone" => ['id' => 'officialContact', 'label' => 'Official Contacts'],
        "officeEmail" => ['id' => 'officialEmail', 'label' => 'Official Email']
    ];

    public function addProfession($data, $userId) {
//        var_dump($data);
    $id = $data['id'];
//    print_r($id);
//        exit;
        $postArr = [
            "occupation" => $data['occup'],
            "organization" => $data['org'],
            "designation" => $data['desig'],
            "income" => $data['salary'],
            "joining" => $data['doj'],
            "bps" => $data['payScale'],
            "nature" => $data['jobnature'],
            "type" => $data['jobtype'],
            "currentJobExperience" => $data['currentExp'],
            "totalExperience" => $data['totalExp'],
            "officeAddress" => $data['offaddr'],
            "reference" => $data['ref'],
            "officePhone" => $data['officialContact'],
            "officeEmail" => $data['officialEmail']
        ];
        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }
        $postArr["userId"] = $userId;

        if (!filter_var($data['officialEmail'], FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'id' => 'officialEmail', 'msg' => 'This is invalid email format.'];
        }
//        $professionData = $this->findOneByField('userId', $userId, 'id');
//        print_r($professionData); exit;
//        print_r( $professionData['id']); exit;

//        if (empty($professionData)) {
//            if ($this->insert($postArr)) {
//                return ['status' => true, 'msg' => 'Profession Record Submitted Successfully.'];
//            } else {
//                return ['status' => false, 'msg' => ' empty Some Internal Error Occured, Please Try Later.'];
//            }
//        } else {
////            print_r($professionData['id']); exit;
//            if ($this->upsert($postArr, $professionData['id'])) {
//
//                return ['status' => true, 'msg' => 'Profession Record Submitted Successfully.'];
//            } else {
//                return ['status' => false, 'msg' => 'empty else Some Internal Error Occured, Please Try Later.'];
//            }
//        }
    
        if ($this->upsert($postArr,$id)) {
                return ['status' => true, 'msg' => 'Profession Record Submitted Successfully.'];
            } else {
                return ['status' => false, 'msg' => ' empty Some Internal Error Occured, Please Try Later.'];
            }
    }

    public function byUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('*')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->orderBy('joining', 'DESC')
                        ->findAll();
    }

    public function findByPKAndUserId($id, $userId, $fields = '*') {
        $data = $this->findByPK($id, $fields);
        if ($data['userId'] === $userId) {
            return $data;
        } else {
            return false;
        }
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }

}
