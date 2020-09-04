<?php

namespace Svv\Framework\Exceptions;

class NotFoundException extends \Exception
{

    protected $message = "Page not found";
    protected $code = 404;

}
