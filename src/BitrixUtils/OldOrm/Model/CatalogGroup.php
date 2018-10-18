<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\BitrixUtils;

class CatalogGroup extends BitrixArrayItemBase
{
    /**
     * @var string
     * @JMS\Serializer\Annotation\Type("string")
     */
    protected $NAME_LANG = '';

    /**
     * @var bool
     * @JMS\Serializer\Annotation\Type("bool")
     */
    protected $base = false;

    public function __construct(array $fields = [])
    {
        parent::__construct($fields);
        if (isset($fields['BASE'])) {
            $this->withBase(BitrixUtils::bitrixBool2bool($fields['BASE']));
        }
    }

    /**
     * @return bool
     */
    public function isBase()
    {
        return $this->base;
    }

    /**
     * @param bool $base
     *
     * @return $this
     */
    public function withBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Возвращает языкозависимое название типа цен.
     *
     * @return string
     */
    public function getLangName()
    {
        return $this->NAME_LANG;
    }

    /**
     * Устанавливает языкозависимое название типа цен.
     *
     * @param string $name
     *
     * @return $this
     */
    public function withLangName($name)
    {
        $this->NAME_LANG = $name;

        return $this;
    }

    /**
     * Возвращает символьный код типа цен, несмотря на нелепое название.
     *
     * @return string
     */
    public function getName()
    {
        return parent::getName();
    }

    /**
     * Устанавливает символьный код типа цен, несмотря на нелепое название.
     *
     * @param string $name
     *
     * @return $this
     */
    public function withName($name)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::withName($name);
    }
}
