<?php

namespace Vf92\BitrixUtils\Command;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\UserTable;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BitrixClearUser
 *
 * @package Vf92\BitrixUtils\Command
 */
class BitrixClearUser extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    
    const ARGUMENT_MINIMAL_ID = 'mid';
    
    const OPTION_DEBUG        = 'debug';
    
    protected $debug    = false;
    
    private $hasError = false;
    
    /**
     * BitrixClearHighloadBlock constructor.
     *
     * @param null $name
     *
     * @throws LogicException
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setLogger(new Logger('Migrator', [new StreamHandler(STDOUT, Logger::DEBUG)]));
    }
    
    /**
     * @throws InvalidArgumentException
     */
    public function configure()
    {
        $this->setName('bitrix:clear:user')
            ->setDescription('Clear users')
            ->addOption(
                self::OPTION_DEBUG,
                        self::OPTION_DEBUG[0],
                        InputOption::VALUE_NONE,
                        'Show debug messages'
            )
            ->addArgument(
                self::ARGUMENT_MINIMAL_ID,
                          InputArgument::REQUIRED,
                          'Minimal user id. Must be an integer, greater than 6'
            );
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws InvalidArgumentException
     * @return null
     *
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $minimalId   = $input->getArgument(self::ARGUMENT_MINIMAL_ID);
        $this->debug = $input->getOption(self::OPTION_DEBUG);
    
        if ($minimalId < 11) {
            throw new InvalidArgumentException('mid must be an integer, greater than 10');
        }
        
        try {
            $this->removeUsers($minimalId);
            
            $this->logger->info(sprintf('Users has been delete.'));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Unknown error: %s', $e->getMessage()));
        }
        
        return null;
    }

    /**
     * @param int $minimalId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function removeUsers($minimalId)
    {
        $userIdCollection = (new Query(UserTable::getEntity()))->setSelect(['ID'])->setFilter(['>=ID' => $minimalId])->exec();
        
        $count = $userIdCollection->getSelectedRowsCount();
        
        $this->logger->debug(sprintf('Users count - %s', $count));
        
        while ($user = $userIdCollection->fetch()) {
            $this->removeUser($user['ID']);
            $this->debugMessage(sprintf('Users count - %s', $count--));
        }
        
        if (!$this->hasError) {
            Application::getConnection()->query(sprintf('ALTER TABLE b_user AUTO_INCREMENT=%u', $minimalId + 1));
        }
    }

    /**
     * @param int $id
     */
    private function removeUser($id)
    {
        if (\CUser::Delete($id)) {
            $this->debugMessage(sprintf('User with id %s was removed', $id));
        } else {
            global $APPLICATION;
            
            $this->hasError = true;
            $this->logger->error(sprintf('User with id %s remove error: %s', $id, $APPLICATION->GetException()));
        }
    }

    /**
     * @param string $message
     */
    private function debugMessage($message)
    {
        if ($this->debug) {
            $this->logger->debug($message);
        }
    }
}
