<?php

namespace Vf92\MiscUtils;

use Bitrix\Main\Type\DateTime;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_object;
use function is_string;

/**
 * Class MiscTools
 * @package Vf92\MiscUtils
 *
 * Все прочие полезные функции, для которых пока нет отдельного класса.
 */
class MiscUtils
{
    /**
     * Возвращает имя класса без namespace
     *
     * @param $object
     *
     * @return string
     */
    public static function getClassName($object): string
    {
        $className = get_class($object);
        $pos = strrpos($className, '\\');
        if ($pos) {

            return substr($className, $pos + 1);
        }
        return $pos;
    }

    /**
     * @param array $arItems
     */
    public static function trimArrayStrings(array &$arItems): void
    {
        if (is_array($arItems) && !empty($arItems)) {
            foreach ($arItems as $key => $val) {
                if (is_array($val)) {
                    self::trimArrayStrings($val);
                } else {
                    $arItems[$key] = trim($val);
                }
            }
        }
    }

    /**
     * @param float|int $size
     * @param int       $round
     *
     * @return string
     */
    public static function getFormattedSize($size, int $round = 2): string
    {
        $sizes = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
        for ($i = 0; $size > 1024 && $i < count($sizes) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $round) . ' ' . $sizes[$i];
    }

    /**
     * @param array $list
     */
    public static function eraseArray(array &$list): void
    {
        $tmpList = [];
        foreach ($list as $key => $val) {
            $tmpList[$key] = $val;
        }
        $list = static::eraseArrayReturn($tmpList);
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public static function eraseArrayReturn(array $list): array
    {
        foreach ($list as $key => $val) {
            if (is_object($val)) {
                continue;
            }
            if (is_array($val)) {
                if (!empty($val)) {
                    $newVal = self::eraseArrayReturn($val);
                    if ($newVal === null || empty($newVal)) {
                        unset($list[$key]);
                    } else {
                        $list[$key] = $val = $newVal;
                    }
                } else {
                    unset($list[$key]);
                }
            } else {
                if ($val === null || empty($val)) {
                    unset($list[$key]);
                }
            }
        }
        return $list;
    }

    /**
     * @param array $params
     *
     * @return array|bool|mixed
     */
    public static function getUniqueArray(array $params = [])
    {
        if (!isset($params['arr1'])) {
            return false;
        }
        if (!isset($params['arr2'])) {
            return $params['arr1'];
        }
        if (!isset($params['bReturnFullDiffArray'])) {
            $params['bReturnFullDiffArray'] = false;
        }
        if (!isset($params['isChild'])) {
            $params['isChild'] = false;
        }
        if (!isset($params['skipKeys'])) {
            $params['skipKeys'] = [];
        }
        $result = [];
        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
            $arTmp = [];
            $diff = [];
        }
        foreach ($params['arr1'] as $key => $val) {
            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                $arTmp[$key] = $val;
            }
            if (is_array($val)) {
                if (!in_array($key, $params['skipKeys'], true)) {
                    if (!isset($params['arr2'][$key]) || (!empty($val) && empty($params['arr2'][$key]))) {
                        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                            $diff[$key] = $val;
                        } else {
                            $result[$key] = $val;
                        }
                    } else {
                        $return = self::getUniqueArray([
                                'arr1'                 => $val,
                                'arr2'                 => $params['arr2'][$key],
                                'bReturnFullDiffArray' => $params['bReturnFullDiffArray'],
                                'skipKeys'             => $params['skipKeys'],
                                'isChild'              => true,
                            ]);
                        if (!empty($return)) {
                            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                                $diff[$key] = $return;
                            } else {
                                $result[$key] = $return;
                            }
                        }
                    }
                }
            } else {
                if (!in_array($key, $params['skipKeys'], true)) {
                    if (!isset($params['arr2'][$key])) {
                        if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                            $diff[$key] = $val;
                        } else {
                            $result[$key] = $val;
                        }
                    } else {
                        $tmpVal = '0';
                        $tmpArr2Val = '1';
                        if (is_object($val)) {
                            if ($val instanceof \Bitrix\Main\Type\DateTime) {
                                /** @var DateTime $val */
                                $tmpVal = $val->format(DateTime::getFormat());
                                /** @var DateTime $val2 */
                                $val2 = $params['arr2'][$key];
                                $tmpArr2Val = $val2->format(DateTime::getFormat());
                                unset($val2);
                            }
                        }
                        if ((is_object($val) && $tmpVal !== $tmpArr2Val) || (!is_object($val) && $val !== $params['arr2'][$key])) {
                            if ($params['bReturnFullDiffArray'] && $params['isChild']) {
                                $diff[$key] = $val;
                            } else {
                                $result[$key] = $val;
                            }
                        }
                    }
                }
            }
        }
        if ($arTmp !== null && !empty($arTmp) && $diff !== null && count($diff) > 0) {
            $result = $arTmp;
        }
        return $result;
    }

    /**
     * @param bool $val
     *
     * @return string
     */
    public static function getStringBoolByBool(bool $val): string
    {
        if (is_bool($val)) {
            if ($val === true) {
                $val = 'true';
            } else {
                $val = 'false';
            }
        }
        return $val;
    }

    /**
     * @param string $val
     *
     * @return bool
     */
    public static function getBoolByStringBool(string $val): bool
    {
        $res = null;
        if (is_string($val)) {
            if (is_numeric($val)) {
                if ((int)$val === 1) {
                    $res = true;
                } elseif ((int)$val === 0) {
                    $res = false;
                }
            } else {
                if ($val === 'true') {
                    $res = true;
                } elseif ($val === 'false') {
                    $res = false;
                }
            }
        }
        if ($res === null) {
            $res = $val;
        }
        return $res;
    }
}
