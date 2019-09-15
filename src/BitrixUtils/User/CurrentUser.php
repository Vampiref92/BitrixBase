<?php

namespace Vf92\BitrixUtils\User;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\EO_User;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CUser;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;
use Vf92\BitrixUtils\Exceptions\User\CurrentUserNotFoundException;

/**
 * Class CurrentUser
 * @package Vf92\BitrixUtils\User
 */
class CurrentUser
{
    /**
     * @var CUser
     */
    protected $curUser;

    /**
     * @throws CurrentUserNotFoundException
     */
    public function __construct()
    {
        if ($this->curUser === null) {
            global $USER;
            $this->curUser = $USER;
        }
        if (!$this->curUser instanceof CUser) {
            throw new CurrentUserNotFoundException('Не найден пользователь', 0);
        }
    }

    /**
     * @return CurrentUser
     * @throws CurrentUserNotFoundException
     */
    public static function get(): CurrentUser
    {
        return new static();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getBitrixObject() !== null ? (int)$this->getBitrixObject()->GetID() : 0;
    }

    /**
     * @return bool
     */
    public function isAuth(): bool
    {
        return $this->getBitrixObject() !== null ? (bool)$this->getBitrixObject()->IsAuthorized() : false;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getBitrixObject() !== null ? (bool)$this->getBitrixObject()->IsAdmin() : false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetFirstName() : '';
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetLastName() : '';
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetSecondName() : '';
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetFullName() : '';
    }

    /**
     * @param null $format
     *
     * @return string
     */
    public function getFullNameByFormat($format = null): string
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
    public function getLogin(): string
    {
        return $this->getBitrixObject() !== null ? (string)$this->getBitrixObject()->GetLogin() : '';
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->getBitrixObject()->GetEmail() ?: '';
    }

    /**
     * @return CUser
     */
    public function getBitrixObject(): CUser
    {
        return $this->curUser;
    }

    /**
     * @return null|EO_User
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws VersionException
     * @throws VersionException
     */
    public function getD7BitrixObject(): ?EO_User
    {
        if (Version::getInstance()->isVersionLessThan('18.0.4')) {
            throw new VersionException();
        }
        return UserTable::getById($this->getId())->fetchObject();
    }
}