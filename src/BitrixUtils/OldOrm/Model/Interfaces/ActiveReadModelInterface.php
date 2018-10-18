<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

interface ActiveReadModelInterface
{
    /**
     * ActiveReadModelInterface constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = []);

    /**
     * @param string $primary
     *
     * @return static
     */
    public static function createFromPrimary($primary);
}
