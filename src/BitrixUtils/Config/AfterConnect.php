<?php

namespace Vf92\BitrixUtils\Config;

/**
 * Class AfterConnect
 * @package Vf92\BitrixUtils\Config
 *
 */
class AfterConnect extends AfterConnectBase
{

    public const FILE_NAME='after_connect';
    public const QUERY_PREFIX = '$DB->Query';
    public const REGEXP = '/\$DB->Query\([\'"](.(?![\'"]\))*)[\'"]\);/im';
    public const SAVE_PREFIX_CONTENT = '';
}