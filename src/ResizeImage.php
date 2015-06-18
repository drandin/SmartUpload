<?php namespace SmartUpload;

use SmartUpload\Exception\ExceptionResizeImage;

/**
 * Class ResizeImage
 * @package SmartUpload\
 */
class ResizeImage implements IResizeImage
{
    /**
     * @var string
     */
    protected $filenameOriginal = '';

    /**
     * @var string
     */
    protected $filenameResult = null;

    /**
     * @var int
     */
    protected $widthNew = 0;

    /**
     * @var int
     */
    protected $heightNew = 0;

    /**
     * @var int
     */
    protected $qualityJpeg = 75;

    /**
     * @var array
     */
    private $propertiesImage = [];

    /**
     * @var null|resource
     */
    private $resourceFile = null;

    /**
     * @var array
     */
    public static $imageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    /**
     * @param null $filenameOriginal
     * @param null $filenameResult
     * @throws ExceptionResizeImage
     */
    public function __construct($filenameOriginal = null, $filenameResult = null)
    {
        if (!is_null($filenameOriginal)) {
            $this->setFilenameOriginal($filenameOriginal);
        }

        if (!is_null($filenameResult)) {
            $this->setFilenameResult($filenameResult);
        }
    }

    /**
     * @param $filenameOriginal
     * @return $this
     * @throws ExceptionResizeImage
     */
    public function setFilenameOriginal($filenameOriginal)
    {
        if (is_string($filenameOriginal) && is_readable($filenameOriginal)) {
            $this->filenameOriginal = $filenameOriginal;
            $this->setPropertiesImage();
            $this->openFile();
        }
        else throw new ExceptionResizeImage('Filename incoming image is not specified!');

        return $this;
    }

    /**
     * @param null $filenameResult
     * @return $this
     * @throws ExceptionResizeImage
     */
    public function setFilenameResult($filenameResult = null)
    {
        if (is_string($filenameResult) || is_null($filenameResult)) {
            $this->filenameResult = $filenameResult;
        }
        else throw new ExceptionResizeImage('filenameOriginal is incorrect!');

        return $this;
    }

    /**
     * @throws ExceptionResizeImage
     */
    private function setPropertiesImage()
    {
        $this->propertiesImage = getimagesize($this->filenameOriginal);
        if (empty($this->propertiesImage)) {
            throw new ExceptionResizeImage('The File which has specified is not image!');
        }
    }

    /**
     * @param $widthNew
     * @return $this
     * @throws ExceptionResizeImage
     */
    public function setWidthNew($widthNew)
    {
        if ($widthNew > 0) {
            $this->widthNew = (int)$widthNew;
        }
        else throw new ExceptionResizeImage('The width of the new image is incorrect!');

        return $this;
    }

    /**
     * @param $heightNew
     * @return $this
     * @throws ExceptionResizeImage
     */
    public function setHeightNew($heightNew)
    {
        if ($heightNew > 0) {
            $this->heightNew = (int)$heightNew;
        }
        else throw new ExceptionResizeImage('The File which has specified as source original image is not image!');

        return $this;
    }

    /**
     * @param $quality
     * @throws ExceptionResizeImage
     */
    public function setQualityJpeg($quality)
    {
        if ($quality >= 0 && $quality <= 100) {
            $this->qualityJpeg = (int)$quality;
        }
        else throw new ExceptionResizeImage('The quality of image (.jpeg) specified is incorrect. Value of quality must be from 0 to 100!');
    }
    
    /**
     * @return int
     */
    protected function getWidthOriginal()
    {
        return (int)$this->propertiesImage[0];
    }

    /**
     * @return int
     */
    protected function getHeightOriginal()
    {
        return (int)$this->propertiesImage[1];
    }

    /**
     * @return string
     */
    protected function getMIME()
    {
        return (string)$this->propertiesImage['mime'];
    }

    /**
     * @param $width
     * @param $height
     * @return array
     * @throws ExceptionResizeImage
     */
    protected function getCoordinatesRectangle($width, $height)
    {
        if ($width > 0 && $height > 0) {

            $ratio = $height / $width;
            $ratioOriginal = $this->getHeightOriginal() / $this->getWidthOriginal();

            if ($ratio > $ratioOriginal) {
                $widthResult = (int)(($width / $height) * $this->getHeightOriginal());
                $heightResult = $this->getHeightOriginal();
                $yOne = 0;
                $xOne = (int)($this->getWidthOriginal() / 2) - (int)($widthResult / 2);
                $yTwo = $heightResult;
                $xTwo = $xOne + $widthResult;
            }
            else {
                $heightResult = (int)($ratio * $this->getWidthOriginal());
                $widthResult = $this->getWidthOriginal();
                $xOne = 0;
                $yOne = (int)($this->getHeightOriginal() / 2) - (int)($heightResult / 2);
                $yTwo = $yOne + $heightResult;
                $xTwo = $widthResult;
            }

            return [
                'xOne' => $xOne,
                'yOne' => $yOne,
                'xTwo' => $xTwo,
                'yTwo' => $yTwo
            ];

        }
        else throw new ExceptionResizeImage('Parameters Width or Height of image were specified incorrectly!');
    }

