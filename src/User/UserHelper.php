<?php

namespace Vf92\Main;

use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\UserTable;
use CUser;
use Vf92\Constructor\EntityConstructor;

/** @deprecated  */
class UserHelper
{
    /**
     * Проверяет вхождение пользователя в группу
     *
     * @param string $groupStringId
     * @param int    $userId
     *
     * @return bool
     * @throws \Vf92\Exception\GroupNotFoundException
     */
    public static function isInGroup($groupStringId, $userId)
    {
        $userId = (int)$userId;
        $groupStringId = trim($groupStringId);

        if ($userId <= 0 || empty($groupStringId)) {
            return false;
        }

        return in_array(
            UserGroupHelper::getGroupIdByCode($groupStringId),
            CUser::GetUserGroup($userId),
            false
        );
    }

    /**
     * Возвращает логин пользователя по хешу его запомненной авторизации.
     *
     * @param string $hash
     *
     * @return string
     */
    public static function getLoginByHash($hash)
    {
        $hash = trim($hash);
        if (empty($hash)) {
            return '';
        }

        $dataManager = EntityConstructor::compileEntityDataClass('UserStoredAuth', 'b_user_stored_auth');
        $result = $dataManager::query()
            ->setSelect(['LOGIN'])
            ->setFilter(['=STORED_HASH' => $hash])
            ->registerRuntimeField(new ReferenceField('USER', UserTable::class, array('=this.USER_ID' => 'ref.ID')))
            ->exec()->fetch();
//        $query =
//            'SELECT LOGIN ' .
//            'FROM b_user_stored_auth as USA ' .
//            'INNER JOIN b_user as U ' .
//            'ON USA.USER_ID = U.ID ' .
//            'WHERE USA.STORED_HASH = \'' . $hash . '\'';
//
//        $result = Application::getConnection()->query($query)->fetch();

        if (false === $result || !isset($result['LOGIN'])) {
            return '';
        }

        return trim($result['LOGIN']);
    }
}
