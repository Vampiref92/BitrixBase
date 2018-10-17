<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

/**
 * Interface RotateImageInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model\Interfaces
 */
interface RotateImageInterface extends FileInterface
{
    /**
     * @param int $angle
     */
    public function setAngle(int $angle);
    
    /**
     * @return int
     */
    public function getAngle() : int;
}
