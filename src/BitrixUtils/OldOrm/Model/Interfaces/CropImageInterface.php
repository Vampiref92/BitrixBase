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
    public function setCropWidth(int $cropWidth);
    
    /**
     * @param int $cropHeight
     */
    public function setCropHeight(int $cropHeight);
    
    /**
     * @return int
     */
    public function getCropWidth() : int;
    
    /**
     * @return int
     */
    public function getCropHeight() : int;
}
