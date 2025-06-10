<?php

/**
 * Description of SuperController
 *
 * @author SystemAnalyst
 */

namespace controllers\apis;

class SuperController extends \mihaka\MihakaController {

    protected $appId;

    public function beforeAction() {
        $headers = $this->headers()->all();
//        SAOh6b7SycZCGnmA2VxxQEEGCU
        if (empty($headers['appKey']) || empty($headers['appId'])) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Not Authorized';
            $this->printAndDieJsonResponse(FALSE, $response);
        }
        $oApiKeysModel = new \models\ApiKeysModel();
        if (empty($oApiKeysModel->validate($headers['appKey'], $headers['appId']))) {
            header("HTTP/1.0 422 UNPROCESSABLE ENTITY");
            $response['status'] = FALSE;
            $response['message'] = 'Not Authorized';
            $this->printAndDieJsonResponse(FALSE, $response);
        }
        $this->appId = $headers['appId'];
    }

    public function afterAction() {
        $headers = $this->headers()->all();
        $post = $this->post()->all();
        $response = $this->getResponse();
        $request = print_r($headers, true) . '\n' . print_r($post, true);

        $oApiLogModel = new \models\ApiLogModel();
        $oApiLogModel->upsert(['appId' => $this->appId, 'url' => $this->getCurrentURL(), 'request' => $request, 'response' => $response, 'responseStatus' => $this->responseStatus]);
    }

}
