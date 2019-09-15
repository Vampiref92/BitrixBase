<?php

namespace Vf92\BitrixUtils\Tables;

use Bitrix\Main\UserTable;
use Vf92\BitrixUtils\Orm\BaseDataManagerTable;
use Vf92\BitrixUtils\Orm\Model\User;

/**
 * Class UserCustomBaseTable
 * @package Vf92\BitrixUtils\Tables
 */
class UserCustomBaseTable extends UserTable
{

    /**
     * @inheritDoc
     */
    public static function getObjectClass()
    {
        return User::class;
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