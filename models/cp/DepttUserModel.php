<?php

/**
 * Description of depttUserModel
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class DepttUserModel extends \models\SuperModel {

    protected $table = 'depttUser';
    protected $pk = 'id';

    public function login($email, $password) {
//        echo $email.','. $password;
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id,name,dId,role,depttName,role, email')
                ->from($this->table)
                ->where('email', $email)
                ->where('paswrd', md5($password))
                ->find();
//        $oSQLBuilder->printQuery();

        if (empty($data)) {
            return false;
        } else {
            $oMenuItemsModel = new \models\cp\MenuItemsModel();
            $data['menus']=$oMenuItemsModel->getMenuByUserId($data['id'],$data['role']);
//            $data['parentMenus']=$oMenuItemsModel->getParentMenuByUserId($data['id']);
//            $data['childMenus']=$oMenuItemsModel->getChildMenuByUserId($data['id']);
            $this->state()->set('depttUserInfo', $data);
            $oDepttUsersLogModel = new \models\cp\DepttUsersLogModel();
            $oDepttUsersLogModel->add($data);
            return true;
        }
    }
    public function checkValidUser($email, $password) {
//        echo $email.','. $password;
        $oSQLBuilder = $this->getSQLBuilder();
        $data = $oSQLBuilder->select('id')
                ->from($this->table)
                ->where('email', $email)
                ->where('paswrd', md5($password))
                ->find();

        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    public function logout() {
        $this->state()->deleteAll();
        $this->redirect(ADMIN_URL);
    }
}
