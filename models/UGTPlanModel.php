<?php

/**
 * Description of UGTPlanModel
 *
 * @author SystemAnalyst
 */

namespace models;

class UGTPlanModel extends SuperModel {

    protected $table = 'ugtPlan';
    protected $pk = 'id';

    public function findByField($field, $value, $selectFileds = '*') {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select($selectFileds)
                ->from($this->table)
                ->where($field, $value)
                ->where('expired', 'NO')
                ->whereNotIN('baseId', [3, 16, 17])
                ->findAll();
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['id']);
            }
        }
        return $data;
    }

    public function emailSeatingPlan() {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('*')
                ->from($this->table)
                ->where('expired', 'NO')
                ->whereIN('offerId', [97])
                ->findAll();
        return $data;
    }

    public function allVenues() {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(distinct userId) cnt, venue')
                ->from($this->table)
                ->groupBy('venue')
                ->orderBy('venue', 'ASC')
                ->findAll();
        return $data;
    }

    public function majorsByVenue($venue) {
        $oSqlBuilder = $this->getSQLBuilder();
        $major = $oSqlBuilder->select('GROUP_CONCAT(distinct major) major')
                ->from($this->table)
                ->where('venue', $venue)
                ->whereNotNull('date')
                ->whereNotNull('time')
                ->find();

        return $major;
    }

    public function applicantsByVenue($venue) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('userId, rn, formNo, rollNo, name, fatherName')
                        ->from($this->table)
                        ->where('venue', $venue)
                        ->whereNotNull('rn')
                        ->whereNotNull('date')
                        ->whereNotNull('time')
                        ->orderBy('rn', 'ASC')
                        ->findAll();
    }

}
