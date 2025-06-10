<?php

/**
 * Description of userAdmissionOfferModel
 *
 * @author SystemAnalyst
 */

namespace models;

class userAdmissionOfferModel extends \models\SuperModel {

    protected $table = 'userAdmissionOffer';
    protected $pk = 'id';

    public function findByUserIdAndExtId($userId, $extId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId,offerId,endDate')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('id', $extId)
                        ->find();
    }

    public function findByUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('userId,offerId,endDate')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('endDate', date("Y-m-d 00:00:00"), '>=')
                        ->find();
    }

}
