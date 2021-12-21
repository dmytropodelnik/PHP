<?php

$method = strtoupper($_SERVER['REQUEST_METHOD']);

switch ($method) {
    case 'GET' :    doGet(); break;
    case 'POST': 
        if ($dtl == 'file') {
            doPostFile();
        } else {
            doPost(); 
        }  
        break;
    case 'PUT':     
        if ($dtl == 'file') {
            doPutFile();
        } else {
            doPut(); 
        }  
        break;
    case 'DELETE':  doDelete(); break;
    case 'PATCH':  doPatch(); break;

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

function doPut() {
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);
    if (JSON_ERROR_NONE !== json_last_error()) {
        send412("JSON parse error");
    }
    $x = $data['x'];
    $y = $data['y'];
    echo "PUT API works with x=$x, y=$y";
}

function doDelete() {
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);
    if (JSON_ERROR_NONE !== json_last_error()) {
        send412("JSON parse error");
    }
    $x = $data['x'];
    $y = $data['y'];
    echo "DELETE API works with x=$x, y=$y";
}

function doPatch() {
    echo "<pre>";
    print_r ($_FILES);
    print_r ($_SERVER);
}

function doPostFile() {
    // echo "<pre>";
    // print_r ($_FILES);
    // print_r ($_SERVER);

    if (empty($_FILES['userFile'])) {
        sendError([
            'code' => 412,
            'text' => "File must be attached"
        ]);
    }
    if ($_FILES['userFile']['error'] != 0) {
        sendError(
            500,
            "Error uploading file"
        );
    }
    if ($_FILES['userFile']['size'] == 0) {
        sendError("Empty file not allowed");
    } 
    $savedName = $_FILES['userFile']['name'];
    $cnt = 1;
    while (file_exists("uploads/" . $savedName)) {
        $savedName = "($cnt)_" . $_FILES['userFile']['name'];
        $cnt += 1;
    }
    if (move_uploaded_file(
        $_FILES['userFile']['tmp_name'],
        "uploads/" . $savedName)) 
    {
        echo "Upload OK";
    } else {
        sendError("Upload fails");
    }
}

function doPutFile() {
    echo "<pre>";
    print_r ($_FILES);
    print_r ($_SERVER);
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

function send500($msg = "") {
    http_response_code(500);
    echo $msg;
    exit;
}

