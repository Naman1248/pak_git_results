<?php

/**
 * Description of ConvocationModel
 *
 * @author SystemAnalyst
 */

namespace models;

class ConvocationModel extends SuperModel {

    protected $table = 'convocation';
    protected $pk = 'id';
    public function findByRN($rn) {
        return $this->findOneByField('RollNo', $rn);
    }
}
