<?php

require_once 'include/database.php';
require_once 'include/function.php';
require_once 'include/act.php';

const METHOD_POST = 'POST';

var_dump($_SERVER['REQUEST_METHOD']);

if($_SERVER['REQUEST_METHOD'] !== METHOD_POST){
    echo getResponse(STATUS_ERROR, MESSAGE_INVALID_REQUEST_METHOD);
    die;
}

$act = isset($_GET['act']) ? $_GET['act'] : null;


switch ($act) {
    case ACT_UPLOADER:
        echo uploadData();
        break;
    default:
      echo getResponse(STATUS_ERROR, MESSAGE_INVALID_ID_ACT);
      die();
}

?>