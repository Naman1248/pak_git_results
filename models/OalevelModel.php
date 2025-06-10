<?php

/**
 * Description of OalevelModel
 *
 * @author SystemAnalyst
 */

namespace models;

class OalevelModel extends SuperModel {

    protected $table = 'oalevel';
    protected $pk = 'id';

    public function getOLevelCompulsorySubjects() {
        return ['islamic_studies' => 'Islamic Studies', 'pakistan_studies' => 'Pakistan Studies',
            'urdu' => 'Urdu', 'english' => 'English', 'math' => 'Mathematics'];
    }

    public function getGradesList() {
        $arr = ['A*' => 'A*', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'];
//        $list = "<select name='" . $name . "' id='" . $name . "'>";
//        $list .= '<option value="" selected="selected">Select grade for ' . $subject . '</option>';
//        foreach ($arr as $grade) {
//            $list .= '<option value="' . $grade . '">' . $grade . '</option>';
//        }
//        $list .= '</select>';
        return $arr;
    }

    public function isExist($userId, $examClass) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id')
                ->from($this->table)
                ->where('userId', $userId)
                ->where('examClass', $examClass)
                ->find();
        if (empty($data)) {
            return '';
        } else {
            return $data['id'];
        }
    }
public function deleteByUserIdAndExamLevel($userId, $examClass) {
    $id=$this->getByUserIdAndExamLevel($userId, $examClass,'id');
    $this->deleteByPK($id['id']);
}
    public function getByUserIdAndExamLevel($userId, $examClass,$fields='*') {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select($fields)
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('examClass', $examClass)
                        ->find();
    }

    public function getByUserId($userId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('userId', $userId)
//                        ->where('examClass', 'A-Level')
                        ->findAll();
    }

}
