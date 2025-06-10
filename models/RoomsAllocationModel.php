<?php

/**
 * Description of RoomsAllocationModel
 *
 * @author SystemAnalyst
 */

namespace models;

class RoomsAllocationModel extends SuperModel {

    protected $table = 'roomsAllocation';
    protected $pk = 'id';

    public function byOfferIdAndSlotNo($offerId, $slotNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('roomId, venue, capacity, sortOrder')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotNo', $slotNo)
                        ->orderBy('sortOrder')
                        ->findAll();
    }

    public function getAllottedRoomsbyOfferIdAndSlotNo($offerId, $slotNo) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, roomId, venue, roomFor, capacity, allotted, sortOrder')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotNo', $slotNo)
                        ->whereNotNull('allotted')
                        ->orderBy('sortOrder')
                        ->findAll();
    }

    public function getAllottedRoomsbySlotId($slotId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, roomId, venue, roomFor, capacity, allotted, sortOrder')
                        ->from($this->table)
                        ->where('slotId', $slotId)
                        ->whereNotNull('allotted')
                        ->orderBy('sortOrder')
                        ->findAll();
    }

    public function getSelectedRoomsbyOfferIdAndSlotNo($offerId, $slotId, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, roomId, venue, roomFor, capacity, allotted, cityId, sortOrder')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('slotId', $slotId)
                        ->where('cityId', $cityId)
                        ->orderBy('sortOrder')
                        ->findAll();
    }

    public function getVacantRoomsbyOfferIdAndSlotNo($offerIds, $slotNo, $cityId, $roomFor) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, roomId, venue, capacity, diff, allotted, roomFor, sortOrder, cityId')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('slotNo', $slotNo)
                        ->where('cityId', $cityId)
                        ->where('roomFor', $roomFor)
                        ->where('diff', 0, '>')
                        ->orderBy('sortOrder')
                        ->findAll();
    }

    public function byOfferIdAndSlotNoAll($offerId, $slotNo, $cityId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('rooms.roomId, rooms.venue, rooms.capacity, roomFor, roomsAllocation.capacity allottedCapacity, roomsAllocation.sortOrder, roomsAllocation.id, allotted, offerId, slotNo, rooms.cityId')
                        ->from('rooms LEFT JOIN roomsAllocation ON rooms.roomId = roomsAllocation.roomId AND offerId = ' . $offerId . ' AND slotNo = ' . $slotNo)
                        ->findAll();
//        $oSQLBuilder->printQuery();
    }

    public function updateAllottedRoom($id, $alloted, $diff) {

        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('allotted', $alloted)
                ->set('diff', $diff)
                ->from($this->table)
                ->where('id', $id)
                ->update();
        return $updateResponse;
    }

    public function resetAllottedRoomsByOfferIdAndSlotNo($offerId, $slotNo, $cityId, $id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $out = $oSqlBuilder->set('allotted', NULL)
                ->setRaw('diff', 'capacity')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('slotNo', $slotNo)
                ->where('cityId', $cityId)
                ->update();

        return $out;
    }

}
