<?php


namespace Vf92\BitrixUtils\Config;


use Vf92\MiscUtils\MiscUtils;

/**
 * Class Dbconn
 * @package Vf92\BitrixUtils\Config
 */
class Dbconn
{
    /**
     * @param $list
     */
    public static function save($list)
    {
        $content = '';
        if (!empty($list['db'])) {
            $content .= '//Db' . PHP_EOL;
            $content .= static::getStringDbContent($list['db']);
        }

        if (!empty($list['define']['db'])) {
            $content .= static::getStringDefineContent($list['define']['db']);
        }

        if (!empty($list['define']['cached'])) {
            $content .= '//Cached' . PHP_EOL;
            $content .= static::getStringDefineContent($list['define']['cached']);
        }

        if (!empty($list['define']['permissions'])) {
            $content .= '//Permissions' . PHP_EOL;
            $content .= static::getStringDefineContent($list['define']['permissions']);
        }

        if (!empty($list['umask'])) {
            $content .= '@umask(' . $list['umask'] . ')';
            $content .= PHP_EOL;
        }

        if (!empty($list['define']['cache'])) {
            $content .= '//Cache' . PHP_EOL;
            $content .= static::getStringDefineContent($list['define']['cache']);
        }

        if (!empty($list['define']['project'])) {
            $content .= '//Project' . PHP_EOL;
            $content .= static::getStringDefineContent($list['define']['project']);
        }

        if (!empty($list['define']['custom'])) {
            $content .= '//Custom' . PHP_EOL;
            $content .= static::getStringDefineContent($list['define']['custom']);
        }

        if (!empty($list['custom'])) {
            $content .= '//Unresolved values' . PHP_EOL;
            $content .= $list['custom'];
            $content .= PHP_EOL;
        }

        if (!empty($content)) {
            $content = '<?php ' . PHP_EOL . $content;
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php', $content);
        }
    }

    /**
     * @return array
     */
    public static function get()
    {
        return static::parse();
    }

    /**
     * @return string
     */
    protected static function getFileContent()
    {
        $file = (string)file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php');
        $file = (string)str_replace(['<?php', '<?', '?>'], '', $file);
        return $file;
    }

    /**
     * @return array
     */
    protected static function parse()
    {
        $file = static::getFileContent();
        if (empty($file)) {
            return [];
        }

        $defineList = [];
        $dbList = [];
        $umaskList = [];
        $custom = [];

        $res = preg_match_all('/if\s?\(.*\)[{:]?.*;((endif;)|})?/is', $file, $customMatches);
        if ($res !== false && !empty($customMatches[0])) {
            foreach ($customMatches[0] as $customMatch) {
                $custom[] = $customMatch;
                $file = str_replace($customMatch, '', $file);
            }
        }

        $res = preg_match_all('/define\([\'"]([^\'"]*)[\'"]\s?,\s?[\'"]?([^)]*)[\'"]?\);/im', $file, $defineMatches);
        if ($res !== false && !empty($defineMatches[1]) && !empty($defineMatches[2])) {
            $defineList = array_combine(array_values($defineMatches[1]), array_values($defineMatches[2]));
            $defineList = static::formatValues($defineList);
        }

        $res = preg_match_all('/\$DB([^\s=]*)\s?=?\s?[\'"]([^\'"]*)[\'"]\;/im', $file, $dbMatches);
        if ($res !== false && !empty($dbMatches[1]) && !empty($dbMatches[2])) {
            $dbList = static::clearValues(array_combine(array_values($dbMatches[1]), array_values($dbMatches[2])));
        }

        $res = preg_match_all('/@?umask\((.*)\);/im', $file, $umaskMatches);
        if ($res !== false && !empty($umaskMatches[1])) {
            $umaskList = static::clearValues($umaskMatches[1]);
        }

        return [
            'define' => $defineList,
            'db'     => $dbList,
            'umask'  => $umaskList,
            'custom' => $custom,
        ];
    }

    /**
     * @param $list
     *
     * @return array
     */
    protected static function clearValues($list)
    {
        $result = [];
        foreach ($list as $key => $val) {
            $result[static::clearValue($key)] = static::clearValue($val);
        }
        return $result;
    }

    /**
     * @param $val
     *
     * @return bool|float|string
     */
    protected static function clearValue($val)
    {
        $val = trim($val);
        if ($val === 'true' || $val === 'false') {
            return MiscUtils::getBoolByStringBool($val);
        }
        if (\is_numeric($val)) {
            return (float)$val;
        }
        return (string)$val;
    }

    /**
     * @param $list
     *
     * @return array
     */
    protected static function formatValues($list)
    {
        $result = [];
        $list = self::clearValues($list);
        foreach ($list as $key => $val) {
            if (preg_match('/(^DB)|(^MYSQL)|(_DB)|(_MYSQL)|(_CONNECT$)/i', $key) !== false) {
                $result['db'][$key] = $val;
            } elseif (preg_match('/^CACHED_/i', $key) !== false) {
                $result['cached'][$key] = $val;
            } elseif (preg_match('/(^BX_CACHE_TYPE$)|(^BX_CACHE_SID$)|(^BX_MEMCACHE_HOST$)|(^BX_MEMCACHE_PORT$)/i',
                    $key) !== false) {
                $result['cache'][$key] = $val;
            } elseif (preg_match('/_PERMISSIONS$/i', $key) !== false) {
                $result['permissions'][$key] = $val;
            } elseif (preg_match('/(^SHORT_INSTALL$)|(^VM_INSTALL$)|(^BX_UTF$)|(^BX_COMPRESSION_DISABLED$)/i',
                    $key) !== false) {
                $result['project'][$key] = $val;
            } else {
                $result['custom'][$key] = $val;
            }
        }
        return $result;
    }

    /**
     * @param $list
     *
     * @return string
     */
    protected static function getStringDbContent($list)
    {
        $content = '';
        foreach ($list as $code => $value) {
            $content .= '$DB' . $code . ' = ';
            if (\is_string($value)) {
                $content .= '\'';
            }
            if (\is_bool($value)) {
                $value = MiscUtils::getStringBoolByBool($value);
            }
            $content .= (string)$value;
            if (\is_string($value)) {
                $content .= '\'';
            }
            $content .= PHP_EOL;
        }
        $content .= PHP_EOL;
        return $content;
    }

    /**
     * @param $list
     *
     * @return string
     */
    protected static function getStringDefineContent($list)
    {
        $content = '';
        foreach ($list as $code => $value) {
            $content .= 'define(\'' . $code . '\' , ';
            if (\is_string($value)) {
                $content .= '\'';
            }
            if (\is_bool($value)) {
                $value = MiscUtils::getStringBoolByBool($value);
            }
            $content .= (string)$value;
            if (\is_string($value)) {
                $content .= '\'';
            }
            $content .= ')' . PHP_EOL;
        }
        $content .= PHP_EOL;

        return $content;
    }
}