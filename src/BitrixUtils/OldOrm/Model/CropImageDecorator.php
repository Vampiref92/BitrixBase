<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Interfaces\CropImageInterface;

/**
 * Class CropImageDecorator
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class CropImageDecorator extends Image implements CropImageInterface
{
    /**
     * @var string
     */
    protected $cropWidth = '-';
    
    /**
     * @var string
     */
    protected $cropHeight = '-';
    
    /**
     * @inheritDoc
     */
    public function setCropWidth($cropWidth)
    {
        $this->cropWidth = $cropWidth > 0 ? $cropWidth : '-';
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setCropHeight($cropHeight)
    {
        $this->cropHeight = $cropHeight > 0 ? $cropHeight : '-';
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getCropWidth()
    {
        return (int)$this->cropWidth;
    }
    
    /**
     * @inheritDoc
     */
    public function getCropHeight()
    {
        return (int)$this->cropHeight;
    }
    
    /**
     * @return string
     */
    public function getSrc()
    {
        return sprintf('/crop/%sx%s%s', $this->getCropWidth() ?: '-', $this->getCropHeight() ?: '-', parent::getSrc());
    }
}
