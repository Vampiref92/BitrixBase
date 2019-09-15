<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock;

/**
 * Class IblockFieldSettingsException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class IblockFieldSettingsException extends IblockException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка получения полей инфоблока инфоблока',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
