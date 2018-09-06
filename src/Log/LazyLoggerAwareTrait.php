<?php

namespace Vf92\Log;

use Vf92\MiscUtils;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Trait LazyLoggerAwareTrait
 *
 * Позволяет сразу использовать метод log() для логирования без необходимости однообразно вызывать фабрику создания
 * логеров LoggerFactory. Если требуется в некоторых случаях изменить тип лога или его имя, то перед первым вызовом
 * log() или в конструкторе следует установить соответствующие параметры. Дополнительный плюс - вместо прямой работы со
 * свойством logger идёт работа с методом log() , благодаря которой и код выглядит более читаемо ( log()->error('!') ),
 * и метод log() можно в любом частном случае переопределить под конкретные нужды.
 *
 * @package Vf92\Log
 *
 */
trait LazyLoggerAwareTrait
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $logName;

    /**
     * @var string
     */
    protected $logType = 'main';

    /**
     * @return LoggerInterface
     * @throws RuntimeException
     */
    public function log()
    {
        if (is_null($this->logger)) {
            $this->logger = LoggerFactory::create($this->getLogName(), $this->getLogType());
        }

        return $this->logger;
    }

    /**
     * @return string
     */
    public function getLogType()
    {
        return $this->logType;
    }

    /**
     * @param string $logType
     *
     * @return $this
     */
    public function withLogType($logType)
    {
        $this->logType = $logType;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogName()
    {
        if (is_null($this->logName)) {
            $this->logName = MiscUtils::getClassName($this);
        }

        return $this->logName;
    }

    /**
     * @param string $logName
     *
     * @return $this
     */
    public function withLogName($logName)
    {
        $this->logName = $logName;

        return $this;
    }

}
