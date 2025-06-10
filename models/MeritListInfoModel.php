<?php

/**
 * Description of MeritListInfoModel
 *
 * @author SystemAnalyst
 */

namespace models;

class MeritListInfoModel extends SuperModel {

    protected $table = 'meritListInfo';
    protected $pk = 'id';

    public function addExpiry($post, $addedBy) {
        return $this->upsert([
                    'dueDate' => $post['expiry'],
                    'totalApplicants' => $post['totApplicants'],
                    'paidApplicants' => !empty($post['paidApplicants'])?$post['paidApplicants']:0,
                    'dueDate' => $post['expiry'],
                    'expiryAddedBy' => $addedBy,
                    'expiryAddedOn' => date('Y-m-d H:i:s')], $post['id']);
    }
    
    public function publishMeritList($post, $addedBy) {
        return $this->upsert([
                    'isPublished' => 'YES',
                    'publishedBy' => $addedBy,
                    'publishedOn' => date('Y-m-d H:i:s')], $post['id']);
    }

    public function meritListInfoByOfferIdAndByMajorIdAndBaseId($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->orderBy('a.meritList', 'ASC')
                ->findAll();
        return $data;
    }

    public function displayLockedMeritLists($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.loc', 'Y')
                ->orderBy('a.meritList', 'ASC')
                ->findAll();
        return $data;
    }
    public function displayPublishedMeritLists($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.isPublished', 'YES')
                ->whereNotNull('a.dueDate')
                ->orderBy('a.meritList', 'ASC')
                ->findAll();
        return $data;
    }
    public function baseWiseMeritListsInfo($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('a.meritList, a.majId, a.totalApplicants')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.loc', 'Y')
                ->where('a.isPublished', 'YES')
                ->orderBy('a.majId, a.meritList', 'ASC')
                ->findAll();
//        echo "<pre>";
//        var_dump($data);
        $oMajorsModel = new \models\MajorsModel();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']]['majorName'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($offerId, $row['majId']);
            $arr[$row['majId']][$row['meritList']] = $row['totalApplicants'];
        }
//        echo "<pre>";
//        var_dump($arr);exit;
        return $arr;
    }
    
    public function isMeritListsLockedAndPublished($offerId, $majorId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majorId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
                ->where('a.loc', 'Y')
                ->where('a.isPublished' , 'YES')
                ->find();
        return $data;
    }

    public function meritListInfoByOfferIdAndBaseId($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->orderBy('a.meritList', 'ASC')
                ->findAll();
        return $data;
    }

    public function meritListInfoLock($offerId, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.loc', 'Y')
                ->orderBy('a.meritList', 'ASC')
                ->findAll();
        return $data;
    }

    public function meritListInfoDetail($offerId, $majId, $baseId, $meritList) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.meritList', $meritList)
//                ->where('a.loc', 'Y')
                ->orderBy('a.meritList', 'ASC')
                ->find();
        return $data;
    }

    public function lockMeritListInfoById($id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('loc', 'Y')
                ->from($this->table)
                ->where('id', $id)
                ->update();
        return $updateResponse;
    }

    public function unLockMeritListInfoById($id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('loc', 'N')
                ->from($this->table)
                ->where('id', $id)
                ->update();
        return $updateResponse;
    }
}