    /**
     * @return array
     */
    protected function getCoordinatesSquare()
    {
        if (!$this->isOriginalImageSquare()) {

            $longSide = max(
                $this->getWidthOriginal(),
                $this->getHeightOriginal()
            );

            $shortSide = min(
                $this->getWidthOriginal(),
                $this->getHeightOriginal()
            );

            $halfLong = floor($longSide / 2);
            $halfShort = floor($shortSide / 2);

            $xOne = $halfLong - $halfShort;
            $xTwo = $halfLong + $halfShort;
            $yOne = 0;
            $yTwo = $shortSide;

            if (($longSide != $this->getWidthOriginal())) {
                list($xOne, $yOne) = [$yOne, $xOne];
                list($xTwo, $yTwo) = [$yTwo, $xTwo];
            }

            $coordinates = [
                'xOne' => $xOne,
                'yOne' => $yOne,
                'xTwo' => $xTwo,
                'yTwo' => $yTwo
            ];

        }
        else {
            $coordinates = [
                'xOne' => 0,
                'yOne' => 0,
                'xTwo' => $this->getWidthOriginal(),
                'yTwo' => $this->getHeightOriginal()
            ];
        }

        return $coordinates;
    }

    /**
     * @return bool
     */
    protected function isImage()
    {
        return in_array($this->getMIME(), self::$imageMimeTypes);
    }

    /**
     * @param bool $outputToBrowser
     * @return bool
     * @throws ExceptionResizeImage
     */
    public function create($outputToBrowser = false)
    {
        if ($this->isImage($this->filenameOriginal)) {

            if (!is_bool($outputToBrowser)) {
                throw new ExceptionResizeImage('Parameter outputToBrowser is incorrect!');
            }

            if (!$outputToBrowser && is_null($this->filenameResult)) {
                throw new ExceptionResizeImage('Parameter filenameOriginal is incorrect!');
            }

            $newImageResource = imagecreatetruecolor($this->widthNew, $this->heightNew);

            if (is_resource($newImageResource)) {

                $coordinatesSquare = ($this->widthNew === $this->heightNew)
                    ? $this->getCoordinatesSquare()
                    : $this->getCoordinatesRectangle($this->widthNew, $this->heightNew);

                $res = imagecopyresampled(
                    $newImageResource,
                    $this->resourceFile,
                    0,
                    0,
                    $coordinatesSquare['xOne'],
                    $coordinatesSquare['yOne'],
                    $this->widthNew,
                    $this->heightNew,
                    $coordinatesSquare['xTwo'] - $coordinatesSquare['xOne'],
                    $coordinatesSquare['yTwo'] - $coordinatesSquare['yOne']
                );

                if ($res) {
                    if (!$outputToBrowser) return $this->makeImage($newImageResource, $this->filenameResult);
                    else return $this->makeImage($newImageResource, null);
                }
                else throw new ExceptionResizeImage('Image processing was stopped!');
            }
            else throw new ExceptionResizeImage('Operation create a new image cannot be performed!');
        }
        else throw new ExceptionResizeImage('The File which has specified as source original image has incorrect MIME type!');
    }


    /**
     * @param $resource
     * @param null|string $filenameResult
     * @return bool
     */
    protected function makeImage($resource, $filenameResult = null)
    {
        switch($this->getMIME())
        {
            case 'image/jpeg':
                $res = imagejpeg($resource, $filenameResult, $this->qualityJpeg);
                break;
            case 'image/png':
                $res = imagepng($resource, $filenameResult);
                break;
            case 'image/gif':
                $res = imagegif($resource, $filenameResult);
                break;
        }

        return empty($res) ? false: $res;
    }


    /**
     * @throws ExceptionResizeImage
     */
    protected function openFile()
    {
        switch($this->getMIME())
        {
            case 'image/jpeg':
                $this->resourceFile = imagecreatefromjpeg($this->filenameOriginal);
                break;
            case 'image/png':
                $this->resourceFile = imagecreatefrompng($this->filenameOriginal);
                break;
            case 'image/gif':
                $this->resourceFile = imagecreatefromgif($this->filenameOriginal);
                break;
            default:
                throw new ExceptionResizeImage('Specified type '.$this->getMIME().' of file is not supported ');
                break;
        }

        if (!is_resource($this->resourceFile)) {
            throw new ExceptionResizeImage('Image resource identifier was not created!');
        }
    }

    /**
     * @return bool
     */
    protected function isOriginalImageSquare()
    {
        return ($this->getWidthOriginal() === $this->getHeightOriginal());
    }


}