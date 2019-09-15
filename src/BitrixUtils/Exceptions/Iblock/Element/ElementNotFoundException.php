<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Element;

/**
 * Class ElementNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Element
 */
class ElementNotFoundException extends ElementException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Элемент инфоблока не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
