<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Adv\Bitrixtools\Tools\BitrixUtils;
use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ActiveReadModelInterface;
use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ItemInterface;
use Vf92\BitrixUtils\OldOrm\Model\Interfaces\ToArrayInterface;

/**
 * Class BitrixArrayItemBase
 *
 * Из-за того, что JMS-serializer не видит трейты и может использоваться не везде, где будет использоваться BitrixOrm,
 * а сам Битрикс числа и все остальные типы данных возвращает как строки, решено:
 *
 * 1 Снабдить модельные классы BitrixOrm явными аннотациями для строгого соблюдения типа.
 *
 * 2 В таких аннотациях использовать полное имя класса аннотации, чтобы не требовать обязательной установки пакета
 * jms/serializer для работоспособности.
 *
 * 3 При использовании трейта дублировать свойства с аннотацией типа.
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 *
 */
abstract class BitrixArrayItemBase implements ActiveReadModelInterface, ItemInterface, ToArrayInterface
{
    public const PATTERN_PROPERTY_VALUE = '~^(?>(PROPERTY_\w+)_VALUE)$~';

    /**
     * @var bool
     * @JMS\Serializer\Annotation\Type("bool")
     */
    protected $active = true;

    /**
     * @var int
     * @JMS\Serializer\Annotation\Type("int")
     */
    protected $ID = 0;

    /**
     * @var string
     * @JMS\Serializer\Annotation\Type("string")
     */
    protected $XML_ID = '';

    /**
     * @var int
     */
    protected $SORT = 500;

    /**
     * @var string
     */
    protected $NAME = '';

    /**
     * BitrixArrayItemBase constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        foreach ($fields as $field => $value) {
            if ($value === null) {
                continue;
            }

            if ($this->isExists($field)) {
                $this->{$field} = $value;
            } elseif ($this->isProperty($field)) {
                $propertyName = $this->getPropertyName($field);

                if ($this->isExists($propertyName)) {
                    $this->{$propertyName} = $value;
                }
            }
        }

        if (isset($fields['ACTIVE'])) {
            $this->withActive(BitrixUtils::bitrixBool2bool($fields['ACTIVE']));
        }
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
            if ($field === 'active' && \is_bool($value)) {
                $value = BitrixUtils::bool2BitrixBool($value);
                $result['ACTIVE'] = $value;
                continue;
            }
            if (0 === strpos($field, 'PROPERTY_')) {
                $result['PROPERTY_VALUES'][substr($field, 9)] = $value;
            } elseif (!\is_object($value) && null !== $value) {
                $result[$field] = $value;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->ID;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function withId($id)
    {
        $this->ID = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getXmlId()
    {
        return $this->XML_ID;
    }

    /**
     * @param string $xmlId
     *
     * @return $this
     */
    public function withXmlId($xmlId)
    {
        $this->XML_ID = $xmlId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function withActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->NAME;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function withName($name)
    {
        $this->NAME = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return (int)$this->SORT;
    }

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function withSort($sort)
    {
        $this->SORT = $sort;

        return $this;
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    protected function getPropertyName($fieldName)
    {
        return preg_replace(self::PATTERN_PROPERTY_VALUE, '$1', $fieldName);
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    protected function isProperty($fieldName)
    {
        return preg_match(self::PATTERN_PROPERTY_VALUE, $fieldName) > 0;
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
}
