<?php

/**
 * Description of UsersModel
 *
 * @author SystemAnalyst
 */

namespace models;

class internationalInfoModel extends SuperModel {

    protected $table = 'internationalInfo';
    protected $pk = 'id';
    protected $fields = [
        "passportNo" => ['id' => 'passportNumber', 'label' => 'Passport Number'],
        "passportIssueDate" => ['id' => 'passportIssuedDate', 'label' => 'Passport Issue Date: (dd-mm-yy)'],
        "passportExpiryDate" => ['id' => 'passportExpiredDate', 'label' => 'Passport Expire Date: (dd-mm-yy)'],
        "passportIssuePlace" => ['id' => 'passportIssuedPlace', 'label' => 'Issue of Passport\'s Place'],
        "visaType" => ['id' => 'visaCategory', 'label' => 'Visa Type'],
        "visaPakNo" => ['id' => 'visaPakistanNo', 'label' => 'Pakistani Visa Number'],
        "visaIssueDate" => ['id' => 'visaIssuedDate', 'label' => 'Visa Issuance Date: (dd-mm-yy)'],
        "visaExpiryDate" => ['id' => 'visaExpiredDate', 'label' => 'Visa Expire Date (dd-mm-yy)'],
        "placeOfBirth" => ['id' => 'placeBirth', 'label' => 'Place Of Birth'],
    ];

    public function addInternationalDetail($data, $userId) {
        $id = $data['intlInfoId'];
        $postArr = [
            "passportNo" => $data['passportNumber'],
            "passportIssueDate" => $data['passportIssuedDate'],
            "passportExpiryDate" => $data['passportExpiredDate'],
            "passportIssuePlace" => $data['passportIssuedPlace'],
            "visaType" => $data['visaCategory'],
            "visaPakNo" => $data['visaPakistanNo'],
            "visaIssueDate" => $data['visaIssuedDate'],
            "visaExpiryDate" => $data['visaExpiredDate'],
            "placeOfBirth" => $data['placeBirth'],
            "userId" => $userId,
            "addedOn" => date("Y-m-d")
        ];

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }

        $postArr["fatherPassportNo"] = $data['fatherPassport'];
        $postArr["fatherAddr"] = $data['fatherAddress'];
        $postArr["motherPassportNo"] = $data['motherPassport'];
        $postArr["motherAddr"] = $data['motherAddress'];
        $postArr["lastUniversity"] = $data['perUniversity'];
        $postArr["lastProgram"] = $data['perProgram'];
        $postArr["lastPassYear"] = $data['passYear'];
        $postArr["hecNocNo"] = $data['hecNocNumber'];
        $postArr["hecDate"] = $data['hecDate'];
        $postArr["lastMajorSubject"] = $data['perSubject'];
        $postArr["lastDegreeStatus"] = $data['perDegreeStatus'];
        $postArr["sponsorName"] = $data['spName'];
        $postArr["sponsorAddr"] = $data['spAddress'];
        $postArr["sponsorEmail"] = $data['spEmail'];
        $postArr["sponsorPhone"] = $data['spPhone'];
        $postArr["sponsorNature"] = $data['spNature'];

        if (!empty($data['schInfo'])) {
            $postArr['scholarshipInfo'] = implode(',', $data['schInfo']);
        }
        
        $out = $this->upsert($postArr, $id);
        if ($out){
            return ['status' => true, 'msg' => 'Information Submited Successfully.'];
        } else {
            return ['status' => false, 'msg' => 'Record could not save, Try again......'];
        }
    }

    private function getFieldLabel($field) {

        return $this->fields[$field];
    }

//   
}
