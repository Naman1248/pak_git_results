<?php

/**
 * Description of MajorStreamModel
 *
 * @author SystemAnalyst
 */

namespace models;

class MajorStreamModel extends SuperModel {

    protected $table = 'majorStream';
    protected $pk = 'id';

    public function getMajorsByOfferIdAndTestStream($offerId, $testStream, $userId = null) {
        if (!empty($userId)) {
            $ouserAdmissionOfferModel = new \models\cp\userAdmissionOfferModel();
            $extensionData = $ouserAdmissionOfferModel->exist($userId, $offerId);
        }
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('a.majId, name')
                ->from($this->table . ' a', 'majors m')
                ->join('a.majId', 'm.majId')
                ->where('m.offerId', $offerId)
                ->where('a.testStream', $testStream);
        if (empty($extensionData)) {
            $oSqlBuilder->where('m.active', 'YES');
        }
        return $oSqlBuilder->findAll();
    }

}
