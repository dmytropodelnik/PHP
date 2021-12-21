<?php

echo "API works";

$method = strtoupper($_SERVER['REQUEST_METHOD']);

switch ($method) {
    case 'GET':
        doGet();
        break;
    case 'POST':
        doPost();
        break;
}

function doGet()
{
    echo "GET works";
}

function doPost()
{
    /* Expected:
        $_POST['pictureDescription'] - string
        $_FILES['pictureFile'] - file/image
    */
    // Primary Validation:
    if (!isset($_POST['pictureDescription'])) {
        sendError([
            'code' => 412,
            'text' => "Expected field: pictureDescription"
        ]);
    }

    $dsecr = trim($_POST['pictureDescription']);
    if (strlen($dsecr) < 2) {
        sendError([
            'code' => 412,
            'text' => "Content too short: pictureDescription"
        ]);
    }
    if (!isset($_FILES['pictureFile'])) {
        sendError([
            'code' => 412,
            'text' => "Expected field(file): pictureFile"
        ]);
    }
    if ($_FILES['pictureFile']['size'] < 256) {
        sendError([
            'code' => 412,
            'text' => "Content too short: pictureFile"
        ]);
    }
    if (strpos($_FILES['pictureFile']['type'], 'image') !== 0) {
        // file type (MIME) does not start with 'image'
        sendError([
            'code' => 415,
            'text' => "Unsupported Media Type (images only): pictureFile"
        ]);
    }
    // Secondary validation: moving uploaded file
    // file extension: 
    $dot_pos = strpos($_FILES['pictureFile']['name'], '.');
    if ($dot_pos === false) {
        // no dot in file name
        sendError([
            'code' => 412,
            'text' => "Meta error: pictureFile should have extension"
        ]);
    }
    $ext = substr($_FILES['pictureFile']['name'], $dot_pos);
    $saved_name = md5($_FILES['pictureFile']['tmp_name']);
    $saved_folder = "pictures/";
    while (file_exists($saved_folder . $saved_name . $ext)) {
        $saved_name = md5($saved_name . rand());
    }
    if (!move_uploaded_file(
        $_FILES['pictureFile']['tmp_name'],
        $saved_folder . $saved_name . $ext
    )) {
        sendError([
            'code' => 409,
            'text' => "File receiving error: pictureFile"
        ]);
    }

    echo $saved_name . $ext;
}
