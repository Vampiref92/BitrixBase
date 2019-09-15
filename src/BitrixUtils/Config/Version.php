<?php

namespace Vf92\BitrixUtils\Config;

use Bitrix\Main\ModuleManager;
use CUpdateClient;
use function array_key_exists;
use function count;
use function explode;
use function is_array;
use function is_string;

/**
 * Class Version
 * @package Vf92\BitrixUtils\Config
 */
class Version
{
    /**
     * @var static
     */
    private static $instance;
    /**
     * @var string
     */
    private $redaction;
    /**
     * @var string
     */
    private $moduleVersion;

    /**
     * @return Version
     */
    public static function getInstance(): Version
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Возвращает редакцию сайта на русском
     * @return string
     */
    public function getProductRedaction(): string
    {
        if ($this->redaction === null) {
            if (!defined('HELP_FILE')) {
                define('HELP_FILE', 'marketplace/sysupdate.php');
            }
            require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_client.php';
            $errorMessage = '';
            $arUpdateList = CUpdateClient::GetUpdatesList($errorMessage);
            if (empty($errorMessage)) {
                if (is_array($arUpdateList) && array_key_exists('CLIENT', $arUpdateList)) {
                    $this->redaction = (string)$arUpdateList['CLIENT'][0]['@']['LICENSE'];
                }
            }
            if ($this->redaction === null) {
                $this->redaction = 'undefined';
            }
        }
        return $this->redaction;
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public function isVersionMoreThan($version, $curVersion = null): bool
    {
        if ($curVersion === null) {
            $curVersion = $this->getProductVersion();
        }
        return $this->matchVersions($curVersion, $version, '>');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public function isVersionMoreEqualThan($version, $curVersion = null): bool
    {
        if ($curVersion === null) {
            $curVersion = $this->getProductVersion();
        }
        return $this->matchVersions($curVersion, $version, '>=');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public function isVersionLessThan($version, $curVersion = null): bool
    {
        if ($curVersion === null) {
            $curVersion = $this->getProductVersion();
        }
        return $this->matchVersions($curVersion, $version, '<');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public function isVersionLessEqualThan($version, $curVersion = null): bool
    {
        if ($curVersion === null) {
            $curVersion = $this->getProductVersion();
        }
        return $this->matchVersions($curVersion, $version, '<=');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public function isEqual($curVersion, $version): bool
    {
        return $curVersion === $version;
    }

    /**
     * @param        $curVersion
     * @param        $version
     * @param string $operation
     *
     * @return bool
     */
    public function matchVersions($curVersion, $version, $operation = '>='): bool
    {
        $explodeList = explode('.', $curVersion);
        $this->prepareVersion($explodeList);
        $explodeCompareList = explode('.', $version);
        $this->prepareVersion($explodeCompareList);
        $res1 = $this->compareVal($curVersion[0], $version[0], $operation);
        if ($res1 < 0) {
            return false;
        }
        if ($res1 === 0) {
            $res2 = $this->compareVal($curVersion[0], $version[0], $operation);
            if ($res2 < 0) {
                return false;
            }
            if ($res2 === 0) {
                $res3 = $this->compareVal($curVersion[0], $version[0], $operation);
                if ($res3 <= 0) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Возвращает версию главного модуля
     * @return string
     */
    public function getProductVersion(): string
    {
        return $this->getModuleVersion('main');
    }

    /**
     * @param $module
     *
     * @return mixed
     */
    public function getModuleVersion($module)
    {
        if (!isset($this->moduleVersion[$module])) {
            $res = ModuleManager::getVersion($module);
            $this->moduleVersion[$module] = is_string($res) ? $res : 'undefined';
        }
        return $this->moduleVersion[$module];
    }

    /**
     * @param array $explodeList
     */
    protected function prepareVersion(&$explodeList): void
    {
        foreach ($explodeList as &$item) {
            $item = (int)$item;
        }
        unset($item);
        $count = count($explodeList);
        if ($count < 3) {
            for ($i = $count; $i <= 3; $i++) {
                $explodeList[$i] = 0;
            }
        }
    }

    /**
     * @param $val1
     * @param $val2
     * @param $operator
     *
     * @return int
     */
    protected function compareVal($val1, $val2, $operator): int
    {
        if ($val1 === $val2) {
            return 0;
        }
        switch ($operator) {
            case '>':
                if ($val1 > $val2) {
                    return 1;
                }
                break;
            case '>=':
                if ($val1 >= $val2) {
                    return 1;
                }
                break;
            case '<':
                if ($val1 < $val2) {
                    return 1;
                }
                break;
            case '<=':
                if ($val1 <= $val2) {
                    return 1;
                }
                break;
        }
        return -1;
    }
}