<?php

namespace Vf92\BitrixUtils\Exceptions\User;

/**
 * Class GroupNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\User
 */
class GroupNotFoundException extends UserException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Группа пользователя не найдена',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
