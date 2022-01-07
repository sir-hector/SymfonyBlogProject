<?php

namespace App\Exception;

use Throwable;

class InvalidConfiramtionTokenException extends  \Exception
{
    public function __construct(
        string $message ='',
        int $code = 0,
        Throwable $previous = null
){
        parent::__construct('Confiramtion token is invalid', $code, $previous);
}

}