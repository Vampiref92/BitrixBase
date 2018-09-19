<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\SystemException;

/**
 * Class IblockPropEntityConstructor
 * @package Vf92\BitrixUtils\Constructor
 */
class IblockPropEntityConstructor extends EntityConstructor
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
        $additionalFields[] = [
            'PROPERTY' => new ReferenceField(
                'PROPERTY',
                PropertyTable::getEntity(),
                ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID']
            ),
        ];
        return static::getBaseDataClass($iblockId, static::MULTIPLE_TYPE, $additionalFields);
    }

    protected static function getBaseDataClass($iblockId, $type = 's', $additionalFields = [])
    {
        $className = 'ElementProp'.ToUpper($type) . $iblockId;
        $tableName = 'b_iblock_element_prop_'.ToLower($type) . $iblockId;
        $additionalFields[] = [
            'ELEMENT' => new ReferenceField(
                'ELEMENT',
                ElementTable::getEntity(),
                ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID']
            ),
        ];
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}