<?php

namespace Vf92\BitrixUtils\Exceptions\File;

/**
 * Class FileTypeException
 *
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileTypeException extends FileException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Неверный тип файла',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
