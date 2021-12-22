<?php
// turn off error/warning messages
error_reporting(0);
const LOG_FILE = "gallery_err.log";

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
    // DB inserting
    $DB = connectDb();
    if (is_string($DB)) {  // string - means error

        // log error and exit
        logError("Connection (GET): " . $DB);
        sendError([
            'code' => 507, # Insufficient Storage
            'text' => "Internal error 1"
        ]);
    }
    $query = "SELECT * FROM Gallery";
    try {
        $ans = $DB->query($query);
    } catch (PDOException $ex) {
        logError("Select(GET): " . $ex->getMessage() . " " . $query);
        sendError([
            'code' => 507, # Insufficient Storage
            'text' => "Internal error 2"
        ]);
    }
    echo json_encode(
        $ans->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE
    );
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
            'code' => 422, # Unprocessable Entity
            'text' => "Expected field: pictureDescription"
        ]);
    }

    $descr = trim($_POST['pictureDescription']);
    if (strlen($descr) < 2) {
        sendError([
            'code' => 422, # Unprocessable Entity
            'text' => "Content too short: pictureDescription"
        ]);
    }
    if (!isset($_FILES['pictureFile'])) {
        sendError([
            'code' => 422, # Unprocessable Entity
            'text' => "Expected field(file): pictureFile"
        ]);
    }
    if ($_FILES['pictureFile']['size'] < 256) {
        sendError([
            'code' => 422, # Unprocessable Entity
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
            'code' => 422, # Unprocessable Entity
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
            'code' => 507, # Insufficient Storage
            'text' => "File receiving error: pictureFile"
        ]);
    }
    $fill_file_name = $saved_folder . $saved_name . $ext;
    // DB inserting
    $DB = connectDb();
    if (is_string($DB)) {  // string - means error

        // delete uploaded file
        unlink($fill_file_name);
        // log error and exit
        logError("Connection: " . $DB);
        sendError([
            'code' => 507, # Insufficient Storage
            'text' => "Internal error 1"
        ]);
    }
    // prepared queries
    $sql = "INSERT INTO Gallery(id, filename, descr) VALUES(UUID_SHORT(), ?, ?)";
    try {
        $prep = $DB->prepare($sql);
        $prep->execute([$saved_name . $ext, $descr]);
    } catch (PDOException $ex) {
        // delete uploaded file
        unlink($fill_file_name);
        // log error and exit
        logError("Connection: " . $ex->getMessage() . " " . $sql);
        sendError([
            'code' => 507, # Insufficient Storage
            'text' => "Internal error 2"
        ]);
        exit;
    }

    echo "Add OK";
}

function logError($err_text)
{
    $f = fopen(LOG_FILE, "a");
    fwrite($f, date("r") . " " . $err_text . "\r\n");
    fclose($f);
}

function connectDb()
{
    // include db configuration
    unset($db_config);          // name from db_config.php file
    @include "db_config.php";   // include with warning suppress
    if (empty($db_config)) {    // include error
        return "DB config read error";
    }

    // PDO technology
    try {
        $DB = new PDO(
            // "mysql:host=localhost;port=3306;dbname=rest_local"
            "{$db_config['type']}:"
                . "host={$db_config['host']};"
                . "port={$db_config['port']};"
                . "dbname={$db_config['name']};"
                . "charset={$db_config['char']}",
            $db_config['user'],
            $db_config['pass'],
        );
        $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
        return $ex->getMessage();
    }
    return $DB;
}
