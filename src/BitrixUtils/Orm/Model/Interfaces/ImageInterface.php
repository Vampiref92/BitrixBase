<?php

namespace Vf92\BitrixUtils\Orm\Model\Interfaces;

use Vf92\BitrixUtils\Orm\Model\Image;

/**
 * Interface ImageInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
interface ImageInterface extends FileInterface
{

    /**
     * @return int
     */
    public function getHeight();

    /**
     * @return int
     */
    public function getWidth();

    /**
     * @param     $size
     * @param int $resizeType
     *
     * @return Image
     */
    public function getResizeImage($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL);
}
