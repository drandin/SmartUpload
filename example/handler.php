<?php

/**
 * @author      Igor Drandin <idrandin@gmail.com>
 * @copyright   2015 Igor Drandin
 */

$cfg = require __DIR__.'/config.php';
require __DIR__.'/../Autoload.php';

/**
 * Short name group of files
 */
$essence = 'images';

/**
 * The ID of the user who owns the image
 */
$idUser = 10;

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {

    /**
     * Code subject which is associated to the files
     */
    $codeSubject = isset($_POST['codeSubject'])
        ? (int)$_POST['codeSubject']
        : 0;

    try {

        $handlerFileUpload = new SmartUpload\HandlerFileUpload();

        $routingFiles = new \SmartUpload\RoutingFiles(
            new \SmartUpload\FileMapper($cfg['tables'], \SmartUpload\DB\DB::getPDO($cfg['db']))
        );

        $resizeImage = new \SmartUpload\ResizeImage();

        $handlerFileUpload
            ->setNameFiles('files')
            ->setMaxFileSize(1024 * 1014)
            ->setMaxQuantityFiles(10);

        $routingFiles
            ->setDirStorage("/fileStorage/images/")
            ->setIdUser(10)
            ->setVendor(null)
            ->setPrefixUser(null)
            ->setYear((int)date('Y'))
            ->setMonth((int)date('m'))
            ->setIdDetails(1);

        $resizeImage
            ->setWidthNew(150)
            ->setHeightNew(150);

        $upload = new \SmartUpload\Upload(
            $handlerFileUpload,
            $routingFiles,
            $resizeImage
        );

        echo json_encode($upload->upload($essence, $codeSubject));
    }
    catch (\PDOException $e) {
        echo $e->getMessage();
    }
    catch (\Exception $e) {
        echo $e->getMessage();
    }

}

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {

    $codeSubject = (int)filter_input(INPUT_GET, 'codeSubject');

    if ($codeSubject > 0 && $idUser > 0) {

        $fileMapper =  new \SmartUpload\FileMapper($cfg['tables'], SmartUpload\DB\DB::getPDO($cfg['db']));

        $searchCriteria = [
            'codeSubject' => $codeSubject,
            'idUser' => $idUser
        ];

        $filesCollection = $fileMapper->getCollection($searchCriteria, 0, 0, 'idFile DESC');

        if (is_object($filesCollection)) {
            require 'view/images.php';
        }
    }
}