<?php

namespace Vf92\BitrixUtils\Config;

/**
 * Class AfterConnectD7
 * @package Vf92\BitrixUtils\Config
 */
class AfterConnectD7 extends AfterConnectBase
{
    public const FILE_NAME = 'after_connect_d7';
    public const QUERY_PREFIX = '$connection->queryExecute';
    public const REGEXP = '/\$connection->queryExecute\([\'"](.(?![\'"]\))*)[\'"]\);/im';
    public const SAVE_PREFIX_CONTENT = '$connection = \Bitrix\Main\Application::getConnection();' . PHP_EOL;
}