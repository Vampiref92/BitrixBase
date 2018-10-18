<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Traits\IblockModelTrait;

/**
 * Class IblockSection
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
abstract class IblockSection extends BitrixArrayItemBase
{
    const ROOT_SECTION_NAME = 'root';
    const ROOT_SECTION_CODE = 'root';

    use IblockModelTrait;

    /**
     * @var int
     * @JMS\Serializer\Annotation\Type("int")
     * @see BitrixArrayItemBase
     */
    protected $IBLOCK_ID = 0;

    /**
     * @var int
     * @JMS\Serializer\Annotation\Type("int")
     * @see BitrixArrayItemBase
     */
    protected $ID = 0;

    /**
     * @var int
     * @JMS\Serializer\Annotation\Type("int")
     * @see BitrixArrayItemBase
     */
    protected $SORT = 500;

    /**
     * @var int
     */
    protected $DEPTH_LEVEL = 0;

    /**
     * @var int
     */
    protected $LEFT_MARGIN = 0;

    /**
     * @var int
     */
    protected $RIGHT_MARGIN = 0;

    /**
     * @var string
     */
    protected $SECTION_PAGE_URL = '';

    /**
     * @var int
     */
    protected $IBLOCK_SECTION_ID = 0;

    /**
     * @var int
     */
    protected $ELEMENT_CNT = 0;

    protected function getElementCount()
    {
        return $this->ELEMENT_CNT;
    }

    /**
     * @param int $elementCount
     *
     * @return static
     */
    protected function setElementCount($elementCount)
    {
        $this->ELEMENT_CNT = $elementCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getIblockSectionId()
    {
        return (int)$this->IBLOCK_SECTION_ID;
    }

    /**
     * @param int $IBLOCK_SECTION_ID
     *
     * @return static
     */
    public function setIblockSectionId($IBLOCK_SECTION_ID)
    {
        $this->IBLOCK_SECTION_ID = $IBLOCK_SECTION_ID;
        return $this;
    }


    /**
     * @return int
     */
    public function getDepthLevel()
    {
        return (int)$this->DEPTH_LEVEL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function withSectionPageUrl($url)
    {
        $this->SECTION_PAGE_URL = $url;

        return $this;
    }

    /**
     * @param int $level
     *
     * @return $this
     */
    public function withDepthLevel($level)
    {
        $this->DEPTH_LEVEL = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeftMargin()
    {
        return $this->LEFT_MARGIN;
    }

    /**
     * @param int $leftMargin
     *
     * @return $this
     */
    public function withLeftMargin($leftMargin)
    {
        $this->LEFT_MARGIN = $leftMargin;

        return $this;
    }

    /**
     * @return int
     */
    public function getRightMargin()
    {
        return $this->RIGHT_MARGIN;
    }

    /**
     * @param int $rightMargin
     *
     * @return $this
     */
    public function withRightMargin($rightMargin)
    {
        $this->RIGHT_MARGIN = $rightMargin;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPageUrl()
    {
        return $this->SECTION_PAGE_URL ?: '';
    }
}
