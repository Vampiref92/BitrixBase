<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock;

use Exception;

/**
 * Class IblockException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class IblockException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки инфоблока',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
