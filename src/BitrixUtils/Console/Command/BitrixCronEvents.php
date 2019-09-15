<?php namespace Vf92\BitrixUtils\Console\Command;

use Bitrix\Main\Loader;
use Bitrix\Sender\MailingManager;
use CAgent;
use CEvent;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vf92\Log\LazyLoggerAwareTrait;

/**
 * Class BitrixCronEvents
 *
 * @package Vf92\BitrixUtils\Command
 */
class BitrixCronEvents extends Command implements LoggerAwareInterface
{
    use LazyLoggerAwareTrait;

    /**
     * @throws InvalidArgumentException
     */
    public function configure()
    {
        $this->setName('bitrix:cron_events')->setDescription('Start bitrix events');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     * @see hack in /bin/symfony_console.php
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        CEvent::CheckEvents();
        CAgent::CheckAgents();
        try {
            if (Loader::includeModule('sender')) {
                MailingManager::checkPeriod(false);
                MailingManager::checkSend();
            }
        } catch (Exception $e) {
            $this->log()->error($e->getMessage());
        }
    }
}
