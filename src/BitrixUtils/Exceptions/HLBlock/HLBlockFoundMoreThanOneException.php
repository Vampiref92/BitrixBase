<?php

namespace Vf92\BitrixUtils\Exceptions\HLBlock;

/**
 * Class HLBlockFoundMoreThanOneException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class HLBlockFoundMoreThanOneException extends HLBlockException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'найдено больше 1 highload',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
