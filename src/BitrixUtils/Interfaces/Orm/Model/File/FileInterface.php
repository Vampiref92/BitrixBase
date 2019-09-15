<?php

namespace Vf92\BitrixUtils\Interfaces\Orm\Model\File;

/**
 * Interface FileInterface
 *
 * @package Vf92\BitrixUtils\Interfaces\Orm\Model\File
 */
interface FileInterface extends ActiveReadModelInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return string
     */
    public function getSrc(): string;

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function getFields(): array;

    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string
     */
    public function getSubDir(): string;
}
