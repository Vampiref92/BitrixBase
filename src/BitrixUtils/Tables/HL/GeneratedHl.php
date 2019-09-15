<?php

namespace Vf92\BitrixUtils\Tables\HL;

use Bitrix\Main;
use Vf92\BitrixUtils\Constructor\EntityConstructor;

/**
 * Class GeneratedHl
 * @package Vf92\BitrixUtils\Tables\HL
 */
abstract class GeneratedHl extends BaseHl
{
    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws Main\SystemException
     */
    public static function getMap()
    {
        return (EntityConstructor::compileEntityDataClass(static::class, static::getTableName()))::getMap();
    }
}