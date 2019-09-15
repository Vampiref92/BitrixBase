<?php

namespace Vf92\BitrixUtils\Exceptions\File;

use Exception;

/**
 * Class FileNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки файла',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
