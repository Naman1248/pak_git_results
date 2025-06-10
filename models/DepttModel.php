<?php

/**
 * Description of DepttModel
 *
 * @author SystemAnalyst
 */

namespace models;

class DepttModel extends SuperModel {

    protected $table = 'deptt';
    protected $pk = 'dId';

    public function getSortedDepartments() {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->orderBy('depttName')
                ->findAll();
        return $data;
    }

}
