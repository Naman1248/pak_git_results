<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace models;

/**
 * Description of EmployeeTestDutyModel
 *
 * @author SystemAnalyst
 */
class EmployeeTestDutyModel extends SuperModel {

    protected $table = 'employeeTestDuty';
    protected $pk = 'id';

    public function findByOfferIdAndSlotNoAndVenueId($offerId, $slotNo, $roomId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, designation, depttId, depttName, empName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->where('roomId', $roomId)
                ->findAll();
        return ($data);
    }

    public function findByOfferIdAndSlotIdAndVenueId($offerId, $slotId, $roomId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, designation, depttId, depttName, empName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->where('roomId', $roomId)
                ->findAll();
        return ($data);
    }

    public function findByOfferIdAndSlotIdAndCityId($offerId, $slotId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, designation, depttId, depttName, empName, roomId, rAllId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->where('cityId', $cityId)
                ->findAll();
        return ($data);
    }

    public function findAllDepttsBySlotId($slotId) {

        $oSQLBuilder = $this->getSQLBuilder();

        $data = $oSQLBuilder->select('count(empName) tot, depttId, depttName')
                ->from($this->table)
                ->where('slotId', $slotId)
                ->groupBy('depttId, depttName')
                ->orderBy('depttName')
                ->findAll();

        return ($data);
    }

    public function findAllDepttsByOfferId($offerId) {

        $oSQLBuilder = $this->getSQLBuilder();

        $data = $oSQLBuilder->select('count(empName) tot, depttId, depttName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->groupBy('depttId, depttName')
                ->orderBy('depttName')
                ->findAll();

        return ($data);
    }

    public function findByOfferIdAndSlotNo($offerId, $slotNo, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, roomId, venue, desigId, designation, depttId, depttName, empName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->orderBy('roomId, desigId')
                ->findAll();
        if (empty($data)) {
            return $data;
        }
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndSlotIdAndDeptt($offerId, $slotId, $depttId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, roomId, venue, designation, depttId, depttName, empName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotId', $slotId)
                ->where('depttId', $depttId)
                ->orderBy('roomId')
                ->findAll();
        if (empty($data)) {
            return $data;
        }
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndDeptt($offerId, $depttId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, roomId, venue, designation, depttId, depttName, empName')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('depttId', $depttId)
                ->orderBy('slotNo, roomId')
                ->findAll();
        if (empty($data)) {
            return $data;
        }
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['venue']][] = $row;
        }
        return $arr;
    }

    public function findByOfferIdAndDepttAll($offerId, $depttId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id, roomId, venue, designation, depttId, depttName, empName, slotNo')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('depttId', $depttId)
                ->orderBy('slotNo, roomId')
                ->findAll();
        return $data;
    }

}
