<?php

namespace Vf92\BitrixUtils\Orm;

use Bitrix\Main\Entity\DataManager;

/**
 * Class BaseDataManager
 * @package Vf92\BitrixUtils\Orm
 */
class BaseDataManagerTable extends DataManager
{
    public static $map;
    public static $tableName;
    public static $objectClass;

    public static function getMap()
    {
        return static::$map;
    }

    public static function getTableName()
    {
        return static::$tableName;
    }

    public static function getObjectClass()
    {
        return static::$objectClass;
    }

    /**
     * @param DataManager|string $className
     */
    public static function init($className): void
    {
        static::$map = $className::getMap();
        static::$tableName = $className::getTableName();
        static::$objectClass = $className::getObjectClass();
    }
}