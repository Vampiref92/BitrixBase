<?php

namespace Vf92\Constructor;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\SystemException;

/**
 * Class IblockSectionUfPropEntityConstructor
 * @package Vf92\Constructor
 */
class IblockSectionUfPropEntityConstructor extends EntityConstructor
{
    const SINGLE_TYPE='s';
    const MULTIPLE_TYPE='m';

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

    /**
     * @param $iblockId
     *
     * @return DataManager|string
     */
    public static function getMultipleDataClass($iblockId)
    {
        return static::getBaseDataClass($iblockId, static::MULTIPLE_TYPE);
    }

    /**
     * @param        $iblockId
     * @param string $type
     *
     * @return DataManager|string
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    protected static function getBaseDataClass($iblockId, $type = 's')
    {
        $className = 'Ut'.ToLower($type).'Iblock' . $iblockId.'Section';
        $tableName = 'b_ut'.ToLower($type).'_iblock_' . $iblockId.'_section';
        $additionalFields = [
            'PROPERTY' => new ReferenceField(
                'PROPERTY',
                SectionTable::class,
                ['=this.VALUE_ID' => 'ref.ID']
            )
        ];
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}