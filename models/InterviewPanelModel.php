<?php

/**
 * Description of InterviewPannelModel
 *
 * @author SystemAnalyst
 */

namespace models;

class InterviewPanelModel extends SuperModel {

    protected $table = 'interviewPanel';
    protected $pk = 'id';

    public function displayInterviewPanel($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.expired', 'NO')
                ->orderBy('a.majId, a.baseId, a.meritList, a.panelNo', 'ASC')
                ->findAll();
        return $data;
    }
    
}
