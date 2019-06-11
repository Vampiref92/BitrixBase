<?php


namespace Vf92\BitrixUtils\User;


use Bitrix\Main\UserTable;
use Bitrix\Main\EO_User;
use Vf92\BitrixUtils\Config\Exception\VersionException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\User\Exception\CurerntUserNotFoundException;

/**
 * Class CurrentUser
 * @package Vf92\BitrixUtils\User
 */
class CurrentUser
{
    protected $curUser;

    /**
     * @throws CurerntUserNotFoundException
     */
    public function __construct()
    {
        if ($this->curUser === null) {
            global $USER;
            $this->curUser = $USER;
        }
        if (!$this->curUser instanceof \CUser) {
            throw new CurerntUserNotFoundException('Не найден пользователь', 0);
        }
    }

    /**
     * @return CurrentUser
     * @throws CurerntUserNotFoundException
     */
    public static function get()
    {
        return new static();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getBitrixObject() !== null ? (int)$this->getBitrixObject()->GetID() : 0;
    }

    /**
     * @return bool
     */
    public function isAuth()
    {
        return $this->getBitrixObject() !== null ? (bool)$this->getBitrixObject()->IsAuthorized() : false;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getBitrixObject() !== null ? (bool)$this->getBitrixObject()->IsAdmin() : false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetFirstName() : '';
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetLastName() : '';
    }

    /**
     * @return string
     */
    public function getSecondName()
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetSecondName() : '';
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetFullName() : '';
    }

    /**
     * @param null $format
     *
     * @return string
     */
    public function getFullNameByFormat($format = null)
    {
        return UserHelper::getFullName([
            'NAME'        => $this->getName(),
            'SECOND_NAME' => $this->getSecondName(),
            'LAST_NAME'   => $this->getLastName(),
            'LOGIN'       => $this->getLogin(),
            'ID'          => $this->getId(),
            'EMAIL'       => $this->getEmail(),
        ], $format);
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetLogin() : '';
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getBitrixObject()->GetEmail() ?: '';
    }

    /**
     * @return \CUser
     */
    public function getBitrixObject()
    {
        return $this->curUser;
    }

    /**
     * @return null|EO_User
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws VersionException
     */
    public function getD7BitrixObject()
    {
        if (Version::getInstance()->isVersionMoreEqualThan('18.0.4')) {
            return UserTable::getById($this->getId())->fetchObject();
        }
        throw new VersionException('Для выполнения данной функции нужна версия не ниже 18.0.4');
    }
}