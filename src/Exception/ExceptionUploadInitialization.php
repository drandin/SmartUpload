<?php  namespace SmartUpload\Exception;

/**
 * Class ExceptionUploadInitialization
 * @package SmartUpload\
 */
class ExceptionUploadInitialization extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}