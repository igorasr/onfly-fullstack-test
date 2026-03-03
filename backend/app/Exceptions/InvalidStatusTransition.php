<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidStatusTransition extends HttpException
{
    public function __construct(string $currentStatus, string $newStatus)
    {
        parent::__construct(422, "Cannot transition from status '{$currentStatus}' to '{$newStatus}'.");
    }
}
