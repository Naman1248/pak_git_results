<?php

/**
 * Description of MenuItemsModel
 *
 * @author SystemAnalyst
 */

namespace models\cp;

class MenuItemsModel extends \models\SuperModel {

    protected $table = 'menuItems';
    protected $pk = 'id';

    public function getChildMenuByUserId($userId){
        
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('p.parentMenuId, p.sortOrder, d.menuId, m.name, m.path, m.sortOrder')
                            ->from($this->table . ' m', 'parentMenus p', 'depttUserMenu d')
                            ->join('p.parentMenuId', 'm.parentMenuId')
                            ->join('d.menuId', 'm.menuId')
                            ->where('d.dUserId', $userId) 
                            ->where('m.status','YES')
                            ->orderBy('m.sortOrder')
                            ->findAll();
//        $oSqlBuilder->printQuery();exit;
        if (!empty($data)){
            return $data;
        }
        
    }
    public function getParentMenuByUserId($userId){
        
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('distinct p.parentMenuId, p.parentMenuName, p.sortOrder')
                            ->from($this->table . ' m', 'parentMenus p', 'depttUserMenu d')
                            ->join('p.parentMenuId', 'm.parentMenuId')
                            ->join('d.menuId', 'm.menuId')
                            ->where('d.dUserId', $userId) 
                            ->where('m.status','YES')
                            ->orderBy('p.sortOrder')
                            ->findAll();
//        $oSqlBuilder->printQuery();exit;
        if (!empty($data)){
            return $data;
        }
        
    }
    public function getMenuByUserId($userId, $role) {
        $oSqlBuilder = $this->getSQLBuilder();
        $data = $oSqlBuilder->select('name,iconName,path')
                ->from($this->table . ' m', 'depttUserMenu dm')
                ->join('m.menuId', 'dm.menuId')
                ->where('dm.dUserId', $userId)
                ->where('m.status', 'YES')
                ->orderBy('sortOrder')
                ->findAll();
        if (!empty($data)) {
            return $data;
        }
//        var_dump($data);exit;
        
        if ($role == 'super_admin' && $userId==1) {
            $oSqlBuilder = $this->getSQLBuilder();
            return $oSqlBuilder->select('name,iconName,path')
                            ->from($this->table)
                            ->where('status', 'YES')
                            ->orderBy('sortOrder')
                            ->findAll();
        }
    }

}
