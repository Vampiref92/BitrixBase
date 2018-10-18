<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

/**
 * Interface ImageInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
interface ImageInterface extends FileInterface
{
    /**
     * @param int $height
     *
     * @return static
     */
    public function setHeight($height);

    /**
     * @return int
     */
    public function getHeight();

    /**
     * @param int $width
     *
     * @return static
     */
    public function setWidth($width);

    /**
     * @return int
     */
    public function getWidth();
}
