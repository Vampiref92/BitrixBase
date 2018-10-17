<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Adv\Bitrixtools\Tools\BitrixUtils;
use CIBlockElement;
use DateTimeImmutable;
use Vf92\BitrixUtils\OldOrm\Model\Traits\IblockModelTrait;
use Vf92\BitrixUtils\OldOrm\Type\TextContent;

/**
 * Class IblockSect
 * @package Vf92\BitrixUtils\OldOrm\Model
 *
 */
class IblockSect extends BitrixArrayItemBase
{

    /**
     * @var int
     * @JMS\Serializer\Annotation\Type("int")
     * @see BitrixArrayItemBase
     */
    protected $IBLOCK_ID = 0;
    
    /**
     * @var string
     * @JMS\Serializer\Annotation\Type("string")
     * @see BitrixArrayItemBase
     */
    protected $CODE = '';
    
    /**
     * @var int
     */
    protected $IBLOCK_SECTION_ID = 0;
    
    /**
     * @var int
     */
    protected $PICTURE = 0;
    
    /**
     * @var int
     */
    protected $DETAIL_PICTURE = 0;
    
    /**
     * @var int
     */
    protected $DEPTH_LEVEL = 0;
    
    /**
     * @var string
     */
    protected $DESCRIPTION = '';

    /**
     * @var string
     */
    protected $DESCRIPTION_TYPE = '';

    /**
     * @var TextContent
     */
    protected $description;

    /**
     * @var string
     */
    protected $SECTION_PAGE_URL = '';

    /**
     * @return string
     */
    public function getSectionPageUrl(): string
    {
        return $this->SECTION_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function withSectionPageUrl(string $url): self
    {
        $this->SECTION_PAGE_URL = $url;

        return $this;
    }

    /**
     * @return TextContent
     */
    public function geDescription(): TextContent
    {
        if (null === $this->description) {
            $this->description = (new TextContent())
                ->withText($this->DESCRIPTION)
                ->withType($this->DESCRIPTION_TYPE);
        }

        return $this->description;
    }

    /**
     * @param TextContent $description
     *
     * @return $this
     */
    public function withDescription(TextContent $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    /**
     * @return int
     */
    public function getIblockId() : int
    {
        return $this->IBLOCK_ID;
    }
    
    /**
     * @param int $iblockId
     *
     * @return $this
     */
    public function withIblockId(int $iblockId): self
    {
        $this->IBLOCK_ID = $iblockId;
    
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->CODE ?? '';
    }
    
    /**
     * @param string $code
     *
     * @return $this
     */
    public function withCode(string $code): self
    {
        $this->CODE = $code;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getIblockSectionId() : int
    {
        return $this->IBLOCK_SECTION_ID;
    }
    
    /**
     * @param int $iblockSectionId
     *
     * @return $this
     */
    public function withIblockSectionId(int $iblockSectionId): self
    {
        $this->IBLOCK_SECTION_ID = $iblockSectionId;
        return $this;
        
    }
    
    /**
     * @return int
     */
    public function getPicture() : int
    {
        return $this->PICTURE;
    }
    
    /**
     * @param int $picture
     *
     * @return $this
     */
    public function withPicture(int $picture): self
    {
        $this->PICTURE = $picture;
        return $this;
        
    }
    
    /**
     * @return int
     */
    public function getDetailPicture() : int
    {
        return $this->DETAIL_PICTURE;
    }
    
    /**
     * @param int $detailPicture
     *
     * @return $this
     */
    public function withDetailPicture(int $detailPicture): self
    {
        $this->DETAIL_PICTURE = $detailPicture;
    
        return $this;
    }
    
    /**
     * @return int
     */
    public function getDepthLevel() : int
    {
        return $this->DEPTH_LEVEL;
    }
    
    /**
     * @param int $depthLevel
     *
     * @return $this
     */
    public function withDepthLevel(int $depthLevel): self
    {
        $this->DEPTH_LEVEL = $depthLevel;
    
        return $this;
    }
}
