<?php

/**
 * Description of countriesModel
 *
 * @author SystemAnalyst
 */

namespace models;

class CountriesModel extends \models\SuperModel {

    protected $table = 'countries';
    protected $pk = 'countryId';

    public function overseasCountries() {

        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('countryId,name')
                        ->from($this->table)
                        ->where('active', 'Y')
                        ->findAll();
    }

}
