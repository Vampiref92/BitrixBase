<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Element;

/**
 * Class ElementOffersCopyException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Element
 */
class ElementOffersCopyException extends ElementException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка копирвоания торгового предложения инфоблока',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
