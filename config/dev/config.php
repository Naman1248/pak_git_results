<?php

define('ENV', 'DEV');
define('SITE_URL', 'http://localhost/');
define('ADMIN_URL', SITE_URL.'cp/');
define('ADMIN_SERVICES_URL', ADMIN_URL.'services/');
define('AJAX_URL', SITE_URL);
define('SERVICES_URL', SITE_URL . 'services/');
define('ASSET_URL', SITE_URL . 'assets/');
define('IMAGE_URL', SITE_URL . 'assets/i/');
define('UPLOAD_PATH', '/mnt/e/gcuonline.pk/assets/uImgs/');
define('PIC_URL', ASSET_URL . 'uImgs/');
$applicationConfigs = [
    'errors' => ['writeErrorFile' => 0, 'errFilePath' => ''],
    'basePath' => '',
    'databases' => [
        'Master' => [
            'userName' => 'root',
            'password' => 'xaMU13zu',
            'dbName' => 'gcuNew',
            'serverName' => 'localhost',
            'port' => '3306',
            'default' => 1,
        ],
        'SLAVE' => [
            'userName' => 'kaiser',
            'password' => 'kaiser',
            'dbName' => 'test',
            'serverName' => 'localhost',
            'port' => '3306',
            'default' => 0,
        ],
        ]];
