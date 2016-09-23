<?php

use Dez\Config\Config;
use Dez\Db\Connection;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

set_exception_handler(function(\Throwable $exception){
  die('<b>' . get_class($exception) . '</b>: <i>' . $exception->getMessage() . '</b>' . "<pre>{$exception->getFile()}:{$exception->getLine()}</pre>");
});

include_once '../vendor/autoload.php';

$connectionConfig = Config::factory(__DIR__ . '/config.php');
$connectionName = $connectionConfig['db']->get('connection_name', 'development');
$db = new Connection($connectionConfig
  ->path('db.connection')
  ->get($connectionName));
///

$stmt = $db->query('select * from film');

var_dump($stmt->loadArray());
