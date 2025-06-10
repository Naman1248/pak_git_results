<?php

/**
 * Description of FormsAndChallansModel
 *
 * @author SystemAnalyst
 */

namespace models;

class FormsAndChallansModel extends SuperModel {

    protected $table = 'formsAndChallans';
    protected $pk = 'cCode';

    public function challanByClass($cCode) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('challan')
                        ->from($this->table)
                        ->where('cCode', $cCode)
                        ->find();
    }

}
