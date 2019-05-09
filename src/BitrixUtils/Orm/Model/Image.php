<?php

namespace Vf92\BitrixUtils\Orm\Model;

use Cfile;
use Vf92\BitrixUtils\Orm\Model\Interfaces\ImageInterface;
use Vf92\Enum\MediaEnum;

/**
 * Class Image
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class Image extends File implements ImageInterface
{
    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /** @var null|Image */
    protected $original = null;

    /**
     * Image constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        if (!empty($fields['width'])){
            $fields['WIDTH']=$fields['width'];
        }
        if (!empty($fields['height'])){
            $fields['HEIGHT']=$fields['height'];
        }
        if(!empty($fields['WIDTH'])) {
            $this->setWidth($fields['WIDTH']);
        }
        if(!empty($fields['HEIGHT'])) {
            $this->setHeight($fields['HEIGHT']);
        }

        parent::__construct($fields);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return (int)$this->width;
    }

    /**
     * @param int $width
     *
     * @return static
     */
    protected function setWidth($width)
    {
        $this->width = (int)$width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return (int)$this->height;
    }

    /**
     * @param int $height
     *
     * @return static
     */
    protected function setHeight($height)
    {
        $this->height = (int)$height;

        return $this;
    }

    /**
     * @param     $size
     * @param int $resizeType
     *
     * @return Image
     */
    public function getResizeImage($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $resizedFile = Cfile::ResizeImageGet($this->getId(), $size, $resizeType, true);
        $fields = [
            'SRC'    => $resizedFile['src'],
            'WIDTH'  => $resizedFile['width'],
            'HEIGHT' => $resizedFile['height'],
            'SIZE'   => $resizedFile['size'],
        ];
        $resizeObj = new static($fields);
        $resizeObj->setOriginal($this);
        return $resizeObj;
    }

    /**
     * @return Image|null
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @param Image $original
     */
    public function setOriginal(Image $original)
    {
        $this->original = $original;
    }

    public static function getNoImage()
    {
        return new static([
            'src'    => MediaEnum::NO_IMAGE_WEB_PATH,
            'width'  => MediaEnum::NO_IMAGE_WIDTH,
            'height' => MediaEnum::NO_IMAGE_HEIGHT,
        ]);
    }
}
