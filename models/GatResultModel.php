<?php

/**
 * Description of GatResultModel
 *
 * @author SystemAnalyst
 */

namespace models;

class gatResultModel extends SuperModel {

    protected $table = 'gatResult';
    protected $pk = 'id';

    public function findByField($field, $value, $selectFileds = '*') {
        $userData = $this->state()->get('userInfo');
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select($selectFileds)
                ->from($this->table)
                ->where($field, $value)
                ->where('expired', 'NO')
                ->findAll();
        if (!empty($data)) {
            foreach ($data as $row) {
                if ($userData['userId'] == $row['userId']) {
                    $this->upsert(['viewed' => date('Y-m-d H:i:s')], $row['id']);
                }
            }
        }
        return $data;
    }

    public function getPassResultByUserId($userId, $majId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('majId', $majId)
                        ->where('status', 'PASS')
                        ->where('expired', 'NO')
                        ->orderBy('total', 'DESC')
                        ->find();
    }
    public function getTestStreamResultByUserIdAndOfferIds($offerIds, $majId, $userId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->whereIN('offerId', $offerIds)
                        ->where('majId', $majId)
                        ->where('userId', $userId)
                        ->where('status', 'Pass')
                        ->where('expired', 'NO')
                        ->orderBy('total', 'DESC')
                        ->find();
    }
    public function getResultByOfferIdByUserIdByMajorId($offerId, $majId, $userId) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('offerId', $offerId)
                        ->where('majId', $majId)
                        ->where('userId', $userId)
                        ->where('expired', 'NO')
                        ->where('status', 'Pass')
                        ->orderBy('total', 'DESC')
                        ->find();
    }
    public function isFormNoExist($formNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('id')
                        ->from($this->table)
                        ->where('formNo', $formNo)
                        ->find();
    }

    public function getGATResultByRollNo($rollNo) {
        $oSqlBuilder = $this->getSQLBuilder();
        return $oSqlBuilder->select('*')
                        ->from($this->table)
                        ->where('rollNo', $rollNo)
                        ->where('expired', 'NO')
                        ->find();
    }

    public function getResultByOfferIdsAndUserId($offerIds, $userId){
        
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('majId')
                            ->from($this->table)
                            ->whereIN('offerId' , $offerIds)
                            ->where('userId' , $userId)
                            ->where('expired', 'NO')
                            ->where('status', 'PASS')
                            ->findAll();
                
    }
    public function getPassByOfferIdsAndMajorIdAndUserId($offerIds, $testStream, $userId){
        
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('majId')
                            ->from($this->table)
                            ->whereIN('offerId' , $offerIds)
                            ->where('majId' , $testStream)
                            ->where('userId' , $userId)
                            ->where('expired', 'NO')
                            ->where('status', 'Pass')
                            ->findAll();
                
    }
            
    public function ResultStatbyOfferIds($offerIds, $testPassPer, $reqPer) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) tot, majId, status')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->groupBy('majId, status')
                ->orderBy('majId', 'ASC')
                ->findAll();

        if (!empty($reqPer)) {
            $perData = $this->RequiredPercentagebyOfferIds($offerIds, $testPassPer, $reqPer);
        }
//        $minOfferId = min($offerIds);
        echo "<pre>";
        $oMajorsModel = new \models\MajorsModel();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']][$row['status']] = $row['tot'];
            $arr[$row['majId']]['name'] = $oMajorsModel->getMultiMajorPrintitleByOfferIdsAndMajorId($offerIds, $row['majId']);
