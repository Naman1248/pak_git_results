<?php

/**
 * Description of FieldsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class FieldsModel extends SuperModel {

    protected $table = 'fields';
    protected $pk = 'fieldId';

    public function getFields($formId, $cCode) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('f.fieldName', 'f.colName', 'c.status')
                        ->from($this->table . ' f', 'classForms c')
                        ->join('f.fieldId', 'c.fieldId')
                        ->where('c.cCode', $cCode)
                        ->where('c.frmId', $formId)
                        ->findAll();
        //$oSQLBuilder->printQuery();
    }

    public function getFieldsOnly($formId, $cCode) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('f.colName')
                ->from($this->table . ' f', 'classForms c')
                ->join('f.fieldId', 'c.fieldId')
                ->where('c.cCode', $cCode)
                ->where('c.frmId', $formId)
                ->findAll();
//        echo $oSQLBuilder->printQuery();
        $out = [];
        foreach ($data as $key => $value) {
            $out[] = $value['colName'];
        }
        return $out;
        //$oSQLBuilder->printQuery();
    }

    public function isFieldExist($fieldName, $cCode) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('f.fieldName', 'f.colName', 'c.status')
                        ->from($this->table . ' f', 'classForms c')
                        ->join('f.fieldId', 'c.fieldId')
                        ->where('c.cCode', $cCode)
                        ->where('f.fieldName', $fieldName)
//                        ->where('c.frmId', $formId)
                        ->find();
//        echo $oSQLBuilder->printQuery();
        return $data;
        //$oSQLBuilder->printQuery();
    }

}
