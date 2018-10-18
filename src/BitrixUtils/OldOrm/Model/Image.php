<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ImageInterface;

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
    protected $width;
    
    /**
     * @var int
     */
    protected $height;
    
    /**
     * Image constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->setWidth($fields['WIDTH']);
        $this->setHeight($fields['HEIGHT']);
        
        parent::__construct($fields);
    }
    
    /**
     * @return int
     */
    public function getWidth() : int
    {
        return (int)$this->width;
    }
    
    /**
     * @param int $width
     *
     * @return static
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getHeight() : int
    {
        return (int)$this->height;
    }
    
    /**
     * @param int $height
     *
     * @return static
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;
        
        return $this;
    }
}