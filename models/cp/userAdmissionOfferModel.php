<?php

/**
 * Description of userAdmissionOfferModel
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class userAdmissionOfferModel extends \models\SuperModel {

    protected $table = 'userAdmissionOffer';
    protected $pk = 'id';

    public function add($params, $id = null) {
        $out = $this->upsert(
                [
                    'offerId' => $params['className'],
                    'userId' => $params['userId'],
                    'className' => $params['classLabel'],
                    'endDate' => $params['endDate'] . ' 23:59:59',
                    'requestedBy' => $params['reqBy'],
                    'addedBy' => $params['liuid'],
                    'addedOn' => date("Y-m-d H:i:s")
                ], $id);
//        if ($id == null) {
            if ($out) {
//            $id = \mihaka\helpers\MString::encrypt($out);
                $id = $out;
                $link = SITE_URL . 'dashboard/applyExtension/id/' . $id;
                $contents = $link;

                $oSmsQueueModel = new \models\cp\SmsQueueModel();
                $oSmsQueueModel->insertPhones(
                        $params['contactNo'],
                        $contents,
                        'Extension' . date('Ym')
                );
            }
//        }

        return $out;
    }

//    public function add($params, $id = null) {
//        $out = $this->upsert(
//                [
//            'offerId' => $params['className'],
//            'userId' => $params['userId'],
//            'className' => $params['classLabel'],
//            'endDate' => $params['endDate'] . ' 23:59:59',
//            'requestedBy' => $params['reqBy'],
//            'addedBy' => $params['liuid'],
//            'addedOn' => date("Y-m-d H:i:s")
//                ], $id);
//        if ($out) {
//            $id = \mihaka\helpers\MString::encrypt($out);
////            $id = $out;
//            $link = SITE_URL . 'dashboard/applyExtension/id/' . $id;
//            $contents = 'Dear Student,'
//                    . '<br><br> We are pleased to inform you that you have granted admission extension. Please <a href="' . $link . '">click here</a> to apply.<br>'
//                    . '<br><br>'
//                    . 'System Analyst'
//                    . '<br>'
//                    . 'GCU  Lahore'
//                    . '<br>'
//                    . '<br><br><br><br>'
//                    . 'You are receiving this email because you have registered on this website for admission. Click here for <a href="' . SITE_URL . 'user/unsubscribe/id/' . $id . '">Unsubscribe</a>';
//            $subject = 'Admission Extension';
//            $oEmailQueueModel = new \models\EmailQueueModel();
//            $oEmailQueueModel->upsert([
//                'email' => $params['email'],
//                'subject' => $subject,
//                'contents' => $contents,
//                'added_on' => date('Y-m-d H:i:s')
//            ]);
//            $oMailGun = new \components\MailGun();
//            $tag = 'Admission Extension';
//            $text = str_ireplace('<br>', "\r\n", $contents);
//            $oMailGun->sendMail($params['email'], $params['name'], $subject, $contents, $text, $tag);
//        }
//        return $out;
//    }

    public function exist($userId, $offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id,userId,endDate')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->orderBy('endDate', 'DESC')
                        ->find();
    }
}
