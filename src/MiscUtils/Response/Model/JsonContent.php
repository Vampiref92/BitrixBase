<?php

namespace Vf92\MiscUtils\Response\Model;

use JsonSerializable;

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

    public function __construct($message = '', $success = true, $data = null)
    {
        $this->message = $message;
        $this->success = (int)$success;
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return (bool)$this->success;
    }

    /**
     * @param bool $success
     *
     * @return JsonContent
     */
    public function withSuccess($success)
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
    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return JsonContent
     */
    public function withMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getReload()
    {
        return (bool)$this->reload;
    }

    public function withReload($reload)
    {
        $this->reload = $reload;
        return $this;
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function withRedirect($redirect)
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
