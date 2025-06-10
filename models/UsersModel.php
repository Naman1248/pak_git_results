<?php

/**
 * Description of UsersModel
 *
 * @author SystemAnalyst
 */

namespace models;

class UsersModel extends SuperModel {

    protected $table = 'users';
    protected $pk = 'userId';
    protected $fields = ["name" => ['id' => 'name', 'label' => 'Name'],
        "fatherName" => ['id' => 'fname', 'label' => 'Father\'s Name'],
        "cnic" => ['id' => 'cnic', 'label' => 'CNIC'],
        "gender" => ['id' => 'gender', 'label' => 'Gender'],
        "dob" => ['id' => 'dob', 'label' => 'Date of Birth'],
        "add1" => ['id' => 'addr', 'label' => 'Address'],
        "cityId" => ['id' => 'city', 'label' => 'City'],
        "ph1" => ['id' => 'ph', 'label' => 'Phone'],
        "email" => ['id' => 'email', 'label' => 'Email'],
        "paswrd" => ['id' => 'pswrd', 'label' => 'Password'],
        "countryID" => ['id' => 'country', 'label' => 'Nationality'],
        "ph2" => ['id' => 'fph', 'label' => 'Father/Guradian\'s Contact Number'],
        "add2" => ['id' => 'permadd', 'label' => 'Permanent Address'],
        "religion" => ['id' => 'relig', 'label' => 'Religion'],
//        "caste" => ['id' => 'appCaste', 'label' => 'Caste'],
        "area" => ['id' => 'belongs', 'label' => 'Caste'],
        "fatherNic" => ['id' => 'fcnic', 'label' => 'Belongs To :'],
        "motherNic" => ['id' => 'mcinc ', 'label' => 'Mother\'s CNIC No.'],
        "provinceId" => ['id' => 'province', 'label' => 'Province'],
        "tehsilId" => ['id' => 'tehsil', 'label' => 'Tehsil'],
        "fatherOccp" => ['id' => 'foccp', 'label' => 'Father\'s Occupation'],
        "fatherIncome" => ['id' => 'fincom', 'label' => 'Father\'s Monthly Income'],
        "districtId" => ['id' => 'district', 'label' => 'District'],
        "fatherQual" => ['id' => 'fqual', 'label' => 'Father\'s Qualification'],
        "fatherStatus" => ['id' => 'fastatus', 'label' => 'Father\'s Status'],
        "fatherNationality" => ['id' => 'fnat', 'label' => 'Father\'s Nationality'],
        "fatherOffice" => ['id' => 'foffice', 'label' => 'Father\'s Office Address'],
        "fatherEmail" => ['id' => 'femail', 'label' => 'Father\'s Email'],
        "motherQual" => ['id' => 'mqual', 'label' => 'Mother\'s Qualification'],
        "motherOccp" => ['id' => 'moccp', 'label' => 'Mother\'s Occupation'],
        "motherIncome" => ['id' => 'mincom', 'label' => 'Mother\'s Monthly Income'],
        "motherName" => ['id' => 'mname', 'label' => 'Mother\'s Name'],
        "motherStatus" => ['id' => 'mostatus', 'label' => 'Mother\'s Status'],
        "motherNationality" => ['id' => 'mnat', 'label' => 'Mother\'s Nationality'],
        "motherOffice" => ['id' => 'moffice', 'label' => 'Mother\'s Office Address'],
        "motherEmail" => ['id' => 'memail', 'label' => 'Mother\'s Email'],
        "ph3" => ['id' => 'mph', 'label' => 'Mother\'s Contact Number'],
        "experience" => ['id' => 'profexp', 'label' => 'Professional Experience'],
        "source" => ['id' => 'sourceOf', 'label' => 'How did you come to know about us']
    ];

