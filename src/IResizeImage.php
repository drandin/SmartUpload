<?php

/**
 * @author      Igor Drandin <idrandin@gmail.com>
 * @copyright   2015 Igor Drandin
 */

namespace SmartUpload;

/**
 * Interface IResizeImage
 */
interface IResizeImage
{
    /**
     * @param $filenameOriginal
     * @return $this
     * @throws \Exception
     */
    public function setFilenameOriginal($filenameOriginal);

    /**
     * @param null $filenameResult
     * @return $this
     * @throws \Exception
     */
    public function setFilenameResult($filenameResult = null);


    /**
     * @param $widthNew
     * @return $this
     * @throws \Exception
     */
    public function setWidthNew($widthNew);

    /**
     * @param $heightNew
     * @return $this
     * @throws \Exception
     */
    public function setHeightNew($heightNew);

    /**
     * @param $quality
     * @throws \Exception
     */
    public function setQualityJpeg($quality);


    /**
     * @param bool $outputToBrowser
     * @return bool
     * @throws \Exception
     */
    public function create($outputToBrowser = false);

}