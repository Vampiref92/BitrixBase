<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.09.18
 * Time: 22:23
 */

namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\FileTable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Constructor\IblockPropEntityConstructor;

/** @todo доделать orm, чтобы можно было работать со свойствами */
class ElementOrm extends ElementTable
{
    const PROPERTY_MULTIPLE = 'PROPERTY_MULTIPLE';
    const PROPERTY_SINGLE = 'PROPERTY_SINGLE';
    const PROPERTY_BASE = 'PROPERTY_BASE';

    /** @var int */
    public static $iblockId;
    protected static $additionalMap = [];

    /**
     * @return int
     */
    public static function getIblockId()
    {
        return (int)static::$iblockId;
    }

    /**
     * @param int $iblockId
     */
    public static function setIblockId($iblockId)
    {
        static::$iblockId = $iblockId;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap()
    {
        $map = parent::getMap();
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $map[] = new Reference(
                'DETAIL_PICTURE_FILE',
                FileTable::getEntity(),
                Join::on('this.DETAIL_PICTURE', 'ref.ID')
            );
            $map[] = new Reference(
                'PREVIEW_PICTURE_FILE',
                FileTable::getEntity(),
                Join::on('this.PREVIEW_PICTURE', 'ref.ID')
            );
        } else {
            $referenceFilter = ['=this.DETAIL_PICTURE' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Join::on('this.DETAIL_PICTURE', 'ref.ID');
            }
            $map[] = new ReferenceField(
                'DETAIL_PICTURE_FILE',
                FileTable::getEntity(),
                $referenceFilter
            );

            $referenceFilter = ['=this.PREVIEW_PICTURE' => 'ref.ID'];
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = Join::on('this.PREVIEW_PICTURE', 'ref.ID');
            }
            $map[] = new ReferenceField(
                'PREVIEW_PICTURE_FILE',
                FileTable::getEntity(),
                $referenceFilter
            );
        }

        return array_merge($map, static::$additionalMap);
    }


    /**
     * @param     int     $iblockId
     * @param string|null $version
     *
     * @return Query
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function query($iblockId, $version = null)
    {
        if ($version === null) {
            $query = IblockTable::query()->setSelect(['VERSION']);
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $query->where('ID', $iblockId);
            } else {
                $query->setFilter(['=ID' => $iblockId]);
            }
            $version = (int)$query->exec()->fetch()['VERSION'];
        }
        $props = [];
        $query = PropertyTable::query()->setSelect(['ID', 'CODE']);
        if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
            $query->where('IBLOCK_ID', $iblockId);
        } else {
            $query->setFilter(['=IBLOCK_ID' => $iblockId]);
        }
        $res = $query->exec();
        while ($prop = $res->fetch()){
            $props[$prop['ID']] = $prop['CODE'];
        }        switch ($version) {
            case 1:
                $entity = (IblockPropEntityConstructor::geV1DataClass())::getEntity();
                $map = [];
                if (Version::getInstance()->isVersionMoreEqualThan('18')) {
                    $map[] = new OneToMany(
                        'PROPS_SINGLE',
                        $entity,
                        'ELEMENT'
                    );
                    $map[] = new OneToMany(
                        'PROPS_MULTIPLE',
                        $entity,
                        'ELEMENT'
                    );
                } else {
                    /** @todo вероятно работать не будет */
                    $referenceFilter = ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID'];
                    if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                        $referenceFilter = Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID');
                    }
                    $map[] = new ReferenceField(
                        'PROPS_SINGLE',
                        $entity,
                        $referenceFilter
                    );
                    $map[] = new ReferenceField(
                        'PROPS_MULTIPLE',
                        $entity,
                        $referenceFilter
                    );
                }
                static::$additionalMap[] = array_merge(static::$additionalMap, $map);
                break;
            case 2:
                $entitySimple = (IblockPropEntityConstructor::geV1DataClass())::getEntity();
                $entityMultiple = (IblockPropEntityConstructor::geV1DataClass())::getEntity();
                $map = [];
                if (Version::getInstance()->isVersionMoreEqualThan('18')) {
                    $map[] = new OneToMany(
                        'PROPS_SINGLE',
                        $entitySimple,
                        'ELEMENT'
                    );
                    $map[] = new OneToMany(
                        'PROPS_MULTIPLE',
                        $entityMultiple,
                        'ELEMENT'
                    );
                } else {
                    /** @todo вероятно работать не будет */
                    $referenceFilter = ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID'];
                    if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                        $referenceFilter = Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID');
                    }
                    $map[] = new ReferenceField(
                        'PROPS_SINGLE',
                        $entitySimple,
                        $referenceFilter
                    );
                    $map[] = new ReferenceField(
                        'PROPS_MULTIPLE',
                        $entityMultiple,
                        $referenceFilter
                    );
                }
                static::$additionalMap[] = array_merge(static::$additionalMap, $map);
                break;
        }
        return parent::query();
    }
}