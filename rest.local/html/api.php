<?php

$method = strtoupper($_SERVER['REQUEST_METHOD']);
switch ($method) {
    case 'GET' : doGet(); break;
    case 'POST': doPost(); break;
    default: send418();
}

function doGet() {
    if (!isset($_GET['x'])) {
        send412("Parameter 'x' required.");  // exit inside send()
    }
    if (!is_numeric($_GET['x'])) {
        send412("Parameter 'x' shoudl be numeric.");  
    }
    if (!isset($_GET['y'])) {
        send412("Parameter 'y' required.");  // exit inside send()
    }
    if (!is_numeric($_GET['y'])) {
        send412("Parameter 'y' shoudl be numeric.");  
    }
    $x = $_GET['x'];
    $y = $_GET['y'];

    echo "GET API works with x=$x, y=$y";
}

function doPost() {
    // var_dump($_POST);
    $contentType = strtolower(trim($_SERVER['CONTENT_TYPE']));
    if ($contentType == 'application/json') {
        $body = file_get_contents("php://input");
        $data = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            send412("JSON parse error");
        }
        $x = $data['x'];
        $y = $data['y'];
        echo "POST API works with x=$x, y=$y";
    } else if ($contentType == 'application/x-www-form-urlencoded') {
        var_dump($_POST);
    } else {
        send415();
    }

    // var_dump($_SERVER);
}

function send412($msg = "") {
    http_response_code(412);
    echo $msg;
    exit;
}

function send415($msg = null) {
    http_response_code(415);
    if (is_null($msg)) {
        $msg = "Unsupported Media Type";
    }
    echo $msg;
    exit;
}

function send418() {
    http_response_code(418);
    echo "API method does not support";
    exit;
}