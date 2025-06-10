<?php

/**
 * Description of EmailQueueModel
 *
 * @author SystemAnalyst
 */

namespace models;

class EmailQueueModel extends SuperModel {

    protected $table = 'emailQueue';
    protected $pk = 'id';

    public function totalEmailByEmailAndSubject($email, $subject){
        $oSqlBuider = $this->getSQLBuilder();
        $data = $oSqlBuider->select('count(email) Total')
                            ->from($this->table)
                            ->where('subject',$subject)
                            ->where('email',$email)
                            ->groupBy('email')
                            ->find();
        return $data;
    }
}