    public function validateConfirmKey($email, $id) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('userId,name,email,cofirmationKey')
                ->from($this->table)
                ->where('email', $email)
                ->where('cofirmationKey', $id)
                ->find();
        return $data;
    }

    public function updateProfile($data) {

        if ($data['password'] == 'Y') {
            $postArr['paswrd'] = md5($data['cnic']);
        }
        $postArr['cnic'] = $data['cnic'];
        $postArr['email'] = $data['email'];
        $postArr['ph1'] = $data['ph1'];
        $postArr['dob'] = $data['dob'];
        $postArr['gender'] = $data['gender'];

        return ($this->upsert($postArr, $data['userid']));
    }

    public function validateForgotKey($email, $id, $field = 'email') {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('userId,name,email,forgotKeyExpiry')
                ->from($this->table)
                ->where($field, $email)
                ->where('forgotKey', $id)
                ->find();
        if (!empty($data)) {
            if ((time() - strtotime($data['forgotKeyExpiry'])) / 86400 <= 1) {
                return $data;
            } else {
                return false;
            }
        } else {
            return $data;
        }
    }

    public function rules() {
        return[
            'name,fatherName,cnic,gender,dob,add1,cityID,ph1,email,paswrd' => 'require'
        ];
    }

    public function login($email, $password) {
//        echo $email.','. $password;
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('userId,name,cnic,ph1,email,countryID,dob,ph2,add1,fatherName,add2,gender,religion,
                                      fatherNic,motherNic,familyNo,provinceId,tehsilId,fatherOccp,fatherIncome,
                                      districtId,cityId,stateCity,fatherQual,motherQual,motherOccp,motherName,
                                      motherIncome,area,picture,picBucket,experience, testCity')
                ->from($this->table)
                ->where('email', $email)
                ->where('paswrd', md5($password))
                ->where('isConfirmed', 'Y')
                ->find();
//        $oSQLBuilder->printQuery();

        if (empty($data)) {
            return false;
        } else {
            $data['imageUrl'] = $this->getUserImageURL($data);
            $this->state()->set('userInfo', $data);
            return true;
        }
    }

    public function setSessionData($userId, $params = []) {
        $oSQLBuilder = $this->getSQLBuilder();
//            $data = $oSQLBuilder->select('userId,name,fatherName,cnic,gender,dob,add1,cityId,ph1,email,picture,picBucket')
        $data = $oSQLBuilder->select('userId,name,cnic,ph1,email,countryID,dob,ph2,add1,fatherName,add2,gender,religion,
                                      fatherNic,motherNic,familyNo,provinceId,tehsilId,fatherOccp,fatherIncome,
                                      districtId,cityId,stateCity,fatherQual,motherQual,motherOccp,motherName,
                                      motherIncome,area,picture,picBucket')
                ->from($this->table)
                ->where('userId', $userId)
                ->find();
        if (empty($data)) {
            return false;
        }
        if (!empty($params)) {
            $data = array_merge($data, $params);
        }
//        var_dump($data);exit;
        $this->state()->set('userInfo', $data);
    }

    public function logout() {
        $this->state()->deleteAll();
        $this->redirect(SITE_URL . 'user/login');
    }

    public function signup($data) {
        $postArr = [
            "name" => strtoupper($data->name),
            "fatherName" => strtoupper($data->fname),
            "cnic" => $data->cnic,
            "gender" => $data->gender,
            "dob" => $data->dob,
            "add1" => strtoupper($data->addr),
            "cityId" => $data->city,
            "ph1" => $data->ph,
            "email" => $data->email,
            "paswrd" => $data->pswrd,
            "source" => $data->sourceOf
        ];

        $upperCase = preg_match('@[A-Z]@', $data->pswrd);
        $lowerCase = preg_match('@[a-z]@', $data->pswrd);
        $number = preg_match('@[0-9]@', $data->pswrd);

        $postArr['add1'] = preg_replace('/[^A-Za-z0-9 ]/', '', $data->addr);

        foreach ($postArr as $key => $value) {
            if (empty($value)) {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot blank.', 'id' => $details['id']];
            }
        }

        if (!is_numeric($postArr['cnic'])) {
            return ['status' => false, 'msg' => 'Enter Valid CNIC', 'id' => $this->fields['cnic']['id']];
        }

        if (!is_numeric($postArr['ph1'])) {
            return ['status' => false, 'msg' => 'Enter Valid Phone in Number', 'id' => $this->fields['ph1']['id']];
        }
        if (!$upperCase || !$lowerCase || !$number || strlen($data->pswrd) < 8) {
            return ['status' => false, 'msg' => 'Password shouble be atleast 8 characters long and should contain at least one upper case letter, one lower case letter and one number.', 'id' => $this->fields['paswrd']['id']];
        }
//        if (strlen($data->pswrd) < 8) {
//            return ['status' => false, 'msg' => 'Password shouble be atleast 8 characters long.', 'id' => $this->fields['pswrd']['id']];
//        }
//        elseif (!preg_match("#[0-9]+#",$data->pswrd)){
//            return ['status' => false, 'msg' => 'Password Must Contain atleast 1 Number.', 'id' => $this->fields['pswrd']['id']];
//        }

        if ($data->pswrd !== $data->cpsswrd) {
            return ['status' => false, 'id' => 'pswrd', 'msg' => 'Password/Confirm Password are not same.'];
        }
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'id' => 'email', 'msg' => 'This is invalid email format.'];
        }
        if (!empty($this->findByField('email', $data->email, 'userId'))) {
            return ['status' => false, 'id' => 'email', 'msg' => 'This Email Already Exist.'];
        }
        if (!empty($this->findByField('cnic', $data->cnic, 'userId'))) {
            if (!empty($this->validUserByCNIC($data->cnic))) {
                return ['status' => false, 'id' => 'cnic', 'msg' => 'User Already Exist with this CNIC.'];
            }
        }
//        if (!empty($this->findByField('cnic', $data->cnic, 'userId'))) {
//            return ['status' => false, 'id' => 'cnic', 'msg' => 'User Already Exist with this CNIC.'];
//        }
        if (!empty($this->findByField('cnic', $data->cnic, 'userId')) && !empty($this->findByField('email', $data->email, 'userId'))) {
            return ['status' => false, 'id' => 'email', 'msg' => 'User Already Exist for this Email and CNIC.'];
        }
        $postArr['paswrd'] = md5($postArr['paswrd']);
        $randId = \mihaka\helpers\MString::randomString();
        $postArr['cofirmationKey'] = $randId;
        $this->insert($postArr);
        $linkData = json_encode(['email' => $postArr['email'], 'id' => $randId]);
        $id = \mihaka\helpers\MString::encrypt($linkData);
        $link = SITE_URL . 'user/confirm/id/' . $id;
        $contents = 'Dear ' . $data->name . ','
                . '<br><br> Please <a href="' . $link . '">click here</a> to confirm your account.<br>'
                . '<br><br>'
                . 'System Analyst'
                . '<br>'
                . 'GCU  Lahore'
                . '<br>'
                . '<br><br><br><br>'
                . 'You are receiving this email because you have registered on this website for admission. Click here for <a href="' . SITE_URL . 'user/unsubscribe/id/' . $id . '">Unsubscribe</a>';
        $subject = 'Account Confirmation Email';
        $oEmailQueueModel = new \models\EmailQueueModel();
        $oEmailQueueModel->upsert([
            'email' => $postArr['email'],
            'subject' => $subject,
            'contents' => $contents,
            'added_on' => date('Y-m-d H:i:s')
        ]);
        /* Mailgun email commented on july 16, 2023
          $oMailGun = new \components\MailGun();
          $tag = 'GCU Admissions 2021';
          $text = str_ireplace('<br>', "\r\n", $contents);
          $oMailGun->sendMail($postArr['email'], $data->name, 'Signup Confirmation', $contents, $text, $tag);
         */
        /* $oMailer = $this->email();
          $oMailer->isHTML();
          $oMailer->from('GCU LAHORE', 'no-reply@gcu.edu.pk');
          $oMailer->send($postArr['email'], $subject, $contents); */
//        return['status' => true, 'msg' => 'Signup Successully completed but not activated yet. Please check your email to activate your account. Please also check your junk/spam in case email not present in inbox.', 'id' => ''];
        return['status' => true, 'msg' => 'Signup Successully completed.', 'id' => ''];
    }

    public function updateTestCentre($data, $userId) {

        if (empty($data['cityTest'])) {
            return ['status' => false, 'msg' => 'Please choose the relevant Test Centre.'];
        }
        
        $oMetaDataModel = new \models\MetaDataModel();
        $cityData['cityData'] = $oMetaDataModel->keyIdbyKeyValueAndByKeyDesc('testCentre', $data['cityTest']);

        $postArr = [
            "testCity" => $data['cityTest'],
            "testCityId" => $cityData['cityData']['keyId']
        ];

        $out = $this->upsert($postArr, $userId);
        return ['status' => true, 'msg' => 'Record updated successfully'];
    }

