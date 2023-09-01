<?php

namespace Phyple\Env\Exceptions;

use RuntimeException;

class ImmutableKeyException extends RuntimeException
{
    /**
     * Exception message
     *
     * @var string $message
     */
    protected $message = 'Cannot modify an immutable key.';
}