<?php

namespace Vf92\Migration;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Sprint\Migration\HelperManager;
use Sprint\Migration\Version;

abstract class SprintMigrationBase extends Version implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var HelperManager
     */
    private $helperManager;

    public function __construct()
    {
        $this->setLogger(new Logger('Migration', [new StreamHandler(STDOUT, Logger::DEBUG)]));

        /**
         * ВНИМАНИЕ! Ни в коем случае не вызывать тут CUser::Authorize(1) !!!
         * И вообще нигде не вызывать его!
         */

        /**
         * Разрешение менять чужие настройки, чтобы нормально отработал \CUserOptions::SetOptionsFromArray
         */
        $_SESSION["SESS_OPERATIONS"]["edit_other_settings"] = true;

    }

    /**
     * @return LoggerInterface
     */
    protected function log()
    {
        return $this->logger;
    }

    /**
     * Хелпер от пакета Sprint\Migration
     *
     * @return HelperManager
     */
    protected function getHelper()
    {
        if (is_null($this->helperManager)) {
            $this->helperManager = new HelperManager();
        }

        return $this->helperManager;
    }
}
