<?php

/**
 * @author      Igor Drandin <idrandin@gmail.com>
 * @copyright   2015 Igor Drandin
 */

namespace SmartUpload;

use SmartUpload\Exception\ExceptionUploadInitialization;

/**
 * Upload files on server
 * Class HandlerFileUpload
 * @package SmartUpload\
 */
class HandlerFileUpload
{
    /**
     * Maximum size each file, that can be upload on server
     * @var int
     */
    private $maxFileSize = 1048576;

    /**
     * Maximum quantity files that can be upload on server
     * @var int
     */
    private $maxQuantityFiles = 10;

    /**
     * Data from $_POST
     * @var array
     */
    private $files = [];

    /**
     * @var null|string
     */
    private $path = null;

    /**
     * @var array
     */
    private $allowedMimeTypes = [
        'image/gif',
        'image/jpeg',
        'image/png'
    ];

    /**
     * @var array
     */
    private $allowedFileExtensions = [
        'gif', 
        'jpg', 
        'jpeg'
    ];


    /**
     * @var int
     */
    protected $filesSize = 0;

    /**
     * @var array
     */
    protected $uploadedFiles = [];

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * @var string
     */
    private $filename = '';

    /**
     * @var string
     */
    private $extension = '';

    /**
     * @var string
     */
    private $mimeType = '';

    /**
     * @var int
     */
    private $idFile = 0;

