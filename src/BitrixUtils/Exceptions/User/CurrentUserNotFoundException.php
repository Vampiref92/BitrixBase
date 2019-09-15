<?php

namespace Vf92\BitrixUtils\Exceptions\User;

/**
 * Class CurrentUserNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\User
 */
class CurrentUserNotFoundException extends UserException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Не удалось получить текущего пользователя',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
