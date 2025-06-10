<?php

/**
 * Description of specialFormsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class specialFormsModel extends SuperModel {

    protected $table = 'specialForms';
    protected $pk = 'baseId';

    public function specialFormsByBaseId($baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('forms')
                        ->from($this->table)
                        ->where('baseId', $baseId)
                        ->find();
    }

}
