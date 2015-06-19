<?php

/**
 * @author      Igor Drandin <idrandin@gmail.com>
 * @copyright   2015 Igor Drandin
 */

namespace SmartUpload;

use SmartUpload\Exception\ExceptionRoutingFiles;


/**
 * Class RoutingFiles
 * @package SmartUpload\
 */
class RoutingFiles
{
    const ID_DETAILS = 1;
    const THUMBNAIL = 'thumbnail';

    private $dirStorage;
    private $vendor;
    private $prefixUser;
    private $idUser;
    private $year;
    private $month;

    /**
     * @var string
     */
    private $dirThumbnail = self::THUMBNAIL;

    /**
     * @var int
     */
    private $idDetails = self::ID_DETAILS;

    /**
     * @var FileMapper
     */
    private $fileMapper;

    /**
     * @param FileMapper $fileMapper
     */
    public function __construct(FileMapper $fileMapper)
    {
        $this->fileMapper = $fileMapper;
    }


    /**
     * It sets the path to the file
     * @param string $dirStorage
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setDirStorage($dirStorage)
    {
        if (is_string($dirStorage) && mb_strlen($dirStorage, 'UTF-8') <= 255) {
            $this->dirStorage = trim($dirStorage, "//\\");;
        }
        else {
            throw new ExceptionRoutingFiles("Name directory of storage files specified incorrect!");
        }

        return $this;
    }

    /**
     * It sets the Vendor
     * @param string $vendor
     * @return $this
     */
    public function setVendor($vendor = null)
    {
        if (is_string($vendor) && mb_strlen($vendor, 'UTF-8') <= 30) {
            $this->vendor = $vendor;
        }
        elseif (is_null($vendor)) {
            $this->vendor = '';
        }
        else {
            throw new ExceptionRoutingFiles("The vendor name specified is invalid!");
        }

        return $this;
    }

    /**
     * It sets the prefix for user
     * @param string $prefixUser
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setPrefixUser($prefixUser = null)
    {
        if (is_string($prefixUser) && mb_strlen($prefixUser, 'UTF-8') <= 100) {
            $this->prefixUser = $prefixUser;
        }
        elseif (is_null($prefixUser)) {
            $this->prefixUser = '';
        }
        else {
            throw new ExceptionRoutingFiles("The prefix of the user is incorrect!");
        }

        return $this;
    }

    /**
     * It sets the user
     * @param $idUser
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setIdUser($idUser) {

        if ($idUser >= 0) {
            $this->idUser = (int)$idUser;
        }
        else {
            throw new ExceptionRoutingFiles("The parameter 'IdUser' is incorrect!");
        }

        return $this;
    }

    /**
     * It sets the year
     * @param $year
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setYear($year = 0) {
        if ($year >= 0 && $year <= 9999) {
            $this->year = (int)$year;
        }
        else {
            throw new ExceptionRoutingFiles("The parameter 'Year' is incorrect!");
        }

        return $this;
    }

    /**
     * It sets the month
     * @param $month
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setMonth($month = 0) {
        if ($month > 0 && $month <= 12) {
            $this->month = (int)$month;
        }
        else {
            throw new ExceptionRoutingFiles("The parameter 'Month' is incorrect!");
        }

        return $this;
    }

    /**
     * It sets the idDetails
     * @param $idDetails
     * @return $this
     * @throws ExceptionRoutingFiles
     */
    public function setIdDetails($idDetails) {

        if ($idDetails > 0) {
            $this->idDetails = (int)$idDetails;
        }
        else {
            throw new ExceptionRoutingFiles("The parameter 'IdDetails' is incorrect!");
        }

        return $this;
    }


    public function getDirStorage() {
        return (string)$this->dirStorage;
    }

    public function getVendor() {
        return (string)$this->vendor;
    }

    public function getPrefixUser() {
        return (string)$this->prefixUser;
    }

    public function getIdUser() {
        return (int)$this->idUser;
    }

    public function getYear() {
        return (int)$this->year;
    }

    public function getMonth() {
        return (int)$this->month;
    }

    public function getDirThumbnail()
    {
        return (string)$this->dirThumbnail;
    }

    public function getIdDetails()
    {
        return (int)$this->idDetails;
    }

    /**
     * @return bool
     */
    protected function checkInitialization()
    {
       if (!empty($this->dirStorage) && !empty($this->year) && !empty($this->month)) {
           return true;
       }
       else throw new ExceptionRoutingFiles("The parameters defining the files location is not set!");
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return implode('/', $this->getArrayPath());
    }

    /**
     * @return array
     */
    private function getArrayPath()
    {
        return array_diff([
            (string)$this->dirStorage,
            (string)$this->vendor,
            (string)$this->prefixUser.(string)$this->idUser,
            (string)$this->year,
            (string)$this->month,
        ], ['']);
    }


