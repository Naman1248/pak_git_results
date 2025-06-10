<?php

/**
 * Description of ManageRightsController
 *
 * @author SystemAnalyst
 */

namespace controllers\cp;

class ManageRightsController extends \controllers\cp\StateController {

    public function userRightsAction() {
        $data['depttName'] = '';
        $data['depttUser'] = '';
        $data['cntrlName'] = '';

        if ($this->isPost()) {
            $post = $this->post()->all();
            $data['depttUser'] = $post['depttUser'];

            $data['cntrlName'] = $post['cntrlName'];
            $data['depttName'] = $post['depttName'];

            $oDepttUsersModel = new \models\cp\DepttUserModel();
            $data['depttUsers'] = $oDepttUsersModel->findByField('dId', $post['depttName'], 'id,name');

            if (!empty($post['depttUser']) && !empty($post['cntrlName']) && !empty($post['depttName'])) {
                $data['depttUser'] = $post['depttUser'];
                $oMenuModel = new \models\cp\MenuItemsModel();
                $data['results'] = $oMenuModel->findByField('controllerName', $post['cntrlName'], 'menuId, name');
                $ouserMenuModel = new \models\cp\DepttUserMenuModel();
                $userMenus = $ouserMenuModel->menuByDIdAndUserId($post['depttName'], $post['depttUser']);
//                print_r($userMenus);exit;
                foreach ($data['results'] as $key => $rowParent) {
                    if (in_array($rowParent['menuId'], $userMenus)) {
                        $data['results'][$key]['assigned'] = 'YES';
                    } else {
                        $data['results'][$key]['assigned'] = 'NO';
                    }
                }
            }
            $data['depttName'] = $post['depttName'];
        }
        $oDepttModel = new \models\DepttModel();
        $data['departments'] = $oDepttModel->findAll();
        $data['controllersList'] = \helpers\Common::controllerList();
//        echo "<pre>";print_r($data);exit;
        $this->render('userRights', $data);
    }

}
