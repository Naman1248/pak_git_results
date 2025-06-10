<?php
/**
 * Description of CityModel
 *
 * @author SystemAnalyst
 */
namespace models;

class CityModel extends \models\SuperModel {

    protected $table = 'cities';
    protected $pk = 'cityId';

    public function getCities() {

        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('cityID,cityName')
                        ->from($this->table)
                        ->orderBy('cityName','ASC')
                        ->findAll();
    }
}
