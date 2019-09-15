<?php

namespace Vf92\BitrixUtils\Exceptions\HLBlock;

/**
 * Class HLBlockNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock
 */
class HLBlockNotFoundException extends HLBlockException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Highload block не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
