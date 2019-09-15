<?php

namespace Vf92\MiscUtils\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

/**
 * Class ObjectArrayCollection
 *
 * Позволяет легко создать коллекцию объектов, в которую нельзя будет добавить что-то иное, кроме разрешённого типа или
 * интерфейса объектов.
 *
 * @package Vf92\MiscUtils\Collection
 */
abstract class ObjectArrayCollection extends ArrayCollection
{
    /**
     * ObjectArrayCollection constructor.
     *
     * @param array $objects
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $objects = [])
    {
        foreach ($objects as $element) {
            $this->checkType($element);
        }
        parent::__construct($objects);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function add($object)
    {
        $this->checkType($object);
        return parent::add($object);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function set($key, $object)
    {
        $this->checkType($object);
        parent::set($key, $object);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function removeElement($object)
    {
        $this->checkType($object);
        return parent::removeElement($object);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function contains($object)
    {
        $this->checkType($object);
        return parent::contains($object);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function indexOf($object)
    {
        $this->checkType($object);
        return parent::indexOf($object);
    }

    /**
     * @param mixed $object
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    abstract protected function checkType($object):bool ;
}
