<?php

/**
 * Description of UGTResultModel
 *
 * @author SystemAnalyst
 */

namespace models\withoutTest;

class UGTResultModel extends \models\UGTResultModel {

    private $aggregateUGTFunction = [
        '3' => 'UGTAggregateDisable',
        '7' => 'UGTAggregateHafiz',
        '9' => 'UGTAggregateGeneral',
        '11' => 'UGTAggregateGeneral',
        '18' => 'UGTAggregateGeneral',
        '20' => 'UGTAggregateGeneral',
        '36' => 'UGTAggregateGeneral'
    ];
    private $aggregateInterFunction = [
        '1' => 'InterAggregateSpecial',
        '3' => 'InterAggregateSpecial',
        '5' => 'InterAggregateGeneral',
        '6' => 'InterAggregateSpecial',
        '7' => 'InterAggregateHafiz',
        '9' => 'InterAggregateGeneral',
        '11' => 'InterAggregateSpecial',
        '13' => 'InterAggregateGeneral',
        '15' => 'InterAggregateKinship',
        '16' => 'InterAggregateSpecial',
        '17' => 'InterAggregateSpecial',
        '30' => 'InterAggregateSpecial',
        '33' => 'InterAggregateSpecial',
        '36' => 'InterAggregateSpecial',
        '42' => 'InterAggregateNomination',
        '43' => 'InterAggregateNomination',
        '44' => 'InterAggregateGeneral',
        '45' => 'InterAggregateNomination',
        '46' => 'InterAggregateKinship',
        '47' => 'InterAggregateHafiz',
        '66' => 'InterAggregateNomination',
        '72' => 'InterAggregateGeneral',
        '79' => 'InterAggregateSpecial',
        '90' => 'InterAggregateNomination'
    ];

    private function getAggregateName($baseId) {
        return $this->aggregateUGTFunction[$baseId];
    }

    private function getInterAggregateName($baseId) {
        return $this->aggregateInterFunction[$baseId];
    }

    public function UGTAggregate($offerId, $majId, $baseId, $formNo = null) {
        $oUgtResultModel = new \models\withoutTest\UGTResultModel();
        $func = $this->getAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId, $formNo);
    }

    public function InterAggregate($offerId, $majId, $baseId, $formNo = null) {
        $oUgtResultModel = new \models\withoutTest\UGTResultModel();
        $func = $this->getInterAggregateName($baseId);
        $oUgtResultModel->$func($offerId, $majId, $baseId, $formNo);
//        }
    }

    public function UGTAggregateHafiz($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interObt', 0, '>')
                ->where('a.trialTotal', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $interAgg = $this->calculatePerentage($row['interObt'] + $row['trialObt'], 100, 100);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['totAgg'] = $params['interAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateGeneral($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $interAgg = $this->calculatePerentage($row['interObt'], 100, 100);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['totAgg'] = $params['interAgg'];
//            print_r($params);exit;
            $this->upsert($params, $row['appId']);
        }
    }

    public function UGTAggregateDisable($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.interObt', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $interAgg = $this->calculatePerentage($row['interObt'], 100, 100);
            $params['interAgg'] = !empty($interAgg) ? $interAgg : 0;
            $params['totAgg'] = $params['interAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateGeneral($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'];
//            print_r($params);exit;
            $this->upsert($params, $row['appId']);
        }
    }

   
    public function InterAggregateSpecial($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.trialObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'];

            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateHafiz($offerId, $majId, $baseId, $formNo = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.trialTotal', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'] + $row['trialObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function InterAggregateKinship($offerId, $majId, $baseId, $formNo = null) {

        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId)
                ->where('a.locMeritList', 'N')
                ->where('a.matricObt', 0, '>')
                ->where('a.kinRelationTotal', 0, '>')
                ->where('a.kinInterviewObt', 0, '>');
        if ($formNo != null) {
            $oSqlBuilder->where('a.formNo', $formNo);
        }
        $data = $oSqlBuilder->findAll();
        foreach ($data as $row) {
            $matricAgg = $this->calculatePerentage($row['matricObt'], $row['matricTotal'], 100);
            $params['matricAgg'] = !empty($matricAgg) ? $matricAgg : 0;
            $params['totAgg'] = $params['matricAgg'] + $row['kinRelationTotal'] + $row['kinInterviewObt'];
            $this->upsert($params, $row['appId']);
        }
    }

    public function marksInfoSpecial($offerId, $baseId) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.baseId', $baseId)
                ->where('a.trialObt', 0, '>')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.appId', 'ASC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }

    public function marksInfoWOTest($offerId, $majId, $baseId, $isVerified = null) {
        $oSqlBuilder = $this->getSQLBuilder();
        $oSqlBuilder->select('*')
                ->from($this->table . ' a')
                ->where('a.offerId', $offerId)
                ->where('a.majId', $majId)
                ->where('a.baseId', $baseId);
        if ($baseId == 3 || $baseId == 7 || $baseId == 41 || $baseId == 36 || $baseId == 11) {
            $oSqlBuilder->where('a.trialObt', 0, '>');
        }
        if ($isVerified != null) {
            $oSqlBuilder->where('a.isVerified', $isVerified);
        }
        $data = $oSqlBuilder->where('status', 'PASS')
                ->whereNull('a.meritList')
                ->whereNull('a.srNo')
                ->orderBy('a.matricObt', 'DESC')
                ->findAll();
        $arr = [];
        foreach ($data as $row) {
            $arr[$row['appId']] = $row;
        }
        return $arr;
    }
}
