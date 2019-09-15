<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Property;

/**
 * Class PropertyEnumNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Property
 */
class PropertyEnumNotFoundException extends PropertyException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Элемент списка свойства не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
