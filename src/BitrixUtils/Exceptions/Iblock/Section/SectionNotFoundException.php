<?php

namespace Vf92\BitrixUtils\Exceptions\Iblock\Section;

use Exception;

/**
 * Class SectionNotFoundException
 * @package Vf92\BitrixUtils\Exceptions\Iblock\Section
 */
class SectionNotFoundException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'Раздел инфоблока не найден',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
