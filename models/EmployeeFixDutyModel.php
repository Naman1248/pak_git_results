<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace models;

/**
 * Description of EmployeeFixDutyModel
 *
 * @author SystemAnalyst
 */
class EmployeeFixDutyModel extends SuperModel {

    protected $table = 'employeeFixDuty';
    protected $pk = 'id';

    public function getSortedFixEmployees() {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->orderBy('sortOrder')
                ->findAll();
        return $data;
    }
}
