<?php
/** либо выбираем текущий конфиг, либо настраиваем свой */
$config = new DotsUnited\PhpCsFixer\Php56Config();
//$config = new DotsUnited\PhpCsFixer\Php71Config();
$projectPath = realpath(__DIR__.'/../');
$config = \PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'                                     => true,
        'linebreak_after_opening_tag'               => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_php4_constructor'                       => true,
        'no_useless_else'                           => true,
        'ordered_imports'                           => true,
        'php_unit_construct'                        => true,
        'phpdoc_order'                              => true,
        'pow_to_exponentiation'                     => true,
        'random_api_migration'                      => true,
        'align_multiline_comment'                   => true,
        'phpdoc_types_order'                        => true,
        'no_null_property_initialization'           => true,
        'no_unneeded_final_method'                  => true,
        'no_unneeded_curly_braces'                  => true,
        'no_superfluous_elseif'                     => true,
        'trailing_comma_in_multiline_array'         => true,
        'no_unused_imports'                         => true,
        'include'                                   => true,
//        'array_syntax'                              => [
//            'syntax' => 'short',
//        ],
    ]);

$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : $projectPath;
$config->setCacheFile($cacheDir . '/.php_cs.cache');

// устанавливаем где искать
// исключения действуют для корня
$config->getFinder()
    ->in(['./'])
    ->exclude([
        'bitrix',
        'upload',
        'html',
        'local/modules/sprint.migration',
        'local/docs',
        'vendor',
        'local/vendor'
    ])
    ->files()
    ->name('*.php');

$config->setUsingCache(false);

return $config;
