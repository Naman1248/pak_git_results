<?php

//phpinfo();
//exit();
require_once("loadMihaka.php");
include('config/config.php');

$oMihaka = new \mihaka\Mihaka($applicationConfigs);
$oMihaka->runAwesome();
