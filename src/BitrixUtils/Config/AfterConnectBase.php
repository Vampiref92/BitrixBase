<?php

namespace Vf92\BitrixUtils\Config;

/**
 * Class AfterConnectBase
 * @package Vf92\BitrixUtils\Config
 */
abstract class AfterConnectBase
{
    public const FILE_NAME='';
    public const QUERY_PREFIX = '';
    public const REGEXP = '';
    public const SAVE_PREFIX_CONTENT = '';

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function formatValues(array $data): array
    {
        $result = [
            'collation' => '',
            'charset'   => '',
            'custom'    => [],
        ];
        foreach ($data as $item) {
            if (preg_match('/SET\sNAMES\s[\'"]([^\'"]*)[\'"]/is', $item, $matches) !== false) {
                $result['charset'] = $matches[1];
            } elseif (preg_match('/SET\scollation_connection\s?=\s?[\'"]([^\'"]*)[\'"]/is', $item,
                    $matches) !== false) {
                $result['collation'] = $matches[1];
            } else {
                $result['custom'] = $item;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function get(): array
    {
        return static::parse();
    }

    /**
     * @return string
     */
    protected static function getFileContent(): string
    {
        $file = (string)file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/'.static::FILE_NAME.'.php');
        $file = (string)str_replace(['<?php', '<?', '?>'], '', $file);
        return $file;
    }

    /**
     * @return array
     */
    protected static function parse(): array
    {
        $file = static::getFileContent();
        if (empty($file)) {
            return [];
        }
        $queries = [];
        $res = preg_match_all(static::REGEXP, $file, $queryMatches);
        if ($res !== false && !empty($queryMatches[1])) {
            $queries = static::formatValues($queryMatches[1]);
        }
        return $queries;
    }

    /**
     * @param array $afterConnectList
     */
    public static function save(array $afterConnectList): void
    {
        $content = '';
        if (!empty($afterConnectList['charset'])) {
            $db = static::QUERY_PREFIX . '("SET NAMES \'%s\'")';
            $content .= sprintf($db, $afterConnectList['charset']) . PHP_EOL;
        }
        if (!empty($afterConnectList['collation'])) {
            $db = static::QUERY_PREFIX . '("SET collation_connection = \'%s\'")';
            $content .= sprintf($db, $afterConnectList['collation']) . PHP_EOL;
        }
        if (!empty($afterConnectList['custom'])) {
            $db = static::QUERY_PREFIX . '("%s")';
            foreach ($afterConnectList['custom'] as $item) {
                $content .= sprintf($db, $item) . PHP_EOL;
            }
        }
        if (!empty($content)) {
            $content = '<?php ' . PHP_EOL . static::SAVE_PREFIX_CONTENT . $content;
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/after_connect.php', $content);
        }
    }
}