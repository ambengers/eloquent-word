<?php

namespace Ambengers\EloquentWord\Exceptions;

trait WithMessage
{
    /**
     * Set the exception message.
     *
     * @param  string  $message
     * @return static
     */
    public static function withMessage($message = '')
    {
        return new static($message);
    }
}
