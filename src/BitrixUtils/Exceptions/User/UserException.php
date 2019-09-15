<?php

namespace Vf92\BitrixUtils\Exceptions\User;

use Exception;

/**
 * Class UserException
 * @package Vf92\BitrixUtils\Exceptions\User
 */
class UserException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки пользователя',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
