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
    public function setAngle($angle);
    
    /**
     * @return int
     */
    public function getAngle();
}
