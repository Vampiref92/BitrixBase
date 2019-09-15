<?php

namespace Vf92\BitrixUtils\Exceptions\File;

/**
 * Class FileNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileNotFoundException extends FileException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Файл не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
