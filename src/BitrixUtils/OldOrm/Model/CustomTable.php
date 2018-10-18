<?php

/*
 * @copyright Copyright (c) ADV/web-engineering co
 */

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ActiveReadModelInterface;
use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ToArrayInterface;

/**
 * Class CustomTable
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
abstract class CustomTable implements ActiveReadModelInterface, ToArrayInterface
{
    /**
     * ModelInterface constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        foreach ($fields as $field => $value) {
            if ($this->isExists($field)) {
                $this->{$field} = $value;
            }
        }
    }
    
    /**
     * @param string $fieldName
     *
     * @return bool
     */
    protected function isExists($fieldName)
    {
        return property_exists($this, $fieldName);
    }
    
    /**
     * @inheritDoc
     */
    public static function createFromPrimary($primary)
    {
        /**
         * @todo Заглушка. Удалить после реализации создания в более конкретных классах.
         */
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        //TODO Дописать лучше часть про поля
        foreach (get_object_vars($this) as $field => $value) {
            if (!\is_object($value) && $value !== null) {
                $result[$field] = $value;
            }
        }

        return $result;
    }
}
