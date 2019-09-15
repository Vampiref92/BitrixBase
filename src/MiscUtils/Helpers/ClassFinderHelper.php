<?php
namespace Vf92\MiscUtils\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class ClassFinderHelper
 * @package Vf92\MiscUtils\Helpers
 */
class ClassFinderHelper
{
    /**
     * Поиск классов с совпадением имени в определенной папке
     * @param string $findNamespace
     * @param string $findDir
     *
     * @return array
     */
    public static function getClasses(string $findNamespace = '', string $findDir = '/'): array
    {
        if($findDir === '/'){
            $findDir = $_SERVER['DOCUMENT_ROOT'].'/';
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($findDir));
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        $classes = [];
        foreach ($regex as $file => $value) {
            $current = static::parseTokens(token_get_all(file_get_contents(str_replace('\\', '/', $file))));
            if ($current !== false) {
                list($namespace, $class) = $current;
                if(!empty($findNamespace)) {
                    if ($namespace === $findNamespace) {
                        $classes[] = $namespace . $class;
                    }
                } else {
                    if(!empty($namespace)) {
                        $classes[] = $namespace . $class;
                    } else {
                        $classes[] = $class;
                    }
                }
            }
        }
        return $classes;
    }

    /**
     * @param string $pathToFile
     *
     * @return string|null
     */
    public static function getClassByFile(string $pathToFile): ?string
    {
        return shell_exec("php -r \"include('$pathToFile'); echo end(get_declared_classes());\"");
    }

    /**
     * @param string $pathToFile
     *
     * @return mixed
     */
    public static function getClassByFileInclude(string $pathToFile)
    {
        include_once($pathToFile);
        return end(get_declared_classes());
    }


    /**
     * @param array $tokens
     *
     * @return array|bool
     */
    /**
     * @param array $tokens
     *
     * @return array|bool
     */
    /**
     * @param array $tokens
     *
     * @return array|bool
     */
    /**
     * @param array $tokens
     *
     * @return array|bool
     */
    private static function parseTokens(array $tokens)
    {
        $nsStart = false;
        $classStart = false;
        $namespace = '';
        foreach ($tokens as $token) {
            if ($token[0] === T_CLASS) {
                $classStart = true;
            }
            if ($classStart && $token[0] === T_STRING) {
                return [$namespace, $token[1]];
            }
            if ($token[0] === T_NAMESPACE) {
                $nsStart = true;
            }
            if ($nsStart && $token[0] === ';') {
                $nsStart = false;
            }
            if ($nsStart && $token[0] === T_STRING) {
                $namespace .= $token[1] . '\\';
            }
        }

        return false;
    }
}