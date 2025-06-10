<?php

/**
 * Description of RoomsModel
 *
 * @author SystemAnalyst
 */

namespace models;

class RoomsModel extends SuperModel {

    protected $table = 'rooms';
    protected $pk = 'roomId';

    public function allAvailableRooms() {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('roomId, venue, capacity')
                        ->from($this->table)
                        ->where('availability', 'YES')
                        ->orderBy('venue')
                        ->findAll();
    }

}
