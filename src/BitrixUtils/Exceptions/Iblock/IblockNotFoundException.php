<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock;

/**
 * Class IblockNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class IblockNotFoundException extends IblockException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Инфоблок не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
