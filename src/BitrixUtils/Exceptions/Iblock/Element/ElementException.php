<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Element;

use Exception;

/**
 * Class ElementNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Element
 */
class ElementException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки элемента инфоблока',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
