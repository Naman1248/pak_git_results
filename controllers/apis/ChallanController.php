<?php

/**
 * Description of ChallanController
 *
 * @author SystemAnalyst
 */

namespace controllers\apis;

class ChallanController extends SuperController {

    public function challanPaymentAction() {
        $post = $this->post()->all();
        if (empty($post['challanNo'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Challan Number is Missing.';
            return $this->printJson(FALSE, $response);
        } elseif (empty($post['trans_id'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Transaction ID is Missing.';
            return $this->printJson(FALSE, $response);
        } elseif (empty($post['paid_date'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Paid Date is Missing.';
            return $this->printJson(FALSE, $response);
        } elseif (empty($post['branch_code'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Branch Code is Missing.';
            return $this->printJson(FALSE, $response);
        }

        $oApplicationsModel = new \models\ApplicationsModel();
        $appId = $oApplicationsModel->isChallanExist(strtoupper($post['challanNo']));

        if (empty($appId)) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Challan Does Not Exist.';
            return $this->printJson(FALSE, $response);
        }

        if (!empty($appId['transactionId'])) {
//            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Already Paid.';
            return $this->printJsonResponse(FALSE, $response);
        }

        if ($oApplicationsModel->updatePaymentStatusBank($appId['appId'], $this->appId, $post)) {
            $ochallansModel = new \models\ChallansModel();
            $challanId = $ochallansModel->findOneByField('chalId', strtoupper($post['challanNo']), 'id');
            if (!empty($challanId)) {
                $ochallansModel->upsert(['isPaid' => 'Y'], $challanId['id']);
            }
            $allChallanUpdate = $oApplicationsModel->updatePaymentByChalId($appId['userId'], strtoupper($post['challanNo']), $post, $this->appId);

            $data = $this->challanDetail($appId, strtoupper($post['challanNo']));
            $data['paid'] = 1;
            $data['Due_Amnt'] = 0;

            $response['msg'] = 'Success';
            $response['challanDetails'] = $data;

            return $this->printJson(TRUE, $response);
        } else {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Some Internal Error Occured, Please Try Again.';
            return $this->printJson(FALSE, $response);
        }
    }

    public function challanInquiryAction() {
        $post = $this->post()->all();
        if (empty($post['challanNo'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Challan Id is Missing.';
            return $this->printJsonResponse(FALSE, $response);
        }

        $oApplicationsModel = new \models\ApplicationsModel();
        $appId = $oApplicationsModel->isChallanExist(strtoupper($post['challanNo']));
        if (empty($appId)) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Challan Does Not Exist.';
            return $this->printJsonResponse(FALSE, $response);
        }

        $data = $this->challanDetail($appId, strtoupper($post['challanNo']));

        $response['msg'] = 'Success';
        $response['challanDetails'] = $data;
        return $this->printJson(TRUE, $response);
    }

    private function challanDetail($appId, $challanNo) {

        $oMajorsModel = new \models\MajorsModel();
        $dues = $oMajorsModel->getAmountByMajorIdAndOfferId($appId['offerId'], $appId['majId']);

        $oUsersModel = new \models\UsersModel();
        $userData = $oUsersModel->findByPK($appId['userId'], 'name, fatherName');

        $data['challan_no'] = $challanNo;
        $data['due_date'] = date('Y-m-d', strtotime($appId['endDate']));
        $data['Buyer_Code'] = $appId['formNo'];
        $data['Semester'] = 0;
        $data['nName'] = strtoupper($userData['name']);
        $data['Father_name'] = strtoupper($userData['fatherName']);

        if (!empty($appId['transactionId'])) {
            $data['Due_Amnt'] = 0;
            $data['paid'] = 1;
        } else {
            $data['paid'] = 0;
            $data['Due_Amnt'] = $dues['dues'];
        }
        return $data;
    }
}