    /**
     * @var array
     */
    private $errorMessages = [];

    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->setNameFiles($name);
    }


    /**
     * @param $name
     * @return $this
     */
    public function setNameFiles($name)
    {
        $files = !empty($_FILES[(string)$name])
            ? $_FILES[(string)$name]
            : [];

        if (!empty($files)) {
            if ($this->isCorrectArrayFiles($files)) {
                $this->files = $files;
            }
        }
        return $this;
    }

    /**
     * @param $files
     * @return bool
     */
    protected function isCorrectArrayFiles($files)
    {
        $key = [
            'name',
            'tmp_name',
            'error',
            'size',
            'type'
        ];

        if (is_array($files) && sizeof(array_diff_key($key, array_keys($files))) === 0) {
            return true;
        }
        else throw new ExceptionUploadInitialization("Information about loaded files is not!");
    }

    /**
     * It is set path where should upload files. Path of repository files
     * @param $path
     * @return $this
     * @throws ExceptionUploadInitialization
     */
    public function setPath($path)
    {
        $path = rtrim((string)$path, "//\\").'/';

        if (is_dir($path)) {
            if (is_writable($path)) {
                $this->path = $path;
            }
            else {
                throw new ExceptionUploadInitialization("The directory is not available for write");
            }
        }
        else  {
            throw new ExceptionUploadInitialization("The path of the repository file is incorrect");
        }

        return $this;
    }

    /**
     * Specifies the array of allowed file extensions
     * @param array $allowedFileExtensions
     * @return $this
     * @throws ExceptionUploadInitialization
     */
    public function setAllowedFileExtensions(array $allowedFileExtensions )
    {
        if (sizeof($allowedFileExtensions) > 0) {
            $this->allowedFileExtensions = $allowedFileExtensions;
        }
        else throw new ExceptionUploadInitialization("Array of allowed file extensions specified is incorrect");

        return $this;
    }

    /**
     * Specifies the array of allowed MIME types
     * @param array $mimeTypes
     * @return $this
     */
    public function setAllowedMimeTypes(array $mimeTypes)
    {
        if (sizeof($mimeTypes) > 0) {
            $this->allowedMimeTypes= $mimeTypes;
        }
        else throw new ExceptionUploadInitialization("Array of allowed MIME types specified is incorrect!");

        return $this;
    }

    /**
     * @param $maxQuantityFiles
     * @return $this
     * @throws ExceptionUploadInitialization
     */
    public function setMaxQuantityFiles($maxQuantityFiles)
    {
        if ($maxQuantityFiles > 0) {
            $this->maxQuantityFiles = (int)$maxQuantityFiles;
        }
        else throw new ExceptionUploadInitialization("Maximum quantity of files specified is incorrect!");

        return $this;
    }


    /**
     * @param $maxFileSize
     * @return $this
     * @throws ExceptionUploadInitialization
     */
    public function setMaxFileSize($maxFileSize)
    {
        if ($maxFileSize > 0) {
            $this->maxFileSize = (int)$maxFileSize;
        }
        else throw new ExceptionUploadInitialization('Maximum size of file specified is incorrect!');

        return $this;
    }

    /**
     * @return string
     */
    private function getMimeType()
    {
        $finfo = new \finfo(FILEINFO_MIME);
        $mimeType = $finfo->file($this->getTemporaryFilename());
        $part = preg_split('/\s*[;,]\s*/', $mimeType);
        unset($finfo);
        return strtolower($part[0]);
    }


    /**
     * @return $this
     */
    protected function defineMimeType()
    {
        $this->mimeType = $this->getMimeType();
        return $this;
    }

    /**
     * @return $this
     */
    protected function defineExtension()
    {
        $this->extension = $this->getExtension();
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTemporaryFilename()
    {
        return (string)$this->files['tmp_name'][$this->cursor];
    }

    /**
     * @return array
     */
    protected function getTemporaryNameFiles()
    {
        return (array)$this->files['tmp_name'];
    }

    /**
     * @return int
     */
    protected function getFileSize()
    {
        return (int)$this->files['size'][$this->cursor];
    }


    /**
     * @return string
     */
    protected function getOriginalFilename()
    {
        return (string)$this->files['name'][$this->cursor];
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return (string)$this->files['type'][$this->cursor];
    }

    /**
     * @return int
     */
    public function getMaxQuantityFiles()
    {
        return (int)$this->maxQuantityFiles;
    }

    /**
     * @return int
     */
    public function getMaxFileSize()
    {
        return (int)$this->maxFileSize;
    }

    /**
     * @return array
     */
    public function getPropertiesFiles()
    {
        return [
            'idFile' => $this->idFile,
            'filenameOriginal' => $this->getOriginalFilename(),
            'filename' => $this->filename,
            'size' => $this->getFileSize(),
            'type' => $this->getType(),
            'mimeType' => $this->mimeType,
            'extension' => $this->extension,
            'countErrors' => $this->countErrors(),
        ];
    }

    /**
     * @return array
     */
    protected function getReportUploadFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return (string)$this->path;
    }


    /**
     * @return $this
     */
    protected function createResultFileName()
    {
        $this->filename = $this->getUniqueCode().'.'.$this->extension;
        return $this;
    }

    /**
     * @return string
     */
    protected function getUniqueCode()
    {
        return (string)str_replace('.', '', uniqid(rand(10, 99), true));
    }

    /**
     * @param $code
     * @return string
     */
    private function codeErrorToMessage($code)
    {
        switch ((int)$code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    /**
     * @return bool
     */
    protected function checkStandardError()
    {
        $code = (int)$this->files['error'][$this->cursor];

        if ($code !== UPLOAD_ERR_OK) {
            $this->errorMessages[] = $this->codeErrorToMessage($code);
            return false;
        }

        return true;
    }

    /**
     * @return $this
     */
    private function checkCorrectFileSize()
    {
        if ($this->getFileSize($this->cursor) > $this->maxFileSize) {
            $this->errorMessages[] = 'The file is too big';
        }
        elseif ($this->getFileSize($this->cursor) === 0) {
            $this->errorMessages[] = 'The file size is not identified!';
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkExtension()
    {
        if (!preg_match('/^('.implode('|', $this->allowedFileExtensions).'){1}$/i', $this->extension)) {
            $this->errorMessages = 'The file have invalid Extension!';
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkMimeType()
    {
        if (!in_array($this->mimeType, $this->allowedMimeTypes)) {
            $this->errorMessages = 'The file have invalid MIME type!';
        }

        return $this;
    }

    /**
     * @return string
     */
    private function getExtension()
    {
        return substr(strtolower(trim(strrchr($this->getOriginalFilename($this->cursor), '.'))), 1);
    }


    /**
     * @return bool
     */
    protected function checkUploadedFile()
    {
        if (!is_uploaded_file($this->getTemporaryFilename())) {
            $this->errorMessages[] = 'Failed to upload file!';
            return false;
        }

        return true;
    }

    /**
     * @return $this
     */
    protected function saveUploadedFile()
    {
        if (!move_uploaded_file($this->getTemporaryFilename(), $this->path.$this->filename)) {
            $this->errorMessages = 'The file is not uploaded, an error occurred!';
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function clear()
    {
        $this->idFile = 0;
        $this->errorMessages = [];
        $this->filename = '';
        $this->extension = '';
        $this->mimeType = '';

        return $this;
    }

    /**
     * @return $this
     */
    protected function processing()
    {
        if ($this->checkStandardError() && $this->checkUploadedFile()) {

            $this
                ->defineExtension()
                ->defineMimeType()
                ->checkCorrectFileSize()
                ->checkExtension()
                ->checkMimeType();

            if ($this->countErrors() === 0) {
                $this->createResultFileName()->saveUploadedFile();
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    private function countErrors()
    {
        return sizeof($this->errorMessages);
    }

    /**
     * @param \Closure|null $callback
     * @return int
     */
    public function run($callback = null)
    {
        $this->uploadedFiles = [];

        $successCount = 0;

        if (!empty($this->files)) {

            $countFiles = sizeof($this->getTemporaryNameFiles());

            $countUploadFiles = 0;

            for ($this->cursor = 0; $this->cursor <= $countFiles - 1; $this->cursor++) {

                $this->clear()->processing();

                if ($this->countErrors() === 0) {
                    $countUploadFiles++;
                    if (is_callable($callback) && method_exists($callback, 'bindTo')) {
                        $callback = $callback->bindTo($this, __CLASS__);
                        $this->idFile = (int)$callback();
                        if ($this->idFile > 0) $successCount++;
                    }
                }

                $this->uploadedFiles[$this->cursor] = $this->getPropertiesFiles();

                if ($countUploadFiles === $this->maxQuantityFiles) break;
            }
        }

        return $successCount;
    }

}