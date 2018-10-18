<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ResizeImageInterface;

/**
 * Class ResizeImageDecorator
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class ResizeImageDecorator extends Image implements ResizeImageInterface
{
    /**
     * @var string
     */
    protected $resizeWidth = '-';
    
    /**
     * @var string
     */
    protected $resizeHeight = '-';
    
    /**
     * @inheritDoc
     */
    public function setResizeWidth($resizeWidth)
    {
        $this->resizeWidth = $resizeWidth > 0 ? $resizeWidth : '-';
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setResizeHeight($resizeHeight)
    {
        $this->resizeHeight = $resizeHeight > 0 ? $resizeHeight : '-';
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getResizeWidth()
    {
        return (int)$this->resizeWidth;
    }
    
    /**
     * @inheritDoc
     */
    public function getResizeHeight()
    {
        return (int)$this->resizeHeight;
    }
    
    /**
     * @return string
     */
    public function getSrc()
    {
        return sprintf('/resize/%sx%s%s',
                       $this->getResizeWidth() ?: '-',
                       $this->getResizeHeight() ?: '-',
                       parent::getSrc());
    }
}
