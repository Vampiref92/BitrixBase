<?php

namespace Vf92\BitrixUtils\Console;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Throwable;

class ConsoleApp
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var string
     */
    private $documentRoot;

    public function __construct(string $documentRoot)
    {
        $this->documentRoot = $documentRoot;
    }

    public function getName()
    {
        return 'project';
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getDir()
    {
        return __DIR__;
    }

    public function getProjectClass()
    {
        return 'Project';
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        try {

            $this->init();
            $this->launchSymfonyConsoleApp($input, $output);
            $this->finish();

        } catch (Throwable $exception) {

            echo sprintf(
                "[%s] %s (%s)\n%s\n",
                get_class($exception),
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getTraceAsString()
            );

            //Non-zero because error
            die(1);

        }
    }

    private function init()
    {
        if (php_sapi_name() !== 'cli') {
            die('Can not run in this mode. Bye!');
        }

        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            $_SERVER['DOCUMENT_ROOT'] = $this->documentRoot;
        }
        require_once __DIR__.'../../../tools/bxAjaxProlog.php';

        // ini_set('memory_limit', '2G');
        error_reporting(E_ERROR);
    }

    /**
     * @throws Exception
     */
    private function launchSymfonyConsoleApp(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->application = new Application();
        $this->application->setName($this->getName() . ' console interface');
        $this->application->setVersion($this->getVersion());
        $this->registerCommands(__DIR__, 'Vf92');
        $this->registerCommands($this->getDir(), $this->getProjectClass());
        $this->application->run($input, $output);
    }

    private function finish()
    {
        //TODO Возможно, требуется оптимизировать, чтобы Битрикс не делал чего-то лишнего в консоли
        /** @noinspection PhpIncludeInspection */
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    }

    /**
     * Регистрирует все команды из namespace
     */
    private function registerCommands($dir, $projectClass)
    {
        $files = new Finder();
        $files->files()->in($dir . '/Command');

        foreach ($files as $file) {

            $classPath = str_replace(
                [
                    \dirname($dir),
                    '.php',
                ],
                '',
                $file->getRealPath()
            );


            $command = "\\" . $projectClass . str_replace('/', '\\', $classPath);

            $this->application->add(new $command);
        }
    }
}
