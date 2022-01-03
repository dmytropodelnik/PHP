<?php
// group.php - query result reordering
$query = <<<SQL
SELECT
    G.id,
    A.iso639_1,
    L.txt
FROM
    Gallery G
    JOIN Literals L ON L.id_entity = G.id
    JOIN Langs A ON L.id_lang = A.id
SQL;

$grp = array();
$DB = connectDb();

try {
    $res = $DB->query($query);
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        if (isset($grp[$row['id']])) {
            $grp[$row['id']]
                [$row['iso639_1']] = $row['txt'];
        } 
        else {
            $grp[$row['id']] = 
                [$row['iso639_1'] => $row['txt']];
        }
    }
}
catch (PDOException $ex) {
    echo $ex->getMessage();
    exit;
}

echo "<pre>"; print_r($grp);
/////////////////////////////////////////////////////////
$query = "
SELECT
    G.id,
    G.filename,
    G.moment,
    A.iso639_1,
    L.txt AS descr
FROM 
    Gallery G 
    JOIN Literals L ON L.id_entity = G.id
    JOIN Langs A ON L.id_lang = A.id
";

$grp = array();
try {
    $res = $DB->query($query);
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        if (isset($grp[$row['id']])) {
            $grp[$row['id']]
                ['descr']
                [$row['iso639_1']] = $row['descr'];
        }
        else {
            $grp[$row['id']] = [
                'filename' => $row['filename'],
                'moment' => $row['moment'],
                'descr' => [
                    $row['iso639_1'] => $row['descr']
                ]
            ];
        }
    }
    print_r($grp);
}
catch (PDOException $ex) {
    echo $ex->getMessage();
    exit;
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