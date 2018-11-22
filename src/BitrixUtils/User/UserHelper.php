<?php

namespace Vf92\BitrixUtils\User;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CUser;
use Vf92\BitrixUtils\Constructor\EntityConstructor;
use Vf92\BitrixUtils\User\Exception\GroupNotFoundException;

/** @deprecated */
class UserHelper
{
    /**
     * Проверяет вхождение пользователя в группу
     *
     * @param string $groupStringId
     * @param int    $userId
     *
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws GroupNotFoundException
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
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
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
            ->registerRuntimeField(new ReferenceField('USER', UserTable::class, ['=this.USER_ID' => 'ref.ID']))
            ->exec()->fetch();

        if (false === $result || !isset($result['LOGIN'])) {
            return '';
        }

        return trim($result['LOGIN']);
    }
}
