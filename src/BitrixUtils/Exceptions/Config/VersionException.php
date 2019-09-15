<?php

namespace Vf92\BitrixUtils\Exceptions\Config;

use Exception;
use Throwable;

/**
 * Class VersionException
 * @package Vf92\BitrixUtils\Exceptions\Config
 */
class VersionException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'минимальная версия Битрикс 18.0.4, обновите Битиркс или используйте 2 версию пакета',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
