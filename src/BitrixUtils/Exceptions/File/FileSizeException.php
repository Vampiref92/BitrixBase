<?php

namespace Vf92\BitrixUtils\Exceptions\File;

/**
 * Class FileSizeException
 *
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileSizeException extends FileException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Неверный размер файла',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
