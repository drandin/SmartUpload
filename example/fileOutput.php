<?php

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {

    $cfg = require __DIR__ . '/config.php';
    require __DIR__ . '/../Autoload.php';

    $routingFiles = new \SmartUpload\RoutingFiles(
        new \SmartUpload\FileMapper($cfg['tables'], \SmartUpload\DB\DB::getPDO($cfg['db']))
    );

    $idFile = (int)filter_input(INPUT_GET, 'idFile');

    if ($idFile > 0) {
        $routingFiles->fileOutput($idFile, (string)filter_input(INPUT_GET, 'disposition'));
        exit;
    }
}