<?php

namespace Vf92\BitrixUtils\Config;


class AfterConnectD7
{
    public static function get()
    {
        return static::parse();
    }

    public static function save($afterConnectList)
    {
        $content = '';

        if (!empty($afterConnectList['charset'])) {
            $db = '$connection->queryExecute("SET NAMES \'%s\'")';
            $content .= sprintf($db, $afterConnectList['charset']) . PHP_EOL;
        }
        if (!empty($afterConnectList['collation'])) {
            $db = '$connection->queryExecute("SET collation_connection = \'%s\'")';
            $content .= sprintf($db, $afterConnectList['collation']) . PHP_EOL;
        }
        if (!empty($afterConnectList['custom'])) {
            $db = '$connection->queryExecute("%s")';
            foreach ($afterConnectList['custom'] as $item) {
                $content .= sprintf($db, $item) . PHP_EOL;
            }
        }

        if (!empty($content)) {
            $content = '<?php ' . PHP_EOL . '$connection = \Bitrix\Main\Application::getConnection();' . PHP_EOL . $content;
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/after_connect.php', $content);
        }
    }

    protected static function getFileContent()
    {
        $file = (string)file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/after_connect.php');
        $file = (string)str_replace(['<?php', '<?', '?>'], '', $file);
        return $file;
    }

    protected static function parse()
    {
        $file = static::getFileContent();
        if (empty($file)) {
            return [];
        }

        $queries = [];

        $res = preg_match_all('/\$connection->queryExecute\([\'"](.(?![\'"]\))*)[\'"]\);/im', $file, $queryMatches);

        if ($res !== false && !empty($queryMatches[1])) {
            $queries = static::formateValues($queryMatches[1]);
        }

        return $queries;
    }

    protected static function formateValues($data)
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
}