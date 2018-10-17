<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class CatalogProduct
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class CatalogProduct
{
    /**
     * @Serializer\SerializedName("ID")
     * @Serializer\Type("int")
     * @Serializer\Groups({"create","read"})
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Вес единицы товара
     *
     * @Serializer\SerializedName("WEIGHT")
     * @Serializer\Type("double")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var double
     */
    protected $weight = 0;

    /**
     * Высота товара (в мм).
     *
     * @Serializer\SerializedName("HEIGHT")
     * @Serializer\Type("double")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var double
     */
    protected $height = 0;

    /**
     * Ширина товара (в мм).
     *
     * @Serializer\SerializedName("WIDTH")
     * @Serializer\Type("double")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var double
     */
    protected $width = 0;

    /**
     * Длина товара (в мм).
     *
     * @Serializer\SerializedName("LENGTH")
     * @Serializer\Type("double")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var double
     */
    protected $length = 0;

    /**
     * Код инфоблока товара
     *
     * @Serializer\SerializedName("ELEMENT_IBLOCK_ID")
     * @Serializer\Type("int")
     * @Serializer\Groups({"read"})
     *
     * @var int
     */
    protected $productIblockId = 0;

    /**
     * Внешний код товара
     *
     * @Serializer\SerializedName("ELEMENT_XML_ID")
     * @Serializer\Type("int")
     * @Serializer\Groups({"read"})
     *
     * @var string
     */
    protected $productXmlId = '';

    /**
     * Название товара
     *
     * @Serializer\SerializedName("ELEMENT_NAME")
     * @Serializer\Type("int")
     * @Serializer\Groups({"read"})
     *
     * @var string
     */
    protected $productName = '';

    /**
     * ID единицы измерения
     *
     * @Serializer\SerializedName("MEASURE")
     * @Serializer\Type("int")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var int
     */
    protected $measureId = 5;

    /**
     * ID ставки НДС
     *
     * @Serializer\SerializedName("VAT_ID")
     * @Serializer\Type("int")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var int
     */
    protected $vatId;

    /**
     * НДС включен в цену
     *
     * @Serializer\SerializedName("VAT_INCLUDED")
     * @Serializer\Type("bitrix_bool")
     * @Serializer\Groups({"create","read","update"})
     *
     * @var bool
     */
    protected $vatIncluded = true;

    /**
     * @var bool
     * @Serializer\SerializedName("AVAILABLE")
     * @Serializer\Type("bitrix_bool")
     * @Serializer\Groups({"create","read","update"})
     */
    protected $available = true;

    /**
     * @var bool
     * @Serializer\SerializedName("CAN_BUY_ZERO")
     * @Serializer\Type("bitrix_bool")
     * @Serializer\Groups({"create","read","update"})
     */
    protected $canBuyZero = true;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return CatalogProduct
     */
    public function setId(int $id): CatalogProduct
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return CatalogProduct
     */
    public function setWeight(float $weight): CatalogProduct
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return float
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @param float $height
     *
     * @return CatalogProduct
     */
    public function setHeight(float $height): CatalogProduct
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return float
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @param float $width
     *
     * @return CatalogProduct
     */
    public function setWidth(float $width): CatalogProduct
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return float
     */
    public function getLength(): ?float
    {
        return $this->length;
    }

    /**
     * @param float $length
     *
     * @return CatalogProduct
     */
    public function setLength(float $length): CatalogProduct
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @param int $measureId
     *
     * @return CatalogProduct
     */
    public function setMeasureId(int $measureId): CatalogProduct
    {
        $this->measureId = $measureId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProductIblockId(): int
    {
        return $this->productIblockId;
    }

    /**
     * @return string
     */
    public function getProductXmlId(): string
    {
        return $this->productXmlId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getMeasureId(): int
    {
        return $this->measureId;
    }

    /**
     * @return int
     */
    public function getVatId(): int
    {
        return $this->vatId;
    }

    /**
     * @param int $vatId
     * @return CatalogProduct
     */
    public function setVatId(int $vatId): CatalogProduct
    {
        $this->vatId = $vatId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     * @return CatalogProduct
     */
    public function setAvailable(bool $available): CatalogProduct
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return bool
     */
    public function canBuyZero(): bool
    {
        return $this->canBuyZero;
    }

    /**
     * @param bool $canBuyZero
     * @return CatalogProduct
     */
    public function setCanBuyZero(bool $canBuyZero): CatalogProduct
    {
        $this->canBuyZero = $canBuyZero;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVatIncluded(): bool
    {
        return $this->vatIncluded;
    }

    /**
     * @param bool $vatIncluded
     *
     * @return CatalogProduct
     */
    public function setVatIncluded(bool $vatIncluded): CatalogProduct
    {
        $this->vatIncluded = $vatIncluded;

        return $this;
    }
}
