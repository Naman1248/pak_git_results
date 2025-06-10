<?php

namespace models;

/**
 * Description of ChallansModel
 *
 * @author Amber
 */
class ChallansModel extends SuperModel {

    protected $table = 'challans';
    protected $pk = 'id';

    public function isExistByUserId($userId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, chalId, isPaid')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('isFreezed', 0)
                        ->find();
    }
    public function isExistByUserIdAndOfferId($userId, $offerId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, chalId, isPaid')
                        ->from($this->table)
                        ->where('userId', $userId)
                        ->where('offerId', $offerId)
                        ->where('isFreezed', 0)
                        ->find();
    }

    public function isChallanExistByChalId($chalId) {
        $oSQLBuilder = $this->getSQLBuilder();
        return $oSQLBuilder->select('id, chalId, isPaid, isFreezed')
                        ->from($this->table)
                        ->where('chalId', $chalId)
                        ->find();
    }

    public function updatePaymentStatus($id, $chStatus) {
        return ($this->upsert(['isPaid' => $chStatus], $id));
//        return ($this->upsert(['isPaid' => 'Y', 'updatedOn' => date('Y-m-d H:i:s'),'updatedBy' => 99], $appId));
    }

    public function byUserId($userId, $appId) {
        $oApplicationsModel = new \models\ApplicationsModel();
        $appUserId = $oApplicationsModel->findByPK($appId, 'appId,userId, offerId,chalId,isChallanGenerated');
        if ($appUserId['isChallanGenerated']) {
            return true; //do nothing, challan is already generated
        }
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOfferModel->findByPK($appUserId['offerId'], 'challansAllowed');
        $challansAllowed = $offerData['challansAllowed'];
        if ($challansAllowed == 1) {
            $this->insert([
                'userId' => $userId,
                'chalId' => $appUserId['chalId'],
                'isFreezed' => 1
                    ]
            );
            $oApplicationsModel->upsert(['isChallanGenerated' => 1], $appUserId['appId']);
        } else {
            $isExist = $this->isExistByUserId($userId);
            if (empty($isExist)) {
                $this->insert([
                    'userId' => $userId,
                    'chalId' => $appUserId['chalId'],
                        ]
                );
                $oApplicationsModel->upsert(['isChallanGenerated' => 1], $appUserId['appId']);
            } else {
//            print_r($isExist);exit;
//                $parentAppId = substr($isExist['chalId'], 2);
//                $parentChallanData = $oApplicationsModel->findByPK($parentAppId, 'isPaid, transactionId');
                $oApplicationsModel->upsert(['isChallanGenerated' => 1, 'chalId' => $isExist['chalId'], 'isPaid' => $isExist['isPaid']], $appUserId['appId']);
//                $oApplicationsModel->upsert(['isChallanGenerated' => 1, 'chalId' => $isExist['chalId'], 'isPaid' => $parentChallanData['isPaid']], $appUserId['appId']);
                $totalChallansAgainstChalId = $oApplicationsModel->getTotalApplicationsAgainstChallan($isExist['chalId']);
                if ($totalChallansAgainstChalId == 3) {
                    $this->upsert(['isFreezed' => 1], $isExist['id']);
                }
            }
        }
    }
    public function byUserIdAndOfferId($userId, $appId) {
        $oApplicationsModel = new \models\ApplicationsModel();
        $appUserId = $oApplicationsModel->findByPK($appId, 'appId,userId, offerId,chalId,isChallanGenerated');
        if ($appUserId['isChallanGenerated']) {
            return true; //do nothing, challan is already generated
        }
        $oAdmissionOfferModel = new \models\AdmissionOfferModel();
        $offerData = $oAdmissionOfferModel->findByPK($appUserId['offerId'], 'challansAllowed');
        $challansAllowed = $offerData['challansAllowed'];
        if ($challansAllowed == 1) {
            $this->insert([
                'userId' => $userId,
                'chalId' => $appUserId['chalId'],
                'isFreezed' => 1,
                'offerId' => $appUserId['offerId']
                    ]
            );
            $oApplicationsModel->upsert(['isChallanGenerated' => 1], $appUserId['appId']);
        } else {
            $isExist = $this->isExistByUserIdAndOfferId($userId, $appUserId['offerId']);
            if (empty($isExist)) {
                $this->insert([
                    'userId' => $userId,
                    'chalId' => $appUserId['chalId'],
                    'offerId' => $appUserId['offerId']
                        ]
                );
                $oApplicationsModel->upsert(['isChallanGenerated' => 1], $appUserId['appId']);
            } else {
                $oApplicationsModel->upsert(['isChallanGenerated' => 1, 'chalId' => $isExist['chalId'], 'isPaid' => $isExist['isPaid']], $appUserId['appId']);
                $totalChallansAgainstChalId = $oApplicationsModel->getTotalApplicationsAgainstChallan($isExist['chalId']);
                if ($totalChallansAgainstChalId == 3) {
                    $this->upsert(['isFreezed' => 1], $isExist['id']);
                }
            }
        }
    }
}
