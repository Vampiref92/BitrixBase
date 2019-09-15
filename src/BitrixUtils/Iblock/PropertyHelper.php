<?php

namespace NotaTools\Helpers;

use Bitrix\Iblock\EO_Property;
use Bitrix\Iblock\EO_PropertyEnumeration;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\SectionPropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Exceptions\Iblock\Property\PropertyEnumNotFoundException;
use Vf92\BitrixUtils\Exceptions\Iblock\Property\PropertyNotFoundException;

/**
 * Class PropertyHelper
 *
 * @package Vf92\BitrixUtils\Helpers
 */
class PropertyHelper
{
    /** @var array|EO_Property[] */
    private static $props;
    /** @var array|EO_SectionProperty_Collection */
    private static $facetProps;
    /** @var array|EO_PropertyEnumeration[] */
    private static $enums = [];

    /**
     * @param string $code
     *
     * @return EO_Property
     * @throws PropertyNotFoundException
     */
    public static function getPropByCode(string $code): EO_Property
    {
        if (self::$props === null) {
            self::loadProps();
        }
        if (!array_key_exists($code, self::$props)) {
            throw new PropertyNotFoundException('свойство не найдено');
        }
        return self::$props[$code];
    }

    /**
     * @param string $code
     *
     * @return int
     * @throws PropertyNotFoundException
     */
    public static function getPropIdByCode(string $code): int
    {

        return self::getPropByCode($code)->getId();
    }

    /**
     * @param int $id
     *
     * @return string
     * @throws PropertyNotFoundException
     */
    public static function getPropCodeById(int $id): string
    {

        return self::getPropById($id)->getCode();
    }

    /**
     * @param $propCode
     * @param $xmlId
     *
     * @return int
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumIdByXmlId(string $propCode, $xmlId): int
    {
        return self::getEnumByXmlId($propCode, $xmlId)->getId();
    }

    /**
     * @param $propCode
     * @param $xmlId
     *
     * @return EO_PropertyEnumeration
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumByXmlId(string $propCode, $xmlId): EO_PropertyEnumeration
    {
        if (!isset(self::$enums[$propCode])) {
            self::loadEnumVals($propCode);
        }
        if (!array_key_exists($propCode, self::$enums)) {
            throw new PropertyNotFoundException('свойство не найдено');
        }
        if (!array_key_exists($xmlId, self::$enums[$propCode])) {
            throw new PropertyEnumNotFoundException('Значение не найдено');
        }
        return self::$enums[$propCode][$xmlId];
    }

    /**
     * @param     $propCode
     * @param int $id
     *
     * @return EO_PropertyEnumeration
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumById(string $propCode, int $id): EO_PropertyEnumeration
    {
        if (!isset(self::$enums[$propCode])) {
            self::loadEnumVals($propCode);
        }
        if (!array_key_exists($propCode, self::$enums)) {
            throw new PropertyNotFoundException('свойство не найдено');
        }
        /** @var EO_PropertyEnumeration $loadEnumVal */
        foreach (self::$enums[$propCode] as $loadEnumVal) {
            if ($loadEnumVal->getId() === $id) {
                return $loadEnumVal;
            }
        }
        throw new PropertyEnumNotFoundException('Значение не найдено');
    }

    /**
     * @param string $propCode
     * @param        $val
     *
     * @return EO_PropertyEnumeration
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getEnumByIdOrXmlId(string $propCode, $val): EO_PropertyEnumeration
    {
        if (is_numeric($val)) {
            $val = (int)$val;
            return static::getEnumById($propCode, $val);
        } elseif (is_string($val) && !empty($val)) {
            return static::getEnumByXmlId($propCode, $val);
        }
        throw new PropertyEnumNotFoundException('Значение не найдено');
    }

    /**
     * @param string $propCode
     *
     * @return array|EO_PropertyEnumeration[]
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    public static function getPropsEnum(string $propCode): array
    {
        if (!isset(self::$enums[$propCode])) {
            self::loadEnumVals($propCode);
        }
        return self::$enums[$propCode] ?: [];
    }

    /**
     * @param int $id
     *
     * @return EO_Property
     * @throws PropertyNotFoundException
     */
    public static function getPropById(int $id): EO_Property
    {
        if (self::$props === null) {
            self::loadProps();
        }
        /** @var EO_Property $prop */
        foreach (self::$props as $prop) {
            if ($prop->getId() === $id) {
                return $prop;
            }
        }
        throw new PropertyNotFoundException('свойство не найдено');
    }

    /**
     * @param int $iblockId
     *
     * @return array|EO_Property[]
     * @throws PropertyNotFoundException
     */
    public static function getPropertiesByIblock(int $iblockId): array
    {
        if (self::$props === null) {
            self::loadProps();
        }
        $res = [];
        /** @var EO_Property $prop */
        foreach (self::$props as $prop) {
            if ($prop->getIblockId() === $iblockId) {
                $res[] = $prop;
            }
        }
        return $res;
    }

    /**
     * @param $propCode
     *
     * @throws PropertyNotFoundException
     * @throws PropertyEnumNotFoundException
     */
    protected static function loadEnumVals(string $propCode): void
    {
        try {
            $res = PropertyEnumerationTable::query()->setSelect(['*'])->where('PROPERTY_ID',
                self::getPropIdByCode($propCode))->exec();
            /** @var EO_PropertyEnumeration $item */
            while ($item = $res->fetchObject()) {
                self::$enums[$propCode][$item->getXmlId()] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new PropertyEnumNotFoundException('Знаачения не найдены для свойства - ' . $propCode . ' - ' . $e->getMessage());
        }
    }

    /**
     *
     * @throws PropertyNotFoundException
     */
    protected static function loadProps(): void
    {
        self::$props = [];
        try {
            $res = PropertyTable::query()->setSelect(['ID', 'CODE'])->exec();
            /** @var EO_Property $item */
            while ($item = $res->fetchObject()) {
                self::$props[$item->getCode()] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new PropertyNotFoundException('свойство не найдено');
        }
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @return ?EO_SectionProperty_Collection
     * @throws PropertyNotFoundException
     */
    public static function getFacetProps(int $iblockId, int $sectionId = 0): ?EO_SectionProperty_Collection
    {
        if (self::$facetProps === null) {
            self::loadFacetProps($iblockId, $sectionId);
        }
        return self::$facetProps[$iblockId][$sectionId];
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @return array
     * @throws PropertyNotFoundException
     */
    public static function getFacetPropsIds(int $iblockId, int $sectionId = 0): array
    {
        $props = static::getFacetProps($iblockId, $sectionId);
        return $props->getPropertyIdList() ?? [];
    }

    /**
     * @param int $iblockId
     * @param int $sectionId
     *
     * @throws PropertyNotFoundException
     */
    protected static function loadFacetProps(int $iblockId, int $sectionId = 0): void
    {
        self::$facetProps = [];
        try {
            $res = SectionPropertyTable::query()->setSelect(['*'])
                ->where('SMART_FILTER', 'Y')
                ->where('IBLOCK_ID', $iblockId)
                ->where('SECTION_ID', $sectionId)
                ->exec();
            self::$facetProps[$iblockId][$sectionId] = $res->fetchCollection();
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new PropertyNotFoundException('свойство не найдено');
        }
    }
}