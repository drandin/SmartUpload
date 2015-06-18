<?php  namespace SmartUpload\Exception;

/**
 * Class ExceptionResizeImage
 * @package SmartUpload\
 */
class ExceptionResizeImage extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}