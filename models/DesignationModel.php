<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace models;

/**
 * Description of DesignationModel
 *
 * @author SystemAnalyst
 */
class DesignationModel extends SuperModel {

    protected $table = 'designation';
    protected $pk = 'id';

    public function getSortedDesignation() {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->orderBy('desig')
                ->findAll();
        return $data;
    }
}
