<?php

/**
 * Description of DepttUsersLog
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class DepttUsersLogModel extends \models\SuperModel {

    protected $table = 'depttUsersLog';
    protected $pk = 'id';

    public function add($data) {
        $postArr = [
            "userId" => $data['id']
        ];
        $this->insert($postArr);
    }

}
