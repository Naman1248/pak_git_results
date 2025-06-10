<?php

/**
 * Description of StudentChallansModel
 *
 * @author SystemAnalyst
 */

namespace models;

class StudentChallansModel extends SuperModel {

    protected $table = 'studentChallans';

    public function findByYearClassRollNoInst($YEAR, $CCODE, $RN, $INSTNO) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*,DATEDIFF(NOW(),DUEDATE) DAYS')
                ->from($this->table)
                ->where('YEAR', $YEAR)
                ->where('CCODE', $CCODE)
                ->where('RN', $RN)
                ->where('INSTNO', $INSTNO)
                ->find();
        return $data;
    }

    public function challanClasses() {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct CCODE,CNAME')
                ->from($this->table)
                ->orderBy('CCODE','ASC')
                ->findAll();
//        return $data;
        
    }

}
