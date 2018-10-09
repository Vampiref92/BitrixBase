<?php

namespace Vf92\BitrixUtils;

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Result;

/**
 * Class BitrixUtils
 * @package Vf92\BitrixUtils
 */
class BitrixUtils
{
    const BX_BOOL_FALSE = 'N';
    const BX_BOOL_TRUE = 'Y';

    /**
     * Определяет является ли запрос аяксовым
     *
     * @return bool
     */
    public static function isAjax()
    {
        //TODO Правильно делать не так, а смотреть на хеадеры `X-Requested-With` === `XMLHttpRequest`

        return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || $_SERVER['HTTP_BX_AJAX'] === 'true');
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    public static function bool2BitrixBool($value)
    {
        return $value ? self::BX_BOOL_TRUE : self::BX_BOOL_FALSE;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function bitrixBool2bool($value)
    {
        return self::BX_BOOL_TRUE === $value;
    }

    /**
     * Возвращает одно сообщение об ошибке из любого Битриксового результата, где ошибки почему-то живут во
     * множественном числе.
     *
     * @param Result $result
     *
     * @return string
     */
    public static function extractErrorMessage(Result $result)
    {
        return implode('; ', $result->getErrorMessages());
    }

    /**
     * Возвращает редакцию сайта на русском
     * @return string
     */
    public static function getProductRedaction()
    {
        if (!defined('HELP_FILE')) {
            define('HELP_FILE', 'marketplace/sysupdate.php');
        }
        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_client.php';
        $errorMessage = '';
        $arUpdateList = \CUpdateClient::GetUpdatesList($errorMessage);
        if (empty($errorMessage)) {
            if (\is_array($arUpdateList) && \array_key_exists('CLIENT', $arUpdateList)) {
                echo (string)$arUpdateList['CLIENT'][0]['@']['LICENSE'];
            }
        }
        return 'undefined';
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public static function isVersionMoreThan($version, $curVersion = null)
    {
        if($curVersion === null){
            $curVersion = static::getProductVersion();
        }
        return static::matchVersions($curVersion, $version, '>');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public static function isVersionMoreEqualThan($version, $curVersion = null)
    {
        if($curVersion === null){
            $curVersion = static::getProductVersion();
        }
        return static::matchVersions($curVersion, $version, '>=');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public static function isVersionLessThan($version, $curVersion = null)
    {
        if($curVersion === null){
            $curVersion = static::getProductVersion();
        }
        return static::matchVersions($curVersion, $version, '<');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public static function isVersionLessEqualThan($version, $curVersion = null)
    {
        if($curVersion === null){
            $curVersion = static::getProductVersion();
        }
        return static::matchVersions($curVersion, $version, '<=');
    }

    /**
     * @param $curVersion
     * @param $version
     *
     * @return bool
     */
    public static function isEqual($curVersion, $version)
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
    public static function matchVersions($curVersion, $version, $operation = '>=')
    {
        $explodeList = \explode('.', $curVersion);
        self::prepareVersion($explodeList);
        $explodeCompareList = \explode('.', $version);
        self::prepareVersion($explodeCompareList);
        $res1 = static::compareVal($curVersion[0], $version[0], $operation);
        if ($res1 < 0) {
            return false;
        }
        if ($res1 === 0) {
            $res2 = static::compareVal($curVersion[0], $version[0], $operation);
            if ($res2 < 0) {
                return false;
            }
            if ($res2 === 0) {
                $res3 = static::compareVal($curVersion[0], $version[0], $operation);
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
    public static function getProductVersion()
    {
        $res = ModuleManager::getVersion('main');
        return \is_string($res) ? $res : 'undefined';
    }

    /**
     * @param array $explodeList
     */
    protected static function prepareVersion(&$explodeList)
    {
        foreach ($explodeList as &$item) {
            $item = (int)$item;
        }
        unset($item);
        $count = \count($explodeList);
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
    protected static function compareVal($val1, $val2, $operator)
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
