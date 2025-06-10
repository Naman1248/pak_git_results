<?php
/**
 * Description of StudentModel
 *
 * @author SystemAnalyst
 */
namespace models;
class StudentModel extends SuperModel {
    
    protected $table='students';
    
    public function findStudentByRN($rn,$CCODE, $year){
        
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('YEAR,NAME,FATHERNAME,ROLLNO,MAJID,SETNO,CCODE')
                            ->from($this->table)
                            ->where('RN',$rn)
                            ->where('CCODE',$CCODE)
                            ->where('YEAR',$year)
                            ->find();
        return $data;
    }
}
