<?php

ini_set('display_errors', 1);

// use DLRoute\DLRoute;
use DLRoute\Server\DLServer;

include dirname(__DIR__) . "/vendor/autoload.php";

$user_agent = DLServer::get_user_agent();


echo $user_agent;