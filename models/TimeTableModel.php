<?php

/**
 * Description of TimeTableModel
 *
 * @author SystemAnalyst
 */

namespace models;

class TimeTableModel extends SuperModel {

    protected $table = 'timeTable';

    public function findByRN($rn,$t_no,$C_CODE) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('e.SUB_CODE,e.SECTION,t.PER_ID,t.DAY_ID,t.BLK_NM,t.RM_NM')
                ->from($this->table . ' t', 'enroll e')
                ->join('t.SUB_CODE', 'e.SUB_CODE')
                ->join('t.SEC', 'e.SECTION')
                ->join('t.C_CODE', 'e.C_CODE')
                ->where('e.RN', $rn)
                ->where('e.C_CODE', $C_CODE)
                ->where('e.T_NO', $t_no)
                ->where('e.YEAR', 2020)
                ->findAll();
        $result = [];
        foreach ($data as $row) {
            $result[$row['DAY_ID']][$row['PER_ID']] = $row;
        }
        return $result;
    }         
}
