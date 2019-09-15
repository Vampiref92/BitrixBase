<?php

namespace Vf92\BitrixUtils\Exceptions\File;

/**
 * Class FileSaveException
 *
 * @package Vf92\BitrixUtils\Exceptions\File
 */
class FileSaveException extends FileException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка сохранение файлы',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
