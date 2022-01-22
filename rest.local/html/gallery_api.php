<?php
// turn off error/warning messages
error_reporting(0);
const LOG_FILE = "gallery_err.log";

$DB = connectDb() ;
if( is_string( $DB ) ) {  // string - means error
	logError( "DB Connection: " . $DB ) ;
	sendError( [
		'code' => 507,  # Insufficient Storage
		'text' => "Internal error 1.1" ] ) ;
}
// Global context :
$_Page = [
	'langs' => array()
] ;

try {
	$res = $DB->query( "SELECT * FROM langs" ) ;
	while( $row = $res->fetch( PDO::FETCH_NUM ) ) 
		$_Page[ 'langs' ][ $row[ 0 ] ] = $row[ 1 ] ;
}
catch( PDOException $ex ) {
	logError( "Select langs: " . $ex->getMessage() ) ;
	sendError( [
		'code' => 500, 
		'text' => "Internal error 1.2" ] ) ;
}

if( isset( $_GET[ 'langs' ] ) && empty( $_GET[ 'langs' ] ) ) {
	// request for languages list
	header( "Content-Type: application/json" ) ;
	echo json_encode( array_values( $_Page[ 'langs' ] ) ) ;
	exit ;
}
// echo '<pre>' ; var_dump( $_Page ) ; exit ;

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
    global $DB;  // DB connection (PDO) 
    global $_Page;
    #print_r( $_GET ) ; exit ;
    #print_r($_GET); echo is_numeric($_GET['page]) ? "+" : "-";   
    $warn = [];  // Warnings

    // Filters
    $filters = array();
    $filter_part = "";
    // lang
    // TODO: 1. Is lang in request? 2. Is lang valid? 3. Form WHERE clause
    if (isset($_GET['lang'])) {  // 1.
        $lang = strtolower($_GET['lang']);
        if (in_array($lang, $_Page['langs']) || $lang === "all") {  // 2.
            if ($lang === "all") {
                unset($lang);
            } else {
                $filters['lang'] = $lang;
            }    
        } else {
            $warn['lang'] = "Language does not support";
            unset($lang);
        }
    }
	// date
	if( isset( $_GET[ 'date' ] ) ) {
		$date = trim( $_GET[ 'date' ] ) ;
		// Date validation
		if( preg_match( "/^\d{4}-\d{2}-\d{2}$/", $date ) ) {
			$filters[ 'date' ] = $date ;
		}
		else {
			sendError( [
			'code' => 422,  # Unprocessable Entity
			'text' => "Invalid date format. YYYY-MM-DD only" ] ) ;
		}
	}
    // 3. WHERE clauses
    $first_clause = true;
    $filter_part = " ";
    // lang
    if (isset($filters['lang'])) {
        if ($first_clause) {
            $filter_part .= " WHERE ";
            $first_clause = false;
        } else {
            $filter_part .= " AND ";
        }
        $filter_part .= " ( iso639_1 = '{$filters['lang']}' ) ";
    }
    // date
    if (isset($filters['date'])) {
        if ($first_clause) {
            $filter_part .= " WHERE ";
            $first_clause = false;
        } else {
            $filter_part .= " AND ";
        }
        $filter_part .= " ( CAST(moment AS DATE) = '$date' ) ";
    }

    // Pagination 
    // 1. Default values:
    $page     = 1;
    $per_page = 4;
    // 2. Looking for data in GET:
    if (isset($_GET['page'])) {
        if (is_numeric($_GET['page'])) {
            $get_page = intval($_GET['page']);
            if ($_GET['page'] == $get_page) {
                if ($get_page > 0) {
                    $page = $get_page;
                } else {
                    $warn['page'] = "Illegal page number, default used";
                }
            } else {
                $warn['page'] = "Page number unrecognized, default used";
            }
        } else {
            $warn['page'] = "Invalid page number, default used";
        }
    }
    if (isset($_GET['perpage'])) {
        if (is_numeric($_GET['perpage'])) {
            $get_page = intval($_GET['perpage']);
            if ($_GET['perpage'] == $get_page) {
                if ($get_page > 0) {
                    $per_page = $get_page;
                } else {
                    $warn['perpage'] = "Illegal perpage value, default used";
                }
            } else {
                $warn['perpage'] = "Perpage value unrecognized, default used";
            }
        } else {
            $warn['perpage'] = "Invalid perpage value, default used";
        }
    }
    // 3. SQL part
    $pagination_part = " LIMIT "
        . ($per_page * ($page - 1))
        . ", "
        . $per_page;
    // 4. Metadata
    $meta = [
        'page'      => $page,
        'perPage'   => $per_page,
        'lastPage'  => null,
        'total'     => null,
        'filters'   => $filters,
    ];
    /////////////// Total ID in gallery
    $query = "
	SELECT 
		COUNT( DISTINCT G.id )
	FROM 
		Gallery G 
		JOIN Literals L ON L.id_entity = G.id
		JOIN Langs A ON L.id_lang = A.id
	" . $filter_part;
    try {
        $meta['total'] =
            $DB->query($query)->fetch(PDO::FETCH_NUM)[0];
    } catch (PDOException $ex) {
        logError("Select COUNT(GET): " . $ex->getMessage() . " " . $query);
        sendError([
            'code' => 500,
            'text' => "Internal error 3"
        ]);
    }
    $meta['lastPage'] = ceil($meta['total'] / $per_page);
    if ($page > $meta['lastPage'] and $page > 1) {
        $warn['data'] = "Page number exceeded last page";
    }
    /////////////////////// Limited IDs
    $query = "
	SELECT 
		DISTINCT G.id
	FROM 
		Gallery G 
		JOIN Literals L ON L.id_entity = G.id
		JOIN Langs A ON L.id_lang = A.id
	" . $filter_part
      . $pagination_part;
    try {
        $ids = array();
        $ans = $DB->query($query);
        while ($row = $ans->fetch(PDO::FETCH_NUM)) {
            $ids[] = $row[0];

        }
    } catch (PDOException $ex) {
        logError("Select LIMIT(GET): " . $ex->getMessage() . " " . $query);
        sendError([
            'code' => 500,
            'text' => "Internal error 4"
        ]);
    }
	///////////////// WHERE clause for paginator
	$pagination_part = "" ;
	if( strlen( $filter_part ) > 3 ) {  // WHERE in $filter_part
		$pagination_part .= " AND " ;
	}
	else {  // $filter_part  is empty or space only
		$pagination_part .= " WHERE " ;
	}
    $pagination_part .= 
    "G.id IN ( " 
    . implode( ',', $ids ) 
    . " ) " ;
    // 5. Data 
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
	" . $filter_part 
	  . $pagination_part ;
    // echo $query ; exit ;
    $res = array() ;
	try {
		$ans = $DB->query( $query ) ;
		// $res = $ans->fetchAll( PDO::FETCH_ASSOC ) ;
		while( $row = $ans->fetch( PDO::FETCH_ASSOC ) ) {
			if( isset( $res[ $row[ 'id' ] ] ) ) {
				$res[ $row[ 'id' ] ]
					[ 'descr' ]
					[ $row[ 'iso639_1' ] ] = $row[ 'descr' ] ;
			}
			else {
				$res[ $row[ 'id' ] ] = [
					'filename' => $row[ 'filename' ],
					'moment'   => $row[ 'moment' ],
					'descr' => [
						$row[ 'iso639_1' ] => $row[ 'descr' ]
					]
				] ;
			}
		}
	}
	catch( PDOException $ex ) {
		logError( "Select(GET): " . $ex->getMessage() . " " . $query ) ;
		sendError( [
			'code' => 507,  # Insufficient Storage
			'text' => "Internal error 2" ] ) ;
	}
	echo json_encode(
		[
			'meta' => $meta,
			'data' => $res,
			'warn' => $warn
		],
		JSON_UNESCAPED_UNICODE
    );
}

