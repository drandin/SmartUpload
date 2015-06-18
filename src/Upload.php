<?php namespace SmartUpload;

use SmartUpload\Exception\ExceptionUploadInitialization;
use SmartUpload\Exception\ExceptionRoutingFiles;
use SmartUpload\Exception\ExceptionResizeImage;

/**
 * Class Upload
 * @package SmartUpload\
 */
class Upload
{
    /**
     * @var HandlerFileUpload
     */
    private $handlerFileUpload;

    /**
     * @var RoutingFiles
     */
    private $routingFiles;

    /**
     * @var ResizeImage
     */
    private $resizeImage;

    /**
     * @param HandlerFileUpload $handlerFileUpload
     * @param RoutingFiles $routingFiles
     * @param IResizeImage $resizeImage
     */
    public function __construct(HandlerFileUpload $handlerFileUpload, RoutingFiles $routingFiles, IResizeImage $resizeImage)
    {
        $this->handlerFileUpload = $handlerFileUpload;
        $this->routingFiles = $routingFiles;
        $this->resizeImage = $resizeImage;
    }


    /**
     * @param $essence
     * @param $codeSubject
     * @return int
     */
    public function upload($essence, $codeSubject)
    {
        $res = 0;

        try {

            $routingFiles = $this->routingFiles;
            $resizeImage = $this->resizeImage;

            // Creating hierarchy directories for files
            $routingFiles
                ->createPath()
                ->writeHTAccess($routingFiles->getPath(), 'deny');

            // Creating  directory 'thumbnail' for files
            $routingFiles
                ->createDirThumbnail()
                ->writeHTAccess($routingFiles->getPathThumbnail(), 'allow');

            // The path to storage files
            $this->handlerFileUpload->setPath($routingFiles->getPath());

            /**
             * Callback function.
             */
            $callback = function() use($essence, $codeSubject, $routingFiles, $resizeImage) {

                /**
                 * @var HandlerFileUpload $this
                 */

                // Create Thumbnail
                $isThumbnail = $resizeImage
                    ->setFilenameOriginal($routingFiles->getPath().'/'.$this->{'filename'})
                    ->setFilenameResult($routingFiles->getPathThumbnail().'/'.$this->{'filename'})
                    ->setWidthNew(128)
                    ->setHeightNew(128)
                    ->create();

                $file = new File();

                $file
                    ->setCodeSubject($codeSubject)
                    ->setEssence($essence)
                    ->setIdDetails($routingFiles->getIdDetails())
                    ->setDirStorage($routingFiles->getDirStorage())
                    ->setVendor($routingFiles->getVendor())
                    ->setPrefixUser($routingFiles->getPrefixUser())
                    ->setIdUser($routingFiles->getIdUser())
                    ->setYear($routingFiles->getYear())
                    ->setMonth($routingFiles->getMonth())
                    ->setFilename($this->{'filename'})
                    ->setDirThumbnail($routingFiles->getDirThumbnail())
                    ->setThumbnail($isThumbnail)
                    ->setSize($this->{'getFileSize'}())
                    ->setMimeType($this->{'mimeType'})
                    ->setExtension($this->{'extension'})
                    ->setFilenameOriginal($this->{'getOriginalFilename'}());

                return $routingFiles->addFileInStorage($file);
            };

            $res = $this->handlerFileUpload->run($callback);
        }
        catch (ExceptionUploadInitialization $e) {
            echo $e->getMessage();
        }
        catch (ExceptionRoutingFiles $e) {
            echo $e->getMessage();
        }
        catch (ExceptionResizeImage $e) {
            echo $e->getMessage();
        }

        return $res;
    }
}