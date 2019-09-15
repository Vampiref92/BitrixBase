<?php

namespace Vf92\BitrixUtils\Exceptions\File;

use Throwable;

/**
 * Class FileExtensionException
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileExtensionException extends FileException
{
    /** @inheritDoc */
    public function __construct($message = 'неверный тип файла', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
