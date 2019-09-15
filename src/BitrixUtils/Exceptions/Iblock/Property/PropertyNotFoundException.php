<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Property;

/**
 * Class PropertyNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Property
 */
class PropertyNotFoundException extends PropertyException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Свойство не найдено',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
