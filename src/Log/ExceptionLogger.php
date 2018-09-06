<?php

namespace Vf92\Log;

use Bitrix\Main\Diag\ExceptionHandlerLog;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class ExceptionLogger
 * @package Vf92\Log
 *
 * В bitrix/.settings.php в ключе exception_handling.value.log указать:
 *
 * [
 *      'class_name' => ExceptionLogger::class,
 *      'settings'   => [
 *          'logger' => \Vf92\Log\LoggerFactory::create('BX_EX_HNDLR'),
 *      ],
 * ]
 *
 */
class ExceptionLogger extends ExceptionHandlerLog implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param array $options
     *
     * @return void
     */
    public function initialize(array $options)
    {
        if (!isset($options['logger']) || !($options['logger'] instanceof LoggerInterface)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Missing valid logger. Pass logger of type %s to `logger` option',
                    LoggerInterface::class
                )
            );
        }

        $this->setLogger($options['logger']);
    }

    /**
     * @param Throwable $exception
     * @param int $logType
     */
    public function write($exception, $logType)
    {
        $this->log()->emergency(
            sprintf(
                "%s [%s] %s (%s)",
                static::logTypeToString($logType),
                get_class($exception),
                $exception->getMessage(),
                $exception->getCode()
            ),
            ['stackTrace' => $exception->getTraceAsString()]
        );
    }

    /**
     * @return LoggerInterface
     */
    protected function log()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->logger;
    }

}
