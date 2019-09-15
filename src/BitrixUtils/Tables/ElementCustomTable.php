<?php

namespace Vf92\BitrixUtils\Tables;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Vf92\BitrixUtils\Orm\BaseDataManagerTable;

/**
 * Class ElementCustomTable
 * @package Vf92\BitrixUtils\Tables
 */
class ElementCustomTable extends ElementTable
{

    public static function getMap()
    {
        $map = parent::getMap();
        $map['CREATED_BY_USER'] = new Reference('CREATED_BY_USER', UserCustomBaseTable::class,
            ['=this.CREATED_BY' => 'ref.ID'], ['join_type' => 'LEFT']);
        return $map;
    }

    /**
     * @inheritDoc
     */
    public static function add(array $data)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::add($data);
    }

    /**
     * @inheritDoc
     */
    public static function update($primary, array $data)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::update($primary, $data);
    }

    /**
     * @inheritDoc
     */
    public static function delete($primary)
    {
        BaseDataManagerTable::init(static::class);
        return BaseDataManagerTable::delete($primary);
    }
}