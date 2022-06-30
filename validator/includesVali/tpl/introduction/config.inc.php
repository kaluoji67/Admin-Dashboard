<?php

//define('SQLVALI_DB_SYSTEM', 'postgresql');
//define('SQLVALI_DB_HOSTNAME', '127.0.0.1');
//define('SQLVALI_DB_USERNAME', 'sqlvalidator');
//define('SQLVALI_DB_PASSWORD', 'Datenbanken2015');
//define('SQLVALI_DB_DBNAME', 'sqlvalidator');

define('SQLVALI_DB_SYSTEM', 'mysql');
define('SQLVALI_DB_HOSTNAME', '127.0.0.1');
define('SQLVALI_DB_USERNAME', 'propra14');  // propra14  root
define('SQLVALI_DB_PASSWORD', 'es70cjY5');   //es70cjY5 ''
define('SQLVALI_DB_DBNAME', 'sqlvali_data');
define('SQLVALI_DB_DBNAMEEVAL', 'sqlvali_eval');

function calltrace() {
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }

    return "\t" . implode("\n\t", $result);
}

function db_connection($username = SQLVALI_DB_USERNAME, $password = SQLVALI_DB_PASSWORD, $dbname = SQLVALI_DB_DBNAME) {
    if(SQLVALI_DB_SYSTEM == 'mysql') {
        return new MySQLiConnection(SQLVALI_DB_HOSTNAME, $username, $password, $dbname);
    } else if(SQLVALI_DB_SYSTEM == 'postgresql') {
        return new PostgreSQLConnection(SQLVALI_DB_HOSTNAME, $username, $password, $dbname);
    }
}
