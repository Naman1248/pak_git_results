<?php

/**
 * Description of TehsilModel
 *
 * @author SystemAnalyst
 */

namespace models;

class TehsilModel extends SuperModel {

    protected $table = 'tehsil';
    protected $pk = 'tehId';

    public function findByField($field, $value, $selectFileds = '*') {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($selectFileds)
                        ->from($this->table)
                        ->where($field, $value)
                        ->where('active', 'Y')
                        ->orderBy('tehNm', 'ASC')
                        ->findAll();
    }

    public function getAllTehsil() {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('tehId,tehNm')
                        ->from($this->table)
                        ->where('active', 'Y')
                        ->orderBy('tehNm', 'ASC')
                        ->findAll();
    }
}
