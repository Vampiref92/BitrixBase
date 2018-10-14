<?php namespace Vf92\BitrixUtils\Command;

use Vf92\Log\LazyLoggerAwareTrait;
use Bitrix\Main\Loader;
use Bitrix\Sender\MailingManager;
use CAgent;
use CEvent;
use Psr\Log\LoggerAwareInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->setName('bitrix:cronevents')->setDescription('Start bitrix events');
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     *
     * @see hack in /bin/symfony_console.php
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws RuntimeException
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
        } catch (\Exception $e) {
            $this->log()->error($e->getMessage());
        }
    }
}
