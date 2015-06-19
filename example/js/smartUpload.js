/**
 * @author Igor Drandin
 * @copyright 2015 Igor Drandin
 */
;(function() {

    /**
     * @param objData
     * @constructor
     */
    function SmartUpload(objData) {

        var obj = (typeof objData === 'object') ? objData : null;
        var errors = [];

        if (!checkBrowser()) {
            errors.push('Your browser does not support uploading files!');
        }

        if (!checkIncomingData()) {
            errors.push('Incoming data are specified is incorrect!');
        }

        if (errors.length > 0) {
            showMessage(errors, 'uploadArea');
        }

        /**
         * @returns {boolean}
         */
        this.uploadFiles = function() {

            var maxQuantityFiles = +obj.maxQuantityFiles,
                maxFileSize = +obj.maxFileSize,
                codeSubject = +obj.codeSubject,
                additionalData = obj.additionalData,
                idFiles = obj.idFile,
                scriptHandler = obj.scriptHandler,
                uploadMessage = obj.uploadMessage,
                filesData = document.getElementById(idFiles);

            var isImgFile = function(file) {
                return (file.type.match(/image.*/));
            };

            var isExcelFile = function(file) {
                return (file.type.match(/(excel|openxml|xlsx|xls).*/));
            };

             if (uploadMessage) {
                 document.getElementById(uploadMessage).innerHTML = '';
             }

            if (filesData && filesData.files) {

                var data = new FormData(),
                    xhr = new XMLHttpRequest(),
                    messages = [];

                var countFiles = +filesData.files.length;

                if (countFiles <= 0) {
                    messages.push('There are not any files!');
                }

                if (countFiles > maxQuantityFiles) {
                    messages.push('The maximum number of files is ' + maxQuantityFiles + '!');
                }

                if (messages.length === 0) {

                    var isFile = false;

                    for (var i = 0; i <= countFiles - 1; i++) {
                        if (filesData.files[i]) {

                            if (filesData.files[i].size > maxFileSize) {
                                messages.push('File "'+ filesData.files[i].name +'" is too big. The maximum size of file is ' + Math.round(maxFileSize / 1024) + ' Кб.');
                                continue;
                            }

                            if (typeof obj.typeFiles === 'string') {

                                switch (obj.typeFiles) {
                                    case 'img':
                                        if (!isImgFile(filesData.files[i])) {
                                            messages.push('File "'+ filesData.files[i].name +'" has incorrect format! It is not image!');
                                            continue;
                                        }
                                        break;

                                    case 'excel':
                                        if (!isExcelFile(filesData.files[i])) {
                                            messages.push('File "'+ filesData.files[i].name +'" has incorrect format! It is not Excel document!');
                                            continue;
                                        }
                                        break;
                                }

                            }

                            data.append(idFiles + '[]', filesData.files[i]);
                            isFile = true;
                        }
                    }

                    if (isFile) {

                        if (maxQuantityFiles) {
                            data.append('maxQuantityFiles', maxQuantityFiles);
                        }

                        if (maxFileSize) {
                            data.append('maxFileSize', maxFileSize);
                        }

                        data.append('codeSubject', codeSubject);

                        if (typeof additionalData === 'object') {
                            for (var key in additionalData) {
                                if (additionalData[key]) {
                                    data.append(key, additionalData[key]);
                                }
                            }
                        }

                        xhr.open('POST', scriptHandler);

                        xhr.onload = function (e) {
                            try {
                                var jsonData = JSON.parse(e.currentTarget.responseText);

                                for (var key in jsonData) {
                                    if (jsonData[key]['files']) {
                                        if (jsonData[key]['files'].error) {
                                            messages.push('Error upload!');
                                        }
                                    }
                                }

                                obj.successUpload(jsonData);

                            } catch (err) {
                                messages.push('Response from server is incorrect!');
                            }
                        };

                        if (obj.progressBar && obj.progressBarArea) {
                            xhr.upload.onprogress = function (e) {
                                document.getElementById(obj.progressBarArea).style.display = 'block';
                                var percent = (e['loaded'] / e['total'] * 100);
                                document.getElementById(obj.progressBar).style.width = percent + '%';
                                if (percent == 100) {
                                    document.getElementById(obj.progressBarArea).style.display = 'none';
                                }
                            };
                        }

                        xhr.send(data);
                    }

                    showMessage(messages, uploadMessage);
                }
                else {
                    showMessage(messages, uploadMessage);
                }
            }

            return true;
        };

        /**
         * @param msg
         * @param id
         */
        function showMessage(msg, id) {
            if (typeof msg === 'object' && msg instanceof Array) {
                document.getElementById(id).innerHTML = msg.join('<br>');
            }
        }

        /**
         * @returns {boolean}
         */
        function checkIncomingData() {
            if (typeof obj === 'object') {
                if (+obj.maxQuantityFiles > 0 && +obj.maxFileSize > 0 && typeof obj.successUpload === 'function') {

                    var requiredFields = [
                        'idFile',
                        'scriptHandler',
                        'uploadArea',
                        'uploadMessage',
                        'codeSubject'
                    ];

                    for (var i = 0; i < requiredFields.length; i++) {
                        if (!obj[requiredFields[i]]) return false;
                    }

                    return true;
                }
            }
            return false;
        }

        /**
         * @returns {boolean}
         */
        function checkBrowser() {
            return (!!window.FormData && !!window.FormData.prototype);
        }
    }

    window.SmartUpload = SmartUpload;
}());