<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Vf92\BitrixUtils\OldOrm\Model\BitrixArrayItemBase;

/**
 * Class CollectionBase
 *
 * @package Vf92\BitrixUtils\OldOrm\Collection
 */
abstract class CollectionBase extends AbstractLazyCollection
{
    /**
     * Do the initialization logic
     *
     * @return void
     */
    protected function doInitialize()
    {
        $this->collection = new ArrayCollection();
        foreach ($this->fetchElement() as $element) {
            /**
             * @var BitrixArrayItemBase
             */
            $this->collection->set($element->getId(), $element);
        }
    }

    /**
     * Извлечение модели
     */
    abstract protected function fetchElement(): \Generator;
}
