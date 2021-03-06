<?php

namespace Vf92\BitrixUtils\User;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CSite;
use CUser;
use Vf92\BitrixUtils\Constructor\EntityConstructor;
use Vf92\BitrixUtils\Exceptions\User\GroupNotFoundException;

/**
 * Class UserHelper
 * @package Vf92\BitrixUtils\User
 */
class UserHelper
{
    /**
     * @var null
     */
    public static $curUser = null;

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
    public static function isInGroup($groupStringId, $userId): bool
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
    public static function getLoginByHash($hash): string
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

    /** $data= [LOGIN,EMAIL,NAME,LAST_NAME,SECOND_NAME,ID]
     *
     * @param      $data
     * @param null $format
     *
     * @return string
     */
    public static function getFullName($data, $format = null): string
    {
        if($format === null){
            $format = CSite::GetNameFormat();
        }
        return (string)CUser::FormatName($format,
            $data,
            true,
            true
        );
    }

    /**
     * @param string $salt
     * @param string $originalPassword
     *
     * @return string
     */
    public static function getPasswordHash(string $salt, string $originalPassword): string
    {
        return md5($salt . $originalPassword);
    }

    /**
     * @param string $originalPassword
     * @param string $salt
     *
     * @return string
     */
    public static function getPasswordToSave(string $originalPassword, string $salt = ''):string
    {
        if(empty($salt)) {
            $salt = static::getPasswordSalt();
        }
        return $salt.static::getPasswordHash($salt, $originalPassword);
    }

    /**
     * @return string
     */
    public static function getPasswordSalt(): string
    {
        return randString(8, array(
            'abcdefghijklnmopqrstuvwxyz',
            'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
            '0123456789',
            ",.<>/?;:[]{}\\|~!@#\$%^&*()-_+=",
        ));
    }
}
