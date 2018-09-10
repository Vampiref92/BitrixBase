<?php
use WebArch\BitrixNeverInclude\BitrixNeverInclude;

/** настрйоки */
require_once __DIR__ . '/settings.php';

/** автоподгрузка из композера для вендора */
require_once $_SERVER['DOCUMENT_ROOT'] . PATH_TO_VENDOR_AUTOLOAD;
/** автоподгрузка модулей битиркса, чтобы их не надо было подключать */
BitrixNeverInclude::registerModuleAutoload();

/** функции */
require_once __DIR__ . '/functions/main.php';
/** агенты */
require_once __DIR__ . '/agents/main.php';

/** регистрация событий */
require_once __DIR__ . '/eventRegister.php';