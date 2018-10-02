<?php


namespace Vf92\BitrixUtils\Config;


/**
 * Class Dbconn
 * @package Vf92\BitrixUtils\Config
 */
class Dbconn
{
    /**
     * @return bool|mixed|string
     */
    protected static function getFileContent()
    {
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php');
        $file = str_replace(['<?php', '<?', '?>'], '', $file);
        return $file;
    }

    /**
     * @return array
     */
    protected static function parse()
    {
        $file = static::getFileContent();

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
        if ($res !== false && !empty($dbList[1]) && !empty($dbList[2])) {
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
        if ($val === 'true') {
            return (bool)true;
        }
        if ($val === 'false') {
            return (bool)false;
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
            if (preg_match('/(^DB)|(^MYSQL)|(.*_DB)|(.*_MYSQL)|(.*_CONNECT)/i', $key) !== false) {
                $result['db'][$key] = $val;
            } elseif (preg_match('/^CACHED_/i', $key) !== false) {
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
     */
    public
    static function save(
        $list
    ) {
        $content = '<?php ';

        $content .= '//Db' . PHP_EOL;
        $content .= static::getStringDbContent($list['db']);

        $content .= static::getStringDefineContent($list['define']['db']);

        $content .= '//Cache' . PHP_EOL;
        $content .= static::getStringDefineContent($list['define']['cache']);

        $content .= '//Permissions' . PHP_EOL;
        $content .= static::getStringDefineContent($list['define']['permissions']);

        $content .= '@umask(' . $list['umask'] . ')';
        $content .= PHP_EOL;

        $content .= '//Project' . PHP_EOL;
        $content .= static::getStringDefineContent($list['define']['project']);

        $content .= '//Custom' . PHP_EOL;
        $content .= static::getStringDefineContent($list['define']['custom']);

        $content .= '//Unresolved values' . PHP_EOL;
        $content .= $list['custom'];
        $content .= PHP_EOL;

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/dbconn.php', $content);
    }

    /**
     * @param $list
     *
     * @return string
     */
    protected
    static function getStringDbContent(
        $list
    ) {
        $content = '';
        foreach ($list['db'] as $code => $value) {
            $content .= '$DB' . $code . ' = ';
            if (\is_string($value)) {
                $content .= '\'';
            }
            $content .= $value;
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
    protected
    static function getStringDefineContent(
        $list
    ) {
        $content = '';
        foreach ($list as $code => $value) {
            $content .= 'define(\'' . $code . '\' , ';
            if (\is_string($value)) {
                $content .= '\'';
            }
            $content .= $value;
            if (\is_string($value)) {
                $content .= '\'';
            }
            $content .= ')' . PHP_EOL;
        }
        $content .= PHP_EOL;

        return $content;
    }

    /**
     * @return array
     */
    public
    static function get()
    {
        return [];
    }
}