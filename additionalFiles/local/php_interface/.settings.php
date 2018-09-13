<?php
/** лог */
define('LOCAL_LOG_FOLDER', '/local/log');
define('LOG_FOLDER', $_SERVER['DOCUMENT_ROOT'] .LOCAL_LOG_FOLDER);
define('LOG_FILENAME', LOG_FOLDER. '/main.log');
//putenv('WWW_LOG_DIR='. LOG_FOLDER);
//putenv('APP_ENV=prod');

define( 'PATH_TO_404', '/404.php');

define( 'PATH_TO_VENDOR_AUTOLOAD', '/local/vendor/autoload.php');

/** О организации */
define( 'SITE_DEVELOPER_DESCRIPTION', 'Техническая поддержка проекта');
define( 'SITE_DEVELOPER_NAME', 'Название организации');
define( 'SITE_DEVELOPER_PATH', 'https://адрес сайта');