<?php  namespace SmartUpload\Exception;


/**
 * Class ExceptionRoutingFiles
 * @package SmartUpload\
 */
class ExceptionRoutingFiles extends \RuntimeException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}