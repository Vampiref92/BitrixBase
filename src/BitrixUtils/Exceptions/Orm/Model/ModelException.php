<?php

namespace Vf92\BitrixUtils\Exceptions\Orm\Model;

use Exception;

/**
 * Class ModelException
 * @package Vf92\BitrixUtils\Exceptions\Orm\Model
 */
class ModelException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Ошибка обработки модели',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
