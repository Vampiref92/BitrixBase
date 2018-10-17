<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

/**
 * Interface ResizeableImageInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model\Interfaces
 */
interface ResizeImageInterface extends FileInterface
{
    /**
     * @param int $resizeWidth
     */
    public function setResizeWidth(int $resizeWidth);
    
    /**
     * @param int $resizeHeight
     */
    public function setResizeHeight(int $resizeHeight);
    
    /**
     * @return int
     */
    public function getResizeWidth() : int;
    
    /**
     * @return int
     */
    public function getResizeHeight() : int;
}
