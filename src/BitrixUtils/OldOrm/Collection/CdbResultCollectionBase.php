<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Vf92\BitrixUtils\OldOrm\Model\BitrixArrayItemBase;

abstract class CdbResultCollectionBase extends CollectionBase
{
    /**
     * @var \CDBResult
     */
    protected $cdbResult;

    /**
     * @var int Сколько всего элементов выбрано, если мы получили только одну страницу.
     */
    protected $totalCount = 0;

    public function __construct(\CDBResult $result)
    {
        $this->cdbResult = $result;
        $this->collection = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        $this->initialize();
        return $this->totalCount;
    }

    /**
     * @return \CDBResult
     */
    public function getCdbResult()
    {
        return $this->cdbResult;
    }

    protected function doInitialize()
    {
        if (true === $this->getCdbResult()->bFromArray && \is_array($this->getCdbResult()->arResult)) {
            /**
             * @todo Зачем тут инициилизация из BitrixItemBase
             * @todo Лучше залогировать и удалить
             */
            $result = (array)$this->getCdbResult()->arResult;

            foreach ($result as $key => $value) {
                if ($value instanceof BitrixArrayItemBase) {
                    $this->collection->set($value->getId(), $value);
                } elseif (\is_array($value) && array_key_exists('ID', $value)) {
                    $this->collection->set($value['ID'], $value);
                } else {
                    $this->collection->set($key, $value);
                }
            }
            $this->totalCount = $this->collection->count();
        } else {
            parent::doInitialize();
            $this->totalCount = (int)$this->cdbResult->AffectedRowsCount();
        }
    }
}
