<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.09.18
 * Time: 22:23
 */

namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\FileTable;

/** @todo доделать orm, чтобы можно было работать со свойствами */
class ElementOrm extends ElementTable
{
    const PROPERTY_MULTIPLE = 'PROPERTY_MULTIPLE';
    const PROPERTY_SINGLE = 'PROPERTY_SINGLE';
    const PROPERTY_BASE = 'PROPERTY_BASE';

    /** @var int */
    public static $iblockId;

    public static function getIblockId()
    {
        return (int)static::$iblockId;
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

        return $map;
    }


    /** @inheritdoc */
    public static function query()
    {
        $query = new ExtendsElementBitrixQuery(static::getEntity());
        $query->setIblockId(static::getIblockId());
        $query->where('IBLOCK_ID', static::getIblockId());
        return $query;
    }
}