function doPost()
{
    global $DB;  // DB connection (PDO) 
    global $_Page;
    /* Expected:
        $_POST['pictureDescription'] - string
        $_FILES['pictureFile'] - file/image
    */
    #print_r ($_FILES); print_r ($_POST); exit;
    // Primary Validation:
    $res = validatePostData();
    if (is_array($res)) {
        sendError($res);
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
    // generate id for picture
    $sql = " SELECT UUID_SHORT() ";
    try {
        $id = $DB->query($sql)->fetch(PDO::FETCH_NUM)[0];
    } catch (PDOException $ex) {
        logError("UUID_SHORT: " . $ex->getMessage() . " " . $sql);
        sendError([
            'code' => 500,
            'text' => "Internal error 3"
        ]);
    }
    // echo $id ; exit ;
    try {
        // store picture entity
        $sql = "INSERT INTO Gallery(id, filename) VALUES(?, ?)";
        $prep = $DB->prepare($sql);
        $prep->execute([$id, $saved_name . $ext]);
        // store descr entities
        $sql = "INSERT INTO literals(id, id_lang, id_entity, txt) VALUES(UUID_SHORT(), (SELECT id FROM langs WHERE iso639_1 = ? ), $id, ?)";
        $prep = $DB->prepare($sql);
        foreach ($_Page['langs'] as $lang) {
            // capitalize first letter:
            $Lang = ucfirst($lang);  // UpperCaseFIRSTletter
            $key = "pictureDescription$Lang";  // DRY
            if (!empty($_POST[$key])) {
                $prep->execute([
                    $lang,
                    trim($_POST[$key])
                ]);
            }
        }
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

function validatePostData()
{
    global $_Page;
    // Description: at least one
    $hasDescr = false;
    foreach ($_Page['langs'] as $lang) {
        $key = "pictureDescription" . ucfirst($lang);
        if (empty($_POST[$key])) {
            $_POST[$key] = null;
        } else {
            $hasDescr = true;
        }
    }
    if ($hasDescr == false) {
        return ([
            'code' => 422,  # Unprocessable Entity
            'text' => "pictureDescription: at least one should be"
        ]);
    }

    if (!isset($_FILES['pictureFile'])) {
        return ([
            'code' => 422,  # Unprocessable Entity
            'text' => "Expected field(file): pictureFile"
        ]);
    }
    if ($_FILES['pictureFile']['size'] < 256) {
        return ([
            'code' => 422,  # Unprocessable Entity
            'text' => "Content too short: pictureFile"
        ]);
    }
    if (strpos($_FILES['pictureFile']['type'], 'image') !== 0) {
        // file type(MIME) does not start with 'image'
        return ([
            'code' => 415,
            'text' => "Unsupported Media Type (images only): pictureFile"
        ]);
    }

    return true;
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
