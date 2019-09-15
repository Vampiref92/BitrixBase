<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Property;

use Exception;

/**
 * Class PropertyException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Property
 */
class PropertyException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки свойства',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
