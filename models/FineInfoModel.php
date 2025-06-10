<?php

/**
 * Description of FineInfoModel
 *
 * @author SystemAnalyst
 */

namespace models;

class FineInfoModel extends SuperModel {

    protected $table = 'fineInfo';
    protected $pk = 'id';

    public function getFine($days) {
        return $this->findOneByQuery('SELECT fineAmount,toDays FROM fineInfo WHERE status = ? AND ? >= fromDays AND ? <= toDays ', ['active', $days, $days]);
    }

}
