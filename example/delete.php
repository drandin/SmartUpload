<?php

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {

    $cfg = require __DIR__ . '/config.php';
    require __DIR__ . '/../Autoload.php';

    $routingFiles = new \SmartUpload\RoutingFiles(
        new \SmartUpload\FileMapper($cfg['tables'], \SmartUpload\DB\DB::getPDO($cfg['db']))
    );

    $idFile = filter_input(INPUT_POST, 'idFile');

    if (ctype_digit($idFile)) {
        echo json_encode(['result' => $routingFiles->deleteFile((int)$idFile)]);
    }

}