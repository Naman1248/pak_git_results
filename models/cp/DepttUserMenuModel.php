<?php

/**
 * Description of DepttUserMenuModel
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class DepttUserMenuModel extends \models\SuperModel {

    protected $table = 'depttUserMenu';
    protected $pk = 'id';

    public function menuByDIdAndUserId($dId, $dUserId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('menuId')
                ->from($this->table)
                ->where('dId', $dId)
                ->where('dUserId', $dUserId)
                ->findAll();

        $arr = [];
        foreach ($data as $row) {
            $arr[] = $row['menuId'];
        }
        return $arr;
    }
    public function findByMenuIdByDIdAndUserId($menuId, $dId, $dUserId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id')
                ->from($this->table)
                ->where('dId', $dId)
                ->where('dUserId', $dUserId)
                ->where('menuId', $menuId)
                ->find();

        return $data;
    }

}
