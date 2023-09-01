<?php

namespace Phyple\Env\Exceptions;

use RuntimeException;

class EnvironmentFileNotFoundException extends RuntimeException
{
    /**
     * Exception message
     *
     * @var string $message
     */
    protected $message = 'Environment File Not Found !';
}