//    public function updateTestStream($data, $userId) {
//        $postArr = [
//            "testStream" => $data['streamTest']
//        ];
//        $out = $this->upsert($postArr, $userId);
//        return ['status' => true, 'msg' => 'Record updated successfully'];
//    }

    public function profileUpdate($data, $userId, $cCode) {
        $userData = $this->state()->get('userInfo');
        $postArr = [
            "name" => strtoupper($data['name']),
//            "cnic" => $data['cnic'],
            "gender" => $data['gender'],
            "dob" => $data['dob'],
            "add1" => strtoupper($data['addr']),
            "cityId" => $data['city'],
            "ph1" => $data['ph'],
//            "email" => $data['email'],
            "countryID" => $data['country'],
            "add2" => strtoupper($data['permadd']),
            "provinceId" => $data['province'],
            "tehsilId" => $data['tehsil'],
            "religion" => $data['relig'],
            "caste" => strtoupper($data['appCaste']),
            "area" => strtoupper($data['belongs']),
            "districtId" => $data['district'],
            "fatherName" => strtoupper($data['fname']),
            "fatherNic" => $data['fcnic'],
            "fatherStatus" => $data['fastatus'],
            "fatherNationality" => $data['fnat'],
            "motherName" => strtoupper($data['mname']),
            "motherNic" => $data['mcnic'],
            "motherNationality" => $data['mnat'],
            "motherStatus" => $data['mostatus'],
            "experience" => $data['profexp']
//            "experience" => "NA"
        ];
//        print_r($postArr);exit;
        if ($data['fastatus'] == 'Alive') {
            $postArr["fatherOccp"] = $data['foccp'];
            $postArr["fatherIncome"] = $data['fincom'];
            $postArr["fatherOffice"] = strtoupper($data['foffice']);
            $postArr["fatherEmail"] = $data['femail'];
            $postArr["fatherQual"] = $data['fqual'];
            $postArr["ph2"] = $data['fph'];
        } else {
            $postArr["fatherIncome"] = 'NA';
        }
        if ($data['mostatus'] == 'Alive') {
            $postArr["motherOccp"] = $data['moccp'];
            $postArr["motherIncome"] = $data['mincom'];
            $postArr["motherOffice"] = strtoupper($data['moffice']);
            $postArr["motherEmail"] = $data['memail'];
            $postArr["motherQual"] = $data['mqual'];
            $postArr["ph3"] = $data['mph'];
        } else {
            $postArr["motherIncome"] = 'NA';
        }


        if (!empty($cCode)) {
            $oFieldsModel = new \models\FieldsModel();
            $fields = $oFieldsModel->getFieldsOnly(FRM_PROFILE, $cCode);
        }
//        if (!filter_var($data['femail'], FILTER_VALIDATE_EMAIL)) {
//            return ['status' => false, 'id' => 'femail', 'msg' => 'This Email is invalid.'];
//        }

        foreach ($postArr as $key => $value) {
            if (empty($value) && !in_array($key, $fields) && $key !== 'caste') {
                $details = $this->getFieldLabel($key);
                return ['status' => false, 'msg' => $details['label'] . ' cannot left blank.', 'id' => $details['id']];
            } elseif (in_array($key, $fields)) {

                unset($postArr[$key]);
            }
        }

//        if ($postArr['fatherNic'] === $userData['cnic']) {
//            return ['status' => false, 'msg' => 'Father and Applicant CNIC cannot same', 'id' => $this->fields['fatherNic']['id']];
//        }
//        
//        if (strtolower($postArr['fatherIncome']) != 'na' && !is_numeric($postArr['fatherIncome'])) {
//            return ['status' => false, 'msg' => 'Enter Valid Income in Number', 'id' => $this->fields['fatherIncome']['id']];
//        }
//
//        if (strtolower($postArr['motherIncome']) != 'na' && !is_numeric($postArr['motherIncome'])) {
//            return ['status' => false, 'msg' => 'Enter Valid Income in Number', 'id' => $this->fields['motherIncome']['id']];
//        }
//        if (!filter_var($data->gemail, FILTER_VALIDATE_EMAIL)) {
//            return ['status' => false, 'id' => 'gemail', 'msg' => 'This is invalid email format.'];
//        }
//        if (!filter_var($data->femail, FILTER_VALIDATE_EMAIL)) {
//            return ['status' => false, 'id' => 'femail', 'msg' => 'This is invalid email format.'];
//        }
//        if (!filter_var($data->memail, FILTER_VALIDATE_EMAIL)) {
//            return ['status' => false, 'id' => 'memail', 'msg' => 'This is invalid email format.'];
//        }

        $postArr["guardName"] = $data['gname'];
        $postArr["ph4"] = $data['gph'];
        $postArr["guardNic"] = $data['gcnic'];
        $postArr["guardAddr"] = $data['gph'];
        $postArr["guardEmail"] = $data['gemail'];
        $postArr["guardIncome"] = $data['gincom'];
        $postArr["guardOccp"] = $data['goccp'];

        $out = $this->upsert($postArr, $userId);

        $this->setSessionData($userId, ['offerId' => $userData['offerId'], 'cCode' => $userData['cCode'], 'preReq' => $userData['preReq'], 'preReq1' => $userData['preReq1'], 'className' => $userData['className'], 'endDate' => $userData['endDate']]);
        return ['status' => true, 'msg' => 'Record updated successfully'];
    }

    private function getFieldLabel($field) {
        return $this->fields[$field];
    }

    public function saveImage($userData, $oPostArray, $bucketId, $fileName) {
        if ($this->upsert($oPostArray, $userData['userId'])) {
            $userData['picBucket'] = $bucketId;
            $userData['picture'] = $fileName;
            $this->state()->set('userInfo', $userData);
            if (!empty($userData['picBucket']) && $userData['picture']) {
                //unlink(UPLOAD_PATH . $userData['picBucket'] . '/' . $userData['picture']);
            }
            return true;
        }
        return false;
    }

    public function getUserImg($width = 100, $height = 110, $uImg = '') {
        if ($uImg != '') {
            return '<img width="' . $width . '" height="' . $height . '" src="' . PIC_URL . $uImg . '">';
        }
        $url = $this->getUserImgURL();
        if ($url === '') {
            return '<img width="' . $width . '" height="' . $height . '" src="' . IMAGE_URL . 'not_available.jpg" alt="user image" class="userimg">';
        }
        return '<img width="' . $width . '" height="' . $height . '" alt="user image" class="userimg" src="' . $url . '">';
    }

    public function getUserImageURL($userData) {
        if (!empty($userData['picture'])) {
            return PIC_URL . $userData['picBucket'] . '/' . $userData['picture'];
        } else {
            return IMAGE_URL . 'not_available.jpg';
        }
    }

    public function getUserImgURL() {
        $userData = $this->state()->get('userInfo');
        if (!empty($userData['picture'])) {
            return PIC_URL . $userData['picBucket'] . '/' . $userData['picture'];
        } else {
            return '';
        }
    }

    public function getUserImgURLByUserData($userData) {
        if (!empty($userData['picture'])) {
            $uImg = PIC_URL . $userData['picBucket'] . '/' . $userData['picture'];
            return '<img width="100" height="110" src="' . $uImg . '">';
        } else {
            return '';
        }
    }

    public function validUserByEmail($email) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId,email,name,gender, ph1')
                        ->from($this->table)
                        ->where('email', $email)
                        ->where('isConfirmed', 'Y')
                        ->find();
    }

    public function validUserByContactNo($email) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId,email,name,gender, ph1')
                        ->from($this->table)
                        ->where('ph1', $email)
                        ->where('isConfirmed', 'Y')
                        ->find();
    }

    public function validUserByMobile($mobile) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId,email,name,gender')
                        ->from($this->table)
                        ->where('ph1', $mobile)
                        ->where('isConfirmed', 'Y')
                        ->find();
    }

    public function validUserByCNIC($cnic) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId, ph1')
                        ->from($this->table)
                        ->where('cnic', $cnic)
                        ->where('isConfirmed', 'Y')
                        ->find();
    }
}
