<?php
$dtl = empty($_GET['dtl']) 
    ? "home" 
    : trim($_GET['dtl']);

if ($dtl == "gallery") {
    include "gallery_api.php";
    exit;
}

include "api.php";


function sendError($err = 400, $msg = "Bad request") {
    $code = 400;
    if (is_int($err)) {
        $code = $err;
    }
    else if (is_string($err)) {
        $msg = $err;
    }
    else if (is_array($err)) {
        if (isset($err['code'])) {
            $code = $err['code'];
        }
        if (isset($err['text'])) {
            $msg = $err['text'];
        }
    }
    http_response_code($code);
    echo $msg;
    exit;
}