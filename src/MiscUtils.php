<?php

namespace Vf92;

/**
 * Class MiscTools
 * @package Vf92
 *
 * Все прочие полезные функции, для которых пока нет отдельного класса.
 */
class MiscUtils
{
    /**
     * Возвращает имя класса без namespace
     *
     * @param $object
     *
     * @return string
     */
    public static function getClassName($object)
    {
        $className = get_class($object);
        $pos = strrpos($className, '\\');
        if ($pos) {

            return substr($className, $pos + 1);
        }

        return $pos;
    }
}
