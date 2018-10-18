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
    public function setResizeWidth($resizeWidth);
    
    /**
     * @param int $resizeHeight
     */
    public function setResizeHeight($resizeHeight);
    
    /**
     * @return int
     */
    public function getResizeWidth();
    
    /**
     * @return int
     */
    public function getResizeHeight();
}
