<?php

/**
 * Description of StudentFineModel
 *
 * @author SystemAnalyst
 */

namespace models;

class StudentFineModel extends SuperModel {

    protected $table = 'studentFine';

    public function isExists($id, $days) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('ID', $id)
                        ->where('TOTDAYS', $days)
                        ->find();
    }

    public function add($params) {
        $oFineInfoModel = new \models\FineInfoModel();
        $fineData = $oFineInfoModel->getFine($params['DAYS']);
        if (!empty($fineData)) {
            $fineExists = $this->isExists($params['ID'], $fineData['toDays']);
            if (!empty($fineExists)) {
                return $fineExists;
            }
            $arr['FINEAMOUNT'] = $fineData['fineAmount'];
            $arr['ID'] = $params['ID'];
            $arr['TOTDAYS'] = $fineData['toDays'];
            $arr['FINEDATE'] = date("Y-m-d H:i:s");
            $arr['DUEDATE'] = date("Y-m-d H:i:s", strtotime($params['DUEDATE'] . '+' . $fineData['toDays'] . ' DAYS'));
            $this->insert($arr);
            return $arr;
        }
        return ['FINEAMOUNT' => 0];
    }

}
