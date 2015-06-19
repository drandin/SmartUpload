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

HTML form:

```html
<div class="container">
    <br>
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
```
