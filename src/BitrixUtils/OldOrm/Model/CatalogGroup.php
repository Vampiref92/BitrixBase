<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Adv\Bitrixtools\Tools\BitrixUtils;

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
    public function isBase(): bool
    {
        return $this->base;
    }

    /**
     * @param bool $base
     *
     * @return $this
     */
    public function withBase(bool $base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Возвращает языкозависимое название типа цен.
     *
     * @return string
     */
    public function getLangName(): string
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
    public function withLangName(string $name)
    {
        $this->NAME_LANG = $name;

        return $this;
    }

    /**
     * Возвращает символьный код типа цен, несмотря на нелепое название.
     *
     * @return string
     */
    public function getName(): string
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
    public function withName(string $name): Interfaces\ItemInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::withName($name);
    }
}
