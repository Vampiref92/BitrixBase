<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

/**
 * Interface CropableImageInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model\Interfaces
 */
interface CropImageInterface extends FileInterface
{
    /**
     * @param int $cropWidth
     */
    public function setCropWidth($cropWidth);
    
    /**
     * @param int $cropHeight
     */
    public function setCropHeight($cropHeight);
    
    /**
     * @return int
     */
    public function getCropWidth();
    
    /**
     * @return int
     */
    public function getCropHeight();
}
