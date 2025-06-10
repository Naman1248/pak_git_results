<?php

/**
 * Description of SmsQueueModel
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class SmsQueueModel extends \models\SuperModel {

    protected $table = 'smsQueue';
    protected $pk = 'id';

    public function insertPhones($phones, $message, $tag = '') {
            //    $url = 'https://pk.eocean.us/APIManagement/API/RequestAPI?user=gcu&pwd=AMdfLD8MORSmS4%2faTrElwt6UjPHaW07grBs1N9FvbRkQynObgl2RWAFi%2bDH8ZhGi5A%3d%3d&sender=GCU&reciever=_Number_&msg-data=_MESSAGE_&response=string';

        $contacts = explode(',', $phones);
        $oEocean = new \components\Eocean();
        foreach ($contacts as $ph) {
            $out = $this->insert([
                'phone' => $ph,
                'contents' => $message,
                'addedOn' => date('Y-m-d H:i:s'),
                'tag' => $tag
            ]);
            if ($out){
                $oEocean->sendSms($ph, $message);
            }
            else {
                $this->deleteByPK($out);
            }
        }
    }

    public function add($phone, $message) {
        $this->upsert([
            'phone' => $phone,
            'contents' => $message,
            'addedOn' => date('Y-m-d H:i:s')
        ]);
    }

    public function totalSmsByMobileAndTag($mobile, $tag) {
        $oSqlBuider = $this->getSQLBuilder();
        $data = $oSqlBuider->select('count(id) Total')
                ->from($this->table)
                ->where('tag', $tag)
                ->where('phone', $mobile)
                ->find();
        return $data;
    }

}
