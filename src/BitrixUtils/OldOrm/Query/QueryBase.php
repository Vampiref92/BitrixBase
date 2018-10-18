<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use Bitrix\Main\DB\Result;
use CDBResult;
use Vf92\BitrixUtils\OldOrm\Collection\CollectionBase;

abstract class QueryBase
{
    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * @var array
     */
    protected $group = [];

    /**
     * @var array
     */
    protected $order = [];

    /**
     * @var array
     */
    protected $nav = [];

    /**
     * @return array
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param array $select
     *
     * @return $this
     */
    public function withSelect(array $select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param array $filter
     *
     * @return $this
     */
    public function withFilter(array $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return $this
     */
    public function withFilterParameter($name, $value)
    {
        $name = trim($name);

        if ($name !== '') {
            $this->filter[$name] = $value;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function withoutFilterParameter($name)
    {
        $name = trim($name);

        if (isset($this->filter[$name])) {
            unset($this->filter[$name]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param array $group
     *
     * @return $this
     */
    public function withGroup(array $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param array $order
     *
     * @return $this
     */
    public function withOrder(array $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return array
     */
    public function getNav()
    {
        return $this->nav;
    }

    /**
     * @param array $nav
     *
     * @return $this
     */
    public function withNav(array $nav)
    {
        $this->nav = $nav;

        return $this;
    }

    /**
     * Исполняет запрос и возвращает коллекцию сущностей. Например, элементов инфоблока.
     *
     * @return CollectionBase
     */
    abstract public function exec();

    /**
     * Непосредственное выполнение запроса через API Битрикса
     *
     * @return mixed|CDBResult|Result
     */
    abstract public function doExec();

    /**
     * Возвращает базовый фильтр - та его часть, которую нельзя изменить. Например, ID инфоблока.
     *
     * @return array
     */
    abstract public function getBaseFilter();

    /**
     * Возвращает базовую выборку полей. Например, те поля, которые обязательно нужны для создания сущности.
     *
     * @return array
     */
    abstract public function getBaseSelect();

    public function getFilterWithBase()
    {
        return array_merge($this->getFilter(), $this->getBaseFilter());
    }

    /**
     * @return array
     */
    public function getSelectWithBase()
    {
        return array_unique(array_merge($this->getSelect(), $this->getBaseSelect()));
    }
}
