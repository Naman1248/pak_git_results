<?php

/**
 * Description of ExamLevelClassModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ExamLevelClassModel extends SuperModel{
    protected $table = 'examLevelClass';
    protected $pk = 'id';

    public function getClassesByExam($levelId)
    {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('levelId,examClass')
                ->from($this->table)
                ->where('levelId',$levelId);
        return $oSqlBuilder->findAll();
    }
}
