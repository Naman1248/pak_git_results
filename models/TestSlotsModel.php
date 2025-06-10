<?php

/**
 * Description of TestSlotsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class TestSlotsModel extends SuperModel {

    protected $table = 'testSlots';
    protected $pk = 'id';

    public function byYearAndClass($year, $class){
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotNo')
                        ->from($this->table)
                        ->where('year', $year)
                        ->where('class', $class)
                        ->where('expired', 'NO')
                        ->orderBy('slotNo')
                        ->findAll();
    }
    public function byYearAndClassAndSlotNo($year, $class, $slotNo){
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo) schedule, slotNo')
                        ->from($this->table)
                        ->where('year', $year)
                        ->where('class', $class)
                        ->where('slotNo', $slotNo)
                        ->findAll();
    }
    public function getSlotsByYear($year){
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distinct concat(date, "   ", day, "   ", startTime, "   ", slotNo, " ", class) schedule, id')
                        ->from($this->table)
                        ->where('year', $year)
                        ->where('expired', 'NO')
                        ->findAll();
    }
    
}
