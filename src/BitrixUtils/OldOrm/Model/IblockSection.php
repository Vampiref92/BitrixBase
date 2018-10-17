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
    protected const ROOT_SECTION_NAME = 'root';
    protected const ROOT_SECTION_CODE = 'root';

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

    protected function getElementCount(): int
    {
        return $this->ELEMENT_CNT;
    }

    /**
     * @param int $elementCount
     *
     * @return static
     */
    protected function setElementCount(int $elementCount)
    {
        $this->ELEMENT_CNT = $elementCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getIblockSectionId(): int
    {
        return (int)$this->IBLOCK_SECTION_ID;
    }

    /**
     * @param int $IBLOCK_SECTION_ID
     *
     * @return static
     */
    public function setIblockSectionId(int $IBLOCK_SECTION_ID)
    {
        $this->IBLOCK_SECTION_ID = $IBLOCK_SECTION_ID;
        return $this;
    }


    /**
     * @return int
     */
    public function getDepthLevel(): int
    {
        return (int)$this->DEPTH_LEVEL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function withSectionPageUrl(string $url)
    {
        $this->SECTION_PAGE_URL = $url;

        return $this;
    }

    /**
     * @param int $level
     *
     * @return $this
     */
    public function withDepthLevel(int $level)
    {
        $this->DEPTH_LEVEL = $level;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeftMargin(): int
    {
        return $this->LEFT_MARGIN;
    }

    /**
     * @param int $leftMargin
     *
     * @return $this
     */
    public function withLeftMargin(int $leftMargin)
    {
        $this->LEFT_MARGIN = $leftMargin;

        return $this;
    }

    /**
     * @return int
     */
    public function getRightMargin(): int
    {
        return $this->RIGHT_MARGIN;
    }

    /**
     * @param int $rightMargin
     *
     * @return $this
     */
    public function withRightMargin(int $rightMargin)
    {
        $this->RIGHT_MARGIN = $rightMargin;

        return $this;
    }

    /**
     * @return string
     */
    public function getSectionPageUrl(): string
    {
        return $this->SECTION_PAGE_URL ?? '';
    }
}
