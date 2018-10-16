<?php

namespace Vf92\BitrixUtils\Console\Command;

use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Data\StaticHtmlCache;
use CBitrixComponent;
use CFile;
use CHTMLPagesCache;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheClear
 * @package Adv\Console\Command
 *
 * TODO Переделать и оформить как отдельный пакет. Сейчас очистка кеша не достаточно надёжная.
 *
 */
class CacheClear extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const ARG_CACHE_TYPE = 'cache-type';

    const OPT_CACHE_PATH = 'cache-path';

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setLogger(new Logger('Cache cleaner', [new StreamHandler(STDOUT, Logger::DEBUG)]));
    }

    protected function configure()
    {
        $this->setName('cache:clear')
             ->setDescription('Clear cache')
             ->addArgument(
                 self::ARG_CACHE_TYPE,
                 InputArgument::OPTIONAL,
                 'Cache type [all, menu, managed, html]',
                 'all'
             )
             ->addOption(
                 self::OPT_CACHE_PATH,
                 'path',
                 InputOption::VALUE_OPTIONAL,
                 'Cache path',
                 ''
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheType = $input->getArgument(self::ARG_CACHE_TYPE);
        $cachePath = $input->getOption(self::OPT_CACHE_PATH);
        $cacheEngine = Cache::getCacheEngineType();

        $this->log(LogLevel::INFO, 'Clear cache start');
        $this->log(LogLevel::INFO, 'Cache Engine: ' . $cacheEngine);
        $this->log(LogLevel::INFO, 'Cache Type: ' . $cacheType);
        if ($cachePath) {
            $this->log(LogLevel::INFO, 'Cache Path: ' . $cachePath);
        }

        $this->fileCacheClean($cacheType, $cacheEngine, $cachePath);
        Application::getInstance()->getManagedCache()->cleanAll();
        if (!$cachePath) {
            switch ($cacheType) {
                case 'menu':
                    $GLOBALS['CACHE_MANAGER']->CleanDir('menu');
                    CBitrixComponent::clearComponentCache('bitrix:menu');
                    break;
                case 'managed':
                    $GLOBALS['CACHE_MANAGER']->CleanAll();
                    $GLOBALS['stackCacheManager']->CleanAll();
                    break;
                case 'html':
                    StaticHtmlCache::getInstance()->deleteAll();
                    break;
                case 'all':
                    BXClearCache(true);
                    $GLOBALS['CACHE_MANAGER']->CleanAll();
                    $GLOBALS['stackCacheManager']->CleanAll();
                    StaticHtmlCache::getInstance()->deleteAll();
                    break;
            }
        }
        $this->logResult();
    }

    protected function log($level, $message = '', array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    protected function fileCacheClean(string $cacheType, string $cacheEngine, string $cachePath)
    {
        if ($cacheType === 'html' || $cacheEngine === 'cacheenginefiles') {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/cache_files_cleaner.php';
            $obCacheCleaner = new \CFileCacheCleaner($cacheType);
            if (!$obCacheCleaner->InitPath($cachePath)) {
                throw new \RuntimeException('Cant init File Cache Cleaner');
            }
        } else {
            return;
        }

        if (!$cachePath) {
            $_SESSION['CACHE_STAT'] = [];
        }

        $currentTime = time();
        $endTime = time() + 30;
        $bDoNotCheckExpiredDate = in_array($cacheType, ['all', 'menu', 'managed', 'html'], true);

        if ($cacheType === 'html') {
            $obCacheCleaner->Start();
            $space_freed = 0;
            while ($file = $obCacheCleaner->GetNextFile()) {
                if (is_string($file)
                    && !preg_match("/(\\.enabled|.config\\.php)\$/", $file)
                ) {
                    $file_size = filesize($file);
                    $_SESSION['CACHE_STAT']['scanned']++;
                    $_SESSION['CACHE_STAT']['space_total'] += $file_size;

                    if (@unlink($file)) {
                        $_SESSION['CACHE_STAT']['deleted']++;
                        $_SESSION['CACHE_STAT']['space_freed'] += $file_size;
                        $space_freed += $file_size;
                    } else {
                        $_SESSION['CACHE_STAT']['errors']++;
                    }
                }

                usleep(2500);
            }
            CHTMLPagesCache::writeStatistic(false, false, false, false, -$space_freed);
        } elseif ($cacheEngine === 'cacheenginefiles') {
            $obCacheCleaner->Start();
            while ($file = $obCacheCleaner->GetNextFile()) {
                if (is_string($file)) {
                    $date_expire = $obCacheCleaner->GetFileExpiration($file);
                    if ($date_expire) {
                        $file_size = filesize($file);

                        $_SESSION['CACHE_STAT']['scanned']++;
                        $_SESSION['CACHE_STAT']['space_total'] += $file_size;

                        if ($bDoNotCheckExpiredDate
                            || ($date_expire < $currentTime)
                        ) {
                            if (@unlink($file)) {
                                $_SESSION['CACHE_STAT']['deleted']++;
                                $_SESSION['CACHE_STAT']['space_freed'] += $file_size;
                            } else {
                                $_SESSION['CACHE_STAT']['errors']++;
                            }
                        }
                    }
                }

                usleep(2500);
            }
        } else {
            $_SESSION['CACHE_STAT'] = [];
        }
    }

    protected function logResult()
    {
        if ($_SESSION['CACHE_STAT']) {
            $this->log(LogLevel::INFO, 'Processed: ' . (int)$_SESSION['CACHE_STAT']['scanned']);
            $this->log(
                LogLevel::INFO,
                'Size of files processed: ' . CFile::FormatSize($_SESSION['CACHE_STAT']['space_total'])
            );
            $this->log(LogLevel::INFO, 'Deleted: ' . (int)$_SESSION['CACHE_STAT']['deleted']);
            $this->log(
                LogLevel::INFO,
                'Size of files deleted: ' . CFile::FormatSize($_SESSION['CACHE_STAT']['space_freed'])
            );
            $this->log(LogLevel::INFO, 'Deletion errors: ' . (int)$_SESSION['CACHE_STAT']['errors']);
        } else {
            $this->log(LogLevel::INFO, 'The cache files has been deleted');
        }
    }
}
