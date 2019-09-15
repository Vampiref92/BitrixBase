<?php

namespace Vf92\MiscUtils\Response\Model;

use JsonSerializable;

/**
 * Class JsonContent
 * @package Vf92\MiscUtils\Response\Model
 */
class JsonContent implements JsonSerializable
{
    /**
     * @var int
     */
    private $success;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $reload = false;

    /**
     * @var string
     */
    private $redirect = '';

    /**
     * JsonContent constructor.
     *
     * @param string $message
     * @param bool   $success
     * @param array|null   $data
     */
    public function __construct(string $message = '', bool $success = true, $data = null)
    {
        $this->message = $message;
        $this->success = (int)$success;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function getSuccess(): bool
    {
        return (bool)$this->success;
    }

    /**
     * @param bool $success
     *
     * @return JsonContent
     */
    public function withSuccess(bool $success): JsonContent
    {
        $this->success = (int)$success;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return JsonContent
     */
    public function withData($data): JsonContent
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return JsonContent
     */
    public function withMessage(string $message): JsonContent
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReload(): bool
    {
        return (bool)$this->reload;
    }

    /**
     * @param bool $reload
     *
     * @return $this
     */
    public function withReload(bool $reload): JsonContent
    {
        $this->reload = $reload;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * @param string $redirect
     *
     * @return $this
     */
    public function withRedirect(string $redirect): JsonContent
    {
        $this->redirect = $redirect;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
