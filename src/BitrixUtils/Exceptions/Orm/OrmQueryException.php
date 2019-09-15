<?php

namespace Vf92\BitrixUtils\Exceptions\Orm;

use Exception;

class OrmQueryException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Базовая ошибка ОРМ Битиркса',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}