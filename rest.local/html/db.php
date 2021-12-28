<?php
// include db configuration
unset($db_config);          // name from db_config.php file
@include "db_config.php";   // include with warning suppress
if (empty($db_config)) {    // include error
    echo "DB config read error";
    exit;
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
}
catch (PDOException $ex) {
    echo "Connection: ", $ex->getMessage();
    exit;
}

$query = <<<SQL
    CREATE TABLE IF NOT EXISTS Gallery (
        id       BIGINT         PRIMARY KEY,
        filename VARCHAR(256),
        descr    TEXT,
        moment   DATETIME       DEFAULT CURRENT_TIMESTAMP
    ) ENGINE = InnoDB DEFAULT CHARSET=UTF8

SQL;
try {
    $DB->query($query);
    echo "Create OK<br/>";
} 
catch (PDOException $ex) {
    echo "Create: ", $ex->getMessage(), $query;
}

echo "Connection OK";