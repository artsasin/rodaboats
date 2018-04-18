<?php
/**
 * Created by PhpStorm.
 * User: artem-sasin
 * Date: 18.04.18
 * Time: 11:39
 */

namespace AppBundle\Model;


class ApiResponse
{
    public $status;

    public $valid;

    public $payload;

    public $message;

    public function __construct()
    {
        $this->status = 0;
        $this->valid = true;
        $this->payload = null;
        $this->message = null;
    }
}