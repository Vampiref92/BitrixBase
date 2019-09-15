<?php

namespace Vf92\Log;

use Exception;
use Vf92\MiscUtils\EnvType;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Class LoggerFactory
 * @package Vf92\Log
 */
abstract class LoggerFactory
{
    /**
     * @var LoggerInterface[]
     */
    private static $loggers;

    /**
     * Возвращает настроенный Logger объект
     *
     * @param string $logName
     * @param string $logType
     * @param bool   $tryStdOut Попытаться, если скрипт запущен из консоли, добавить вывод лога в STDOUT
     *
     * @return LoggerInterface
     * @throws Exception
     */
    public static function create($logName, $logType = 'main', $tryStdOut = true)
    {
        $loggerKey = (string)$logName . '@' . (string)$logType;

        //Если такой логер уже есть, отдать сразу, чем ускорить работу
        if (isset(self::$loggers[$loggerKey]) && self::$loggers[$loggerKey] instanceof LoggerInterface) {
            return self::$loggers[$loggerKey];
        }

        //В logName должны быть только буквы, цифры или символ подчёркивания.
        if (empty(trim($logName)) || preg_match('/[^A-Z_0-9]/i', $logName)) {
            throw new InvalidArgumentException('Log name must be string of A-Z, 0-9 or `_` characters');
        }

        $logger = new Logger($logName);

        $envLogDir = getenv('WWW_LOG_DIR');
        if (false === $envLogDir) {
            throw new RuntimeException('WWW_LOG_DIR env variable is not set');
        }
        $logDir = $envLogDir . '/' . $logType . '/';
        $fileAll = 'all_' . date('Y_m_d') . '.log';
        $fileError = 'error_' . date('Y_m_d') . '.log';

        $minErrorType = Logger::DEBUG;
        if (EnvType::isProd()) {
            $minErrorType = Logger::INFO;
        }

        $logger
            ->pushHandler(new StreamHandler($logDir . $fileAll, $minErrorType))
            ->pushHandler(new StreamHandler($logDir . $fileError, Logger::ERROR));

        //STDOUT определён только при запуске из консоли
        if ($tryStdOut && defined('STDOUT') && is_resource(STDOUT)) {
            $logger->pushHandler(new StreamHandler(STDOUT, Logger::DEBUG));
        }

        self::$loggers[$loggerKey] = $logger;

        return $logger;
    }
}
