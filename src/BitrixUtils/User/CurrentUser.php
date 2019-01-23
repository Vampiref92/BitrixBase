<?php


namespace Vf92\BitrixUtils\User;


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
     * @return int
     */
    public function getId()
    {
        return $this->curUser !== null ? (int)$this->curUser->GetID() : 0;
    }

    /**
     * @return bool
     */
    public function isAuth()
    {
        return $this->curUser !== null ? (bool)$this->curUser->IsAuthorized() : false;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->curUser !== null ? (bool)$this->curUser->IsAdmin() : false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->curUser !== null ? (string)$this->curUser->GetFirstName() : '';
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->curUser !== null ? (string)$this->curUser->GetFullName() : '';
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->curUser !== null ? (string)$this->curUser->GetLogin() : '';
    }

    /**
     * @return \CUser
     */
    public function getBitrixObject()
    {
        return $this->curUser;
    }
}