    /**
     * @return string
     */
    public function getPathThumbnail()
    {
        $dirThumbnail = $this->getDirThumbnail();

        if ($dirThumbnail !== '') {
            return $this->getPath().'/'.$dirThumbnail;
        }

        return false;
    }

    /**
     * Creating the hierarchy of directories to files
     * and setting permissions on directories
     * @return $this
     */
    public function createPath()
    {
        if ($this->checkInitialization()) {

            $path = $this->getPath();

            if (!is_dir($path)) {

                $arrayPath = $this->getArrayPath();

                $dirElement = '';

                $i = 0;

                foreach ($arrayPath as $nameElement => $dir) {

                    $i++;

                    $dirElement.= $dir.'/';

                    if (!file_exists(rtrim($dirElement, "//\\"))) {

                        $mode = ($i == sizeof($arrayPath))
                            ? 0777
                            : 0755;

                        $pathFull = rtrim($dirElement, "//\\");

                        if (mkdir($pathFull)) {
                            if (!chmod($pathFull, $mode)) {
                                throw new ExceptionRoutingFiles("Failed to modify permissions for directory!");
                            }
                        }
                        else throw new ExceptionRoutingFiles("Create directory '$pathFull' failed!");
                    }
                }

                if (!is_dir($path)) {
                    throw new ExceptionRoutingFiles("Create directories failed!");
                }
            }
        }

        return $this;
    }


    /**
     * Creating directory for thumbnails of images
     * and setting permissions on this directory
     * @return $this
     * @throws \Exception
     */
    public function createDirThumbnail()
    {
        $dirThumbnail = $this->getDirThumbnail();

        if (ctype_alnum($dirThumbnail)) {

            $pathThumbnail = $this->getPathThumbnail();

            if (!is_dir($pathThumbnail)) {
                if (mkdir($pathThumbnail)) {
                    if (!chmod($pathThumbnail, 0777)) {
                        throw new ExceptionRoutingFiles("Failed to modify permissions for directory '{$dirThumbnail}'!");
                    }
                }
                else throw new ExceptionRoutingFiles("Create directory for thumbnails of images failed!");
            }
        }
        else throw new ExceptionRoutingFiles("The name of directory for thumbnails of images is not set!");

        return $this;
    }


    /**
     * It creates file .htaccess
     * @param $path
     * @param $access
     * @param bool $rewrite
     * @return bool
     */
    public function writeHTAccess($path, $access, $rewrite = false)
    {
        if (in_array($access, ['allow', 'deny']) && is_bool($rewrite)) {

            if (!is_string($path) || !is_dir($path)) {
                throw new ExceptionRoutingFiles("Directory does not exist!");
            }

            $fileHTAccess = rtrim($path, '//\\').'/'.".htaccess";

            if (!file_exists($fileHTAccess) || $rewrite) {

                $str = "<Files *>\n";
                $str.= "order deny,allow\n";
                $str.= $access." from all\n";
                $str.= "</files>";

                $f = fopen($fileHTAccess, 'w');

                if (fwrite($f, $str) !== false) {
                    fclose($f);
                    return true;
                }
            }
        }
        else {
            throw new ExceptionRoutingFiles("Incorrect parameters for write .htaccess!");
        }

        return false;
    }


    /**
     * @param $idFile
     * @param string $contentDisposition - ['inline', 'attachment']
     * 'inline' - displaying in the browser
     * 'attachment' - downloading file
     */
    public function fileOutput($idFile, $contentDisposition = null)
    {
        if ($idFile > 0) {

            $contentDisposition = in_array($contentDisposition, ['inline', 'attachment'])
                ? $contentDisposition
                : 'attachment';

            $file = $this->fileMapper->getOne((int)$idFile);

            if (!empty($file) && is_file($file->getPathToFile())) {
                header("Pragma: no-cache");
                header("Content-Type: ".$file->getMimeType());
                header("Content-Length: ".$file->getSize());
                header('Content-Disposition: '.$contentDisposition.'; filename="'.$file->getFilenameOriginal().'"');
                readfile($file->getPathToFile());
            }
        }
    }

    /**
     * @param File $file
     * @return int
     */
    public function addFileInStorage(File $file)
    {
        return $this->fileMapper->insertData($file);
    }

    /**
     * @param int $idFile
     * @return bool
     */
    public function deleteFile($idFile)
    {
        if (is_int($idFile) && $idFile > 0) {

            $file = $this->fileMapper->getOne((int)$idFile);

            if (is_object($file) && $this->fileMapper->delete($file->getIdFile())) {

               $pathToFileThumbnail = $file->getPathToFileThumbnail();

                if (is_file($pathToFileThumbnail)) {
                    unlink($file->getPathToFileThumbnail());
                }

                return unlink($file->getPathToFile());
            }
        }

        return false;
    }



}