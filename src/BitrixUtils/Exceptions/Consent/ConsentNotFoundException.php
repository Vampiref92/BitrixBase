<?php

namespace Vf92\BitrixUtils\Exceptions\Consent;
use Exception;

/**
 * Class ConsentNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Consent
 */
class ConsentNotFoundException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'пользовательское соглашеине не найдено',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
