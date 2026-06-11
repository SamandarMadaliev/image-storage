<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UploadLimitExceededException extends HttpException
{
    public function __construct(int $limit)
    {
        parent::__construct(
            Response::HTTP_TOO_MANY_REQUESTS,
            "Daily upload limit of {$limit} images exceeded.",
        );
    }
}
