<?php

namespace Vf92\BitrixUtils\Exceptions\HLBlock;

use Exception;

/**
 * Class HLBlockException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class HLBlockException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка Highload block',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
