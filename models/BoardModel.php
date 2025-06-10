<?php

/**
 * Description of BoardModel
 *
 * @author SystemAnalyst
 */

namespace models;

class BoardModel extends SuperModel {

    protected $table = 'board';
    protected $pk = 'boardId';

    public function getBoards($preclass) {

        $isBoard = 1;
        if ($preclass > 2) {
            $isBoard = 0;
        }
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('boardId,boardNm')
                        ->from($this->table)
                        ->where('isBoard', $isBoard)
                        ->orderBy('boardNm', 'ASC')
                        ->findAll();
    }

}
