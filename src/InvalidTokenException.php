<?php
    
namespace Slacker;

class InvalidTokenException extends \Exception
{
    private $token;
    
    public function __construct($message = '', $token = '', $code = 0, Throwable $previous = NULL) {
        $this->token = $token;
        parent::__construct($message, $code, $previous);
    }
    
    public function getToken() {
        return $this->token;
    }
}

?>