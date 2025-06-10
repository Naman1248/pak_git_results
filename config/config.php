<?php

$server = $_SERVER['HTTP_HOST'];
if (stristr($server, 'localhost')) {
    $envFolder = 'dev/';
} else {
    $envFolder = 'live/';
}
include($envFolder . 'config.php');
define('FRM_APPLY',1);
define('FRM_PROFILE',2);