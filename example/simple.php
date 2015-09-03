<?php

use Dez\Config\Adapter\Json as JsonConfig;
use Dez\Config\Adapter\Ini  as IniConfig;
use Dez\Db\Connection;

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );

include_once '../vendor/autoload.php';

$config     = new JsonConfig( __DIR__ . '/config.json' );
$config->merge( new IniConfig( __DIR__ . '/config.ini' ) );

$connectionConfig           = $config['db']['connection'];
$connectionConfig['schema'] = $config['db']['schema'];

$db         = new Connection( $connectionConfig );
$stmt       = $db->query( 'select * from robots' );

var_dump( $stmt->loadIntoObject( new stdClass() ) );