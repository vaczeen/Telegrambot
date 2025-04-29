<?php
$config = include __DIR__ . '/../config/config.php';

$connectionInfo = [
    "Database" => $config['db']['database'],
    "UID" => $config['db']['user'],
    "PWD" => $config['db']['password']
];
$MsConn = sqlsrv_connect($config['db']['host'], $connectionInfo);
if (!$MsConn) {
    die(print_r(sqlsrv_errors(), true));
}
return $MsConn;