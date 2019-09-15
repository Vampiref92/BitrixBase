<?php

namespace Vf92\BitrixUtils\Interfaces\Orm\Model\File;

use Vf92\BitrixUtils\Orm\Model\Image;

/**
 * Interface ImageInterface
 *
 * @package Vf92\BitrixUtils\Interfaces\Orm\Model\File
 */
interface ImageInterface extends FileInterface
{

    /**
     * @return int
     */
    public function getHeight(): int;

    /**
     * @return int
     */
    public function getWidth(): int;

    /**
     * @param     $size
     * @param int $resizeType
     *
     * @return Image
     */
    public function getResizeImage($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL): Image;
}