//            $arr[$row['majId']]['name'] = $oMajorsModel->getMajorNameByOfferIdAndMajorId($minOfferId, $row['majId']);
//            $arr[$row['majId']]['name'] = $row['major'];
            $arr[$row['majId']]['reqPer'] = $perData[$row['majId']] ?? 0;
        }
        return $arr;
    }

    public function RequiredPercentagebyOfferIds($offerIds, $testPassPer, $reqPer) {

        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(formNo) total, majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('percentage', $reqPer - 1, '>')
                ->where('percentage', $testPassPer, '<')
                ->groupBy('majId, major')
                ->orderBy('majId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function countMSEmailsGATPassByOfferId($offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('COUNT(DISTINCT(email)) tot')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('u.msAdmission', 'N')
                ->where('a.offerId', $offerId)
                ->where('a.status', 'PASS')
                ->find();
        return $data;
    }

    public function emailsMSGATPassByOfferId($offerId, $offset, $perPage) {
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('DISTINCT(email) em', 'u.name', 'u.userId')
                ->from($this->table . ' a', 'users u')
                ->join('a.userId', 'u.userId')
                ->where('u.msAdmission', 'N')
                ->where('a.offerId', $offerId)
                ->where('a.status', 'PASS')
                ->limit($offset, $perPage)
                ->findAll();
        return $data;
    }

    public function releaseResultByOfferIds($slotId, $slotNo, $expired, $releasedAction, $id) {
        $oTestScheduleModel = new \models\TestScheduleModel();
        $offerIds = $oTestScheduleModel->getOfferIdsBySlotId($slotId);
        $tot = $this->totalExpiredStatusByOfferIdAndSlotNo($offerIds, $slotNo, $expired);
        if ($tot > 0) {
            $oSqlBuilder = $this->getSQLBuilder();
            $updateResponse = $oSqlBuilder->set('expired', $expired)
                    ->set('releasedBy', $id)
                    ->set('releasedOn', date("Y-m-d H:i:s"))
                    ->set('releasedAction', $releasedAction)
                    ->from($this->table)
                    ->whereIN('offerId', $offerIds)
                    ->where('slotNo', $slotNo)
                    ->where('testTotal', 0, '>')
                    ->update();
            return 'Roll No. Slips Updated Successfully to ' . $tot . ' Applicants.';
        } else {
            return 'No Roll No. Slips Found to Update.';
        }
    }

    private function totalExpiredStatusByOfferIdAndSlotNo($offerIds, $slotNo, $expired) {
        $_expired[] = $expired;
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('count(rn) totalRn')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('slotNo', $slotNo)
                ->where('testTotal', 0, '>')
                ->whereNotIn('expired', $_expired)
                ->find();
        return $data['totalRn'];
    }

    public function shiftMajorWiseAward($offerId, $majorId, $cityId, $id) {

        $oGatSlipModel = new \models\GatSlipModel();
        $slipData = $oGatSlipModel->applicantsByOfferIdAndMajorIdForAward($offerId, $majorId, $cityId);
//        print_r($slipData);exit;
        if (empty($slipData)) {
            return "No Applicant to Transfer for Award.";
        }
        $tot = 0;
        foreach ($slipData as $row) {

            $out = $this->insert(
                    [
                        'userId' => $row['userId'],
                        'formNo' => $row['formNo'],
                        'rn' => $row['rn'],
                        'rollNo' => $row['rollNo'],
                        'name' => $row['name'],
                        'fatherName' => $row['fatherName'],
                        'contactNo' => $row['contactNo'],
                        'email' => $row['email'],
                        'cnic' => $row['cnic'],
                        'majId' => $majorId,
                        'major' => $row['major'],
                        'testDate' => $row['date'],
                        'offerId' => $row['offerId'],
                        'slotId' => $row['slotId'],
                        'slotNo' => $row['slotNo'],
                        'cityId' => $cityId,
                        'postedBy' => $id,
                        'postedOn' => date("Y-m-d H:i:s")
                    ]
            );
            if ($out) {
                $oGatSlipModel->upsert(['award' => 'YES'], $row['id']);
                $tot++;
            } else {
                $this->deleteByPK($out);
            }
        }

        return 'Award Data Posted Successfully For ' . $tot . ' Applicants.';
    }

    public function deleteMajorWiseAward($offerId, $majorId, $cityId, $id) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->beginTransaction();

        $oGatSlipModel = new \models\GatSlipModel();
        $total = $oGatSlipModel->totalAwardByOfferIdAndMajorId($offerId, $majorId, $cityId);

        if (empty($total)) {
            return "No Applicant to Delete for Award.";
        }

        $updateResponseAward = $oGatSlipModel->resetAwardByOfferIdAndMajorId($offerId, $majorId, $cityId);

        $deleteResponse = $oSqlBuilder->select('')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('majId', $majorId)
                ->where('cityId', $cityId)
                ->where('expired', 'YES')
                ->delete();

        if ($updateResponseAward && $deleteResponse) {
            $oSqlBuilder->commit();
            return 'Award Reset Successfully for ' . $total . ' Applicants.';
        } else {
            $oSqlBuilder->rollback();
            return 'Nothing to Reset OR Some Internal Error.';
        }
    }

    public function countApplicantsByOfferId($offerId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->where('offerId', $offerId)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function countApplicantsByMultiOfferIds($offerIds, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('count(id) total,majId')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('cityId', $cityId)
                ->whereNotNull('rn')
                ->groupBy('majId')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['majId']] = $row['total'];
        }
        return $arr;
    }

    public function applicantsByOfferIdAndByMajorId($offerIds, $majId, $cityId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id, userId, formNo, rn, rollNo, name, fatherName, attendance, compulsory, subject, total, testTotal, status')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
//                ->where('rn', '4000', '>')
//                ->where('rn', '6001', '<')
                ->orderBy('rn', 'ASC')
                ->findAll();
        return $data;
    }
    public function applicantsByOfferIdAndByMajorIdByRNRange($offerIds, $majId, $cityId, $startRn, $endRn) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id, userId, formNo, rn, rollNo, name, fatherName, attendance, compulsory, subject, total, testTotal, status')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
                ->where('rn', $startRn - 1 , '>')
                ->where('rn', $endRn + 1 , '<')
                ->orderBy('rn', 'ASC')
                ->findAll();
        return $data;
    }
    public function applicantsByAllChildOfferIdsAndByMajorIdAndBaseId($offerIds, $majId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id, a.userId, a.formNo, a.rn, rollNo, name, fatherName, attendance, compulsory, subject, total, testTotal, status')
                ->from($this->table . ' a', ' applications ap')
                ->join('a.offerId', 'ap.offerId')
                ->join('a.formNo', 'ap.formNo')
                ->whereIN('a.offerId', $offerIds)
                ->where('a.majId', $majId)
                ->where('status', 'PASS')
                ->where('ap.baseId', $baseId)
//                ->where('rn', '4000', '>')
//                ->where('rn', '6001', '<')
                ->orderBy('a.rn', 'ASC')
                ->findAll();
        return $data;
    }

    public function applicantsByOfferIdAndByMajorIdAndByStatus($offerIds, $majId, $cityId, $status) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('id, rn, rollNo, name, fatherName, contactNo, email, total, testTotal, status')
                ->from($this->table)
                ->whereIN('offerId', $offerIds)
                ->where('majId', $majId)
                ->where('cityId', $cityId)
                ->where('status', $status)
                ->orderBy('rn', 'ASC')
                ->findAll();
        return $data;
    }

    public function resetGATResult($gatId, $id) {
        $oSqlBuilder = $this->getSQLBuilder();
        $updateResponse = $oSqlBuilder->set('percentage', NULL)
                ->set('status', 'Absent')
                ->set('compulsory', NULL)
                ->set('attendance', 'No')
                ->set('subject', NULL)
                ->set('total', NULL)
                ->set('testTotal', NULL)
                ->set('updatedBy', $id)
                ->set('updatedOn', date("Y-m-d H:i:s"))
                ->from($this->table)
                ->where('id', $gatId)
                ->update();

        return $updateResponse;
    }

    public function GATResultCalculator($id, $passPer) {

        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.id', $id)
//                ->where('a.compulsory', 0, '>')
//                ->where('a.subject', 0, '>')
                ->where('attendance', 'Yes')
                ->findAll();
        foreach ($data as $row) {
            $appPer = $this->calculatePerentage($row['total'], $row['testTotal'], 100);
            $status = $this->calculateResult($appPer, $passPer);
            $params['percentage'] = !empty($appPer) ? $appPer : 0;
            $params['status'] = $status;
            $this->upsert($params, $id);
        }
    }

    public function calculatePerentage($obt, $tot, $per) {
        if ($tot == 0 || $obt == 0) {
            return 0;
        }
        return round(($obt / $tot * $per), 2);
    }

    public function calculateResult($per, $passPer) {

        if ($per < $passPer) {
            return 'Fail';
        } else {
            return 'Pass';
        }
    }

}
