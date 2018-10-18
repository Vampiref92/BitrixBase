<?php
defined('NO_KEEP_STATISTIC') || define('NO_KEEP_STATISTIC', 'Y');
defined('NOT_CHECK_PERMISSIONS') || define('NOT_CHECK_PERMISSIONS', true);
defined('NO_AGENT_CHECK') || define('NO_AGENT_CHECK', true);
defined('PUBLIC_AJAX_MODE') || define('PUBLIC_AJAX_MODE', true);

$GLOBALS['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'];

/** @noinspection PhpIncludeInspection */
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

set_time_limit(0);