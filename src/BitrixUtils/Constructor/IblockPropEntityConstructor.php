<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Main;
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
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    public static function getDataClass($iblockId)
    {
        return static::getBaseDataClass($iblockId, static::SINGLE_TYPE);
    }

    /**
     * @return Main\Entity\DataManager|string
     * @throws Main\ArgumentException
     * @throws Main\SystemException
     */
    public static function geV1DataClass()
    {
        $className = 'ElementPropV1';
        $tableName = 'b_iblock_element_property';
        $additionalFields = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Main\ORM\Fields\Relations\Reference(
                'ELEMENT',
                \Bitrix\Iblock\ElementTable::getEntity(),
                Main\Entity\Query\Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Main\Entity\Query\Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID');
            }
            $additionalFields[] = new Main\Entity\ReferenceField(
                'ELEMENT',
                \Bitrix\Iblock\ElementTable::getEntity(),
                $referenceFilter
            );
        }
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Main\ORM\Fields\Relations\Reference(
                'PROPERTY',
                \Bitrix\Iblock\PropertyTable::getEntity(),
                Main\Entity\Query\Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Main\Entity\Query\Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID');
            }
            $additionalFields[] = new Main\Entity\ReferenceField(
                'PROPERTY',
                \Bitrix\Iblock\PropertyTable::getEntity(),
                $referenceFilter
            );
        }

        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }

    /**
     * @param $iblockId
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws Main\ArgumentException
     */
    public static function getMultipleDataClass($iblockId)
    {
        $additionalFields = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Main\ORM\Fields\Relations\Reference(
                'PROPERTY',
                \Bitrix\Iblock\PropertyTable::getEntity(),
                Main\Entity\Query\Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Main\Entity\Query\Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID');
            }
            $additionalFields[] = new Main\Entity\ReferenceField(
                'PROPERTY',
                \Bitrix\Iblock\PropertyTable::getEntity(),
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
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws Main\ArgumentException
     */
    protected static function getBaseDataClass($iblockId, $type = 's', array $additionalFields = [])
    {
        $className = 'ElementProp' . ToUpper($type) . $iblockId;
        $tableName = 'b_iblock_element_prop_' . ToLower($type) . $iblockId;
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = (new Main\ORM\Fields\Relations\Reference(
                'ELEMENT',
                \Bitrix\Iblock\ElementTable::getEntity(),
                Main\Entity\Query\Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID')
            ))->configureJoinType('inner');
        } else {
            $referenceFilter = ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Main\Entity\Query\Join::on('this.IBLOCK_ELEMENT_ID', 'ref.ID');
            }
            $additionalFields[] = new Main\Entity\ReferenceField(
                'ELEMENT',
                \Bitrix\Iblock\ElementTable::getEntity(),
                $referenceFilter
            );
        }

        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}