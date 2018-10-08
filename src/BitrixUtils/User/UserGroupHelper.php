<?php

namespace Vf92\BitrixUtils\Main;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\User\Exception\GroupNotFoundException;
use Bitrix\Main\GroupTable;

/** @deprecated  */
class UserGroupHelper
{
    /**
     * @var array
     */
    public static $groupIdByCodeIndex;

    /**
     * Возвращает id группы пользователей по её коду
     *
     * @param string $stringId
     *
     * @return int
     * @throws GroupNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getGroupIdByCode($stringId)
    {
        if (self::$groupIdByCodeIndex === null) {
            self::$groupIdByCodeIndex = [];

            $dbAllGroups =GroupTable::query()->setSelect(['ID', 'STRING_ID'])->exec();
            while ($group = $dbAllGroups->fetch()) {
                self::$groupIdByCodeIndex[$group['STRING_ID']] = (int)$group['ID'];
            }
        }

        if (!isset(self::$groupIdByCodeIndex[$stringId])) {
            throw new GroupNotFoundException(
                sprintf(
                    'User group `%s` not found',
                    $stringId
                )
            );
        }

        return self::$groupIdByCodeIndex[$stringId];
    }
}
