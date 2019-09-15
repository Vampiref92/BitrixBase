<?php

namespace Vf92\BitrixUtils\Exceptions\Form;

use Exception;

/**
 * Class FormNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Form
 */
class FormNotFoundException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Форма не найдена',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}