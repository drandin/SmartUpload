<?php  namespace SmartUpload;

/**
 * Class File
 * @package SmartUpload\
 */
class File
{
    protected $idFile;
    protected $codeSubject;
    protected $essence;
    protected $idDetails;
    protected $dirStorage;
    protected $vendor;
    protected $prefixUser;
    protected $idUser;
    protected $year;
    protected $month;
    protected $filename;
    protected $dirThumbnail;
    protected $thumbnail;
    protected $size;
    protected $mimeType;
    protected $extension;
    protected $filenameOriginal;
    protected $date;

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                if (property_exists(__CLASS__, $key) && isset($val)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPathToFile()
    {
        return $this->getPath().'/'.$this->getFilename();
    }


    /**
     * @return string
     */
    public function getPathToFileThumbnail()
    {
        if ((string)$this->getDirThumbnail() !== '') {

            return implode('/', [
                $this->getPath(),
                $this->getDirThumbnail(),
                $this->getFilename()
            ]);

        }

        return false;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return implode('/', array_diff([
            (string)$this->dirStorage,
            (string)$this->vendor,
            (string)$this->prefixUser.(string)$this->idUser,
            (string)$this->year,
            (string)$this->month,
        ], ['']));
    }


    public function getIdFile() {
        return (int)$this->idFile;
    }

    public function getCodeSubject() {
        return (int)$this->codeSubject;
    }

    public function getEssence() {
        return (string)$this->essence;
    }

    public function getIdDetails() {
        return (int)$this->idDetails;
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

    public function getFilename() {
        return (string)$this->filename;
    }

    public function getDirThumbnail()
    {
        return (string)$this->dirThumbnail;
    }

    public function getThumbnail()
    {
        return (int)$this->thumbnail;
    }

    public function getSize() {
        return (int)$this->size;
    }

    public function getMimeType() {
        return (string)$this->mimeType;
    }

    public function getExtension() {
        return (string)$this->extension;
    }

    public function getFilenameOriginal() {
        return (string)$this->filenameOriginal;
    }

    public function getDate() {
        return (string)$this->date;
    }


    public function setCodeSubject($codeSubject) {
        if ($codeSubject > 0) {
            $this->codeSubject = (int)$codeSubject;
        }
        return $this;
    }

    public function setEssence($essence) {
        $this->essence = (string)$essence;
        return $this;
    }

    public function setIdDetails($idDetails) {
        if ($idDetails > 0) {
            $this->idDetails = $idDetails;
        }
        return $this;
    }


    public function setDirStorage($dirStorage) {
        $this->dirStorage = (string)$dirStorage;
        return $this;
    }

    public function setVendor($vendor) {
        $this->vendor = (string)$vendor;
        return $this;
    }

    public function setPrefixUser($prefixUser) {
        $this->prefixUser = (string)$prefixUser;
        return $this;
    }


    public function setIdUser($idUser) {
        if ($idUser >= 0) {
            $this->idUser = (int)$idUser;
        }

        return $this;
    }

    public function setYear($year) {
        if ($year > 0) {
            $this->year = (int)$year;
        }

        return $this;
    }

    public function setMonth($month) {
        if ($month > 0) {
            $this->month = (int)$month;
        }

        return $this;
    }

    public function setFilename($filename) {
        $this->filename = (string)$filename;
        return $this;
    }

    public function setDirThumbnail($dirThumbnail) {
        $this->dirThumbnail = (string)$dirThumbnail;
        return $this;
    }

    public function setThumbnail($thumbnail) {

        $thumbnail = (int)$thumbnail;

        if (in_array($thumbnail, [0, 1])) {
            $this->thumbnail = $thumbnail;
        }
        return $this;
    }
    
    public function setSize($size) {
        if ($size >= 0) {
            $this->size = (int)$size;
        }

        return $this;
    }

    public function setMimeType($mimeType) {
        $this->mimeType = (string)$mimeType;
        return $this;
    }

    public function setExtension($extension) {
        $this->extension = (string)$extension;
        return $this;
    }

    public function setFilenameOriginal($filenameOriginal) {
        $this->filenameOriginal = (string)$filenameOriginal;
        return $this;
    }

    public function setDate($date) {
        $this->date = (string)$date;
        return $this;
    }


}