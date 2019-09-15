<?php

namespace Vf92\BitrixUtils\Tables\HL;

use Bitrix\Main;

/**
 * Class BaseHl
 * @package Vf92\BitrixUtils\Tables\HL
 */
abstract class BaseHl extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     * @throws Main\NotImplementedException
     */
    public static function getTableName()
    {
        throw new Main\NotImplementedException();
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws Main\SystemException
     */
    public static function getMap()
    {
        throw new Main\NotImplementedException();
    }
}