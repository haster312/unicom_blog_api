<?php

namespace App\Exceptions;

use Exception;

class JsonResponse extends Exception
{
    public $data;
    public $code;

    public function __construct($data, $code)
    {
        $this->data = $data;
        $this->code = $code;
        parent::__construct();
    }
}
