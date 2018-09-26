<?php
use WebArch\BitrixNeverInclude\BitrixNeverInclude;

/** настрйоки */
$localPath = __DIR__ . '/.settings.php';
if(file_exists($localPath)) {
    require_once $localPath;
}
$localPath = __DIR__ . '/.settings.local.php';
if(file_exists($localPath)) {
    require_once $localPath;
}

/** автоподгрузка из композера для вендора */
$localPath = $_SERVER['DOCUMENT_ROOT'] . PATH_TO_VENDOR_AUTOLOAD;
if(file_exists($localPath)) {
    require_once $localPath;
    /** автоподгрузка модулей битиркса, чтобы их не надо было подключать */
    BitrixNeverInclude::registerModuleAutoload();
}

/** функции */
$localPath = __DIR__ . '/functions/main.php';
if(file_exists($localPath)) {
    require_once $localPath;
}

/** регистрация событий */
$localPath = __DIR__ . '/eventRegister.php';
if(file_exists($localPath)) {
    require_once $localPath;
}

/** агенты */
$localPath = __DIR__ . '/agents/main.php';
if(file_exists($localPath)) {
    require_once $localPath;
}