<?php

namespace Vf92\BitrixUtils\Interfaces\Orm\Model\File;

/**
 * Interface ActiveReadModelInterface
 * @package Vf92\BitrixUtils\Interfaces\Orm\Model\File
 */
interface ActiveReadModelInterface
{
    /**
     * ActiveReadModelInterface constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = []);

    /**
     * @param string|int $primary
     *
     * @return static
     */
    public static function createFromPrimary($primary): ActiveReadModelInterface;
}
