<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.09.18
 * Time: 22:23
 */

namespace Vf92\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\FileTable;
use Vf92\Constructor\IblockPropEntityConstructor;

class ElementOrm extends ElementTable

{
    public static $iblockId;

    public static function getIblockId()
    {
        return static::$iblockId;
    }

    public static function setIblockId($iblockId)
    {
        static::$iblockId = $iblockId;
    }

    public static function getMap()
    {
        $map = parent::getMap();
        $map['DETAIL_PICTURE_FILE'] = new ReferenceField(
            'DETAIL_PICTURE_FILE',
            FileTable::getEntity(),
            ['=this.DETAIL_PICTURE' => 'ref.ID']
        );
        $map['PREVIEW_PICTURE_FILE'] = new ReferenceField(
            'PREVIEW_PICTURE_FILE',
            FileTable::getEntity(),
            ['=this.PREVIEW_PICTURE' => 'ref.ID']
        );

        $map['PROPERTY_SINGLE'] = new ReferenceField(
            'PROPERTY_SINGLE',
            IblockPropEntityConstructor::getDataClass(static::getIblockId()),
            ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID']
        );
        $map['PROPERTY_MULTIPLE'] = new ReferenceField(
            'PROPERTY_MULTIPLE',
            IblockPropEntityConstructor::getMultipleDataClass(static::getIblockId()),
            ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID']
        );
    }


    /** @inheritdoc */
    public static function query()
    {
        $query = new ExtendsElementBitrixQuery(static::getEntity());
        $query->where('IBLOCK_ID', static::getIblockId());
        return $query;
    }
}