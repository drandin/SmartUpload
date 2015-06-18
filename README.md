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

How use SmartUpload?

HTML form

<pre>
<div class="container">
    <br>
    <div id="uploadArea">
        <table class="table-upload">
            <tr>
                <td class="td-button">
                <span class="btn btn-success file-input-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Прикрепить файлы...</span>
                <input id="files" type="file" multiple="true">
                </span>
                </td>
                <td class="td-progressbar">
                    <div class="progress progress-custom" id="progressBarArea">
                        <div class="progress-bar progress-bar-success progress-bar-striped" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0">
                            <span class="sr-only">%</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div id="uploadMessage" class="uploadMessage"></div>
        <div id="attachedFiles" class="attachedFiles"></div>
    </div>
</div>
<script>

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

</script>

</pre>   

