<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Config\Version;

/**
 * Class IblockPropEntityConstructor
 * @package Vf92\BitrixUtils\Constructor
 */
class IblockPropEntityConstructor extends EntityConstructor
{
    const SINGLE_TYPE = 's';
    const MULTIPLE_TYPE = 'm';

    /**
     * @param int $iblockId
     *
     * @return DataManager|string
     * @throws SystemException
     */
    public static function getDataClass($iblockId)
    {
        return static::getBaseDataClass($iblockId, static::SINGLE_TYPE);
    }

    public static function geV1DataClass()
    {
        $className = 'ElementPropV1';
        $tableName = 'b_iblock_element_property';
        $additionalFields = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Reference(
                'ELEMENT',
                ElementTable::getEntity(),
                Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter =Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID');
            }
            $additionalFields[] = new ReferenceField(
                'ELEMENT',
                ElementTable::getEntity(),
                $referenceFilter
            );
        }
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Reference(
                'PROPERTY',
                PropertyTable::getEntity(),
                Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter =Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID');
            }
            $additionalFields[] = new ReferenceField(
                'PROPERTY',
                PropertyTable::getEntity(),
                $referenceFilter
            );
        }

        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }

    /**
     * @param $iblockId
     *
     * @return DataManager|string
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getMultipleDataClass($iblockId)
    {
        $additionalFields = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Reference(
                'PROPERTY',
                PropertyTable::getEntity(),
                Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter =Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID');
            }
            $additionalFields[] = new ReferenceField(
                'PROPERTY',
                PropertyTable::getEntity(),
                $referenceFilter
            );
        }
        return static::getBaseDataClass($iblockId, static::MULTIPLE_TYPE, $additionalFields);
    }

    /**
     * @param        $iblockId
     * @param string $type
     * @param array  $additionalFields
     *
     * @return DataManager|string
     * @throws SystemException
     * @throws ArgumentException
     */
    protected static function getBaseDataClass($iblockId, $type = 's', array $additionalFields = [])
    {
        $className = 'ElementProp' . ToUpper($type) . $iblockId;
        $tableName = 'b_iblock_element_prop_' . ToLower($type) . $iblockId;
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Reference(
                'ELEMENT',
                ElementTable::getEntity(),
                Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter =Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID');
            }
            $additionalFields[] = new ReferenceField(
                'ELEMENT',
                ElementTable::getEntity(),
                $referenceFilter
            );
        }

        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}