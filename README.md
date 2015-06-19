# SmartUpload
Upload files on server, сreate thumbnail of images, validating files

<h2>Example</h2>

http://drandin.ru/SmartUpload/example/

<h2>Features</h2>

1.	Multiple file selection
2.	Progress bar
3.	Create thumbnail of images
4.	Validating files
5.	Uploading files in the directory hierarchy
6.	Storing information about the files in the database
7.	Restricting access to files

<h2>How use SmartUpload?</h2>

<h3>HTML form</h3>

Fragment of file index.html

```html
<div id="uploadArea">
    <table class="table-upload">
        <tr>
            <td class="td-button">
            <span class="btn btn-success file-input-button">
            <i class="glyphicon glyphicon-plus"></i>
            <span>To attach files...</span>
            <input id="files" type="file" multiple="true">
            </span>
            </td>
            <td class="td-progressbar">
                <div class="progress progress-custom" id="progressBarArea">
                    <div class="progress-bar progress-bar-success progress-bar-striped"
                         id="progressBar" role="progressbar" aria-valuenow="0"
                         aria-valuemin="0" aria-valuemax="100" style="width:0">
                        <span class="sr-only">%</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div id="uploadMessage" class="uploadMessage"></div>
    <div id="attachedFiles" class="attachedFiles"></div>
</div>
```
JavaScript code:

````javascript
    $(function() {

        var obj = {
            idFile: 'files',
            scriptHandler: 'handler.php',
            progressBar: 'progressBar',
            progressBarArea: 'progressBarArea',
            uploadArea: 'uploadArea',
            uploadMessage: 'uploadMessage',
            maxQuantityFiles: 6,
            maxFileSize: 1024 * 1014,
            codeSubject: 1,
            typeFiles: 'img',
            additionalData: {
                codeExample: '1'
            },
            successUpload: function(json) {
                showImages();
            }
        };

        var smartUpload = new SmartUpload(obj);

        var showImages = function() {
            $('#attachedFiles').load(obj.scriptHandler + '?codeSubject=' + obj.codeSubject);
        };

        $('#files').change(function() {
            smartUpload.uploadFiles();
        }).click(function() {
            $('#' + obj.uploadMessage).empty();
        });

        showImages();

        $('#attachedFiles').on('click', '.deleteImg', function() {
            if (this.id > 0) {
                $.ajax({
                    'type': 'POST',
                    'cache': false,
                    'url': 'delete.php',
                    'data': {'idFile': this.id},
                    'success': function(json) {
                            try {
                                var jsonData = JSON.parse(json);

                                if (jsonData.result === true) {
                                    showImages();
                                }

                            } catch (err) {
                                console.log(err + 'Response from server is incorrect!');
                            }
                    }
                });
            }
        });
    });
```

<h3>The server-side PHP</h3>

heandler.php

```php

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
```

fileOutput.php

```php
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
```

delete.php

```php
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
```

config.php

```php
return [

    /**
    * Parameters access to DB
    */
    'db' => [
    
         // Host
        'host' => 'localhost',
        
        // Name DB
        'dbname' => '*',
        
        // Namme user
        'user' => '*',
        
        // Password for access to DB
        'password' => '*',

    ],

    /**
    * Name tables in DB
    */
    'tables' => [
        'fileStorage' => 'fileStorage',
    ]

];
```



