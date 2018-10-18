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
    public function getSectionPageUrl()
    {
        return $this->SECTION_PAGE_URL;
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
     * @return TextContent
     */
    public function geDescription()
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
    public function withDescription(TextContent $description)
    {
        $this->description = $description;

        return $this;
    }
    
    /**
     * @return int
     */
    public function getIblockId()
    {
        return $this->IBLOCK_ID;
    }
    
    /**
     * @param int $iblockId
     *
     * @return $this
     */
    public function withIblockId($iblockId)
    {
        $this->IBLOCK_ID = $iblockId;
    
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->CODE ?: '';
    }
    
    /**
     * @param string $code
     *
     * @return $this
     */
    public function withCode($code)
    {
        $this->CODE = $code;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getIblockSectionId()
    {
        return $this->IBLOCK_SECTION_ID;
    }
    
    /**
     * @param int $iblockSectionId
     *
     * @return $this
     */
    public function withIblockSectionId($iblockSectionId)
    {
        $this->IBLOCK_SECTION_ID = $iblockSectionId;
        return $this;
        
    }
    
    /**
     * @return int
     */
    public function getPicture()
    {
        return $this->PICTURE;
    }
    
    /**
     * @param int $picture
     *
     * @return $this
     */
    public function withPicture($picture)
    {
        $this->PICTURE = $picture;
        return $this;
        
    }
    
    /**
     * @return int
     */
    public function getDetailPicture()
    {
        return $this->DETAIL_PICTURE;
    }
    
    /**
     * @param int $detailPicture
     *
     * @return $this
     */
    public function withDetailPicture($detailPicture)
    {
        $this->DETAIL_PICTURE = $detailPicture;
    
        return $this;
    }
    
    /**
     * @return int
     */
    public function getDepthLevel()
    {
        return $this->DEPTH_LEVEL;
    }
    
    /**
     * @param int $depthLevel
     *
     * @return $this
     */
    public function withDepthLevel($depthLevel)
    {
        $this->DEPTH_LEVEL = $depthLevel;
    
        return $this;
    }
}
