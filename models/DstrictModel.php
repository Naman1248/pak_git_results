<?php

/**
 * Description of DstrictModel
 *
 * @author SystemAnalyst
 */

namespace models;

class DistrictModel extends SuperModel {

    protected $table = 'disrict';
    protected $pk = 'distId';

    public function findByField($field, $value, $selectFileds = '*') {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select($selectFileds)
                        ->from($this->table)
                        ->where($field, $value)
                        ->where('active', 'Y')
                        ->orderBy('distnm', 'ASC')
                        ->findAll();
    }

    public function getDistrictByProvinceIdAndDistrictId($provinceId, $districtId) {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distnm')
                        ->from($this->table)
                        ->where('distId', $districtId)
                        ->where('provinceId', $provinceId)
                        ->find();
//        return $data['name'];
    }

    public function getAllDistrict() {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('distId,distnm')
                        ->from($this->table)
                        ->where('active', 'Y')
                        ->orderBy('distnm', 'ASC')
                        ->findAll();
    }
}
