<?php

namespace Vf92\Main;

use Vf92\Exception\GroupNotFoundException;
use Bitrix\Main\GroupTable;

/** @deprecated  */
class UserGroupUtils
{
    /**
     * @var array
     */
    static $groupIdByCodeIndex;

    /**
     * Возвращает id группы пользователей по её коду
     *
     * @param string $stringId
     *
     * @return int
     * @throws GroupNotFoundException
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
