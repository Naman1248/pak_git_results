<?php

/**
 * Description of ClassRoomCapacity
 *
 * @author SystemAnalyst
 */

namespace models;

class ClassRoomCapacity extends SuperModel{
    
    protected $table = 'classRoomCapacity';
    protected $pk = 'roomId';
    
    public function getAvaialbeRoomsByOfferId($offerId, $roomFor)
    {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('venue, capacity, sortOrder, diff')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('roomFor', $roomFor)
                        ->where('diff', 0, '>')
                        ->findAll();
        
    }
}
