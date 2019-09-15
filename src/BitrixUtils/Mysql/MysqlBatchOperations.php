<?php

namespace Vf92\BitrixUtils\Mysql;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Db\SqlQueryException;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\SystemException;
use function count;

/**
 * Class MysqlBatchOperations
 * @package Vf92\BitrixUtils\Mysql
 */
class MysqlBatchOperations
{
    /** @var ExtendsBitrixQuery */
    private $query;
    /**
     * @var bool
     */
    private $lowPriority = false;
    /**
     * @var bool
     */
    private $quick = false;
    /**
     * @var bool
     */
    private $delayed = false;
    /**
     * @var bool
     */
    private $ignore = false;
    /**
     * @var string
     */
    private $table = '';
    /**
     * @var int
     */
    private $limit = 500;
    /**
     * @var int
     */
    private $step = 0;

    /**
     * Устанавливаем сформирвоанный объект с запросом
     * MysqlBatchOperations constructor.
     *
     * @param Query  $query
     * @param string $table
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function __construct(Query $query = null, string $table = '')
    {
        if ($query instanceof Query) {
            $this->setQuery($query);
        }
        if (!empty($table)) {
            $this->setTable($table);
        }
    }

    /**
     * @param Query|null $query
     * @param string     $table
     *
     * @return static
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getInstance(Query $query = null, string $table = ''): MysqlBatchOperations
    {
        return new static($query, $table);
    }

    /**
     * Делаем массовое обновление данных по условию
     *
     * @param array $fields
     *
     * @throws SqlQueryException
     */
    public function batchUpdate(array $fields): void
    {
        /** @todo обновление из нескольких таблиц */
        /** @todo транзакции */
        if (!empty($fields) && $this->hasTable()) {
            $updates = [];
            $connection = Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();
            foreach ($fields as $column => $val) {
                if (!empty($column) && !empty($val)) {
                    $updates[] = $sqlHelper->quote($column) . ' = ' . $val;
                }
            }
            $queryString = 'UPDATE' . $this->getLowPriority() . $this->getIgnore() . ' ' . $sqlHelper->quote($this->getTable())
                . ' SET ' . implode(', ', $updates)
                . $this->getWhere() . $this->getOrder() . $this->getLimitString();
            $connection->queryExecute($queryString);
        }
    }

    /**
     * Делаем массовое удаление по условию
     * @throws SqlQueryException
     */
    public function batchDelete(): void
    {
        /** @todo удаление из нескольких таблиц */
        /** @todo использование USING */
        if ($this->hasTable()) {
            $connection = Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();
            $queryString = 'DELETE' . $this->getLowPriority() . $this->getQuick() . ' FROM ' . $sqlHelper->quote($this->getTable())
                . $this->getWhere() . $this->getOrder() . $this->getLimitString();
            $connection->queryExecute($queryString);
        }
    }

    /**
     * Делаем массовую вставку
     *
     * @param array $fields
     *
     * @throws SqlQueryException
     */
    public function batchInsert(array $fields): void
    {
        /** @todo транзакции */
        /** @todo вставка по подзапросу в том числе с лимитом */
        /** @todo использование ON DUPLICATE KEY UPDATE */
        if ($this->hasTable()) {
            $fields = $this->getPart($fields);
            if (!empty($fields)) {
                $values = [];
                $columns = [];
                foreach ($fields as $item) {
                    $values[] = '(' . implode(', ', array_values($item)) . ')';
                    /** @noinspection SlowArrayOperationsInLoopInspection */
                    $columns = array_merge($columns, array_keys($item));
                }
                array_unique($columns);
                $connection = Application::getConnection();
                $sqlHelper = $connection->getSqlHelper();
                foreach ($columns as &$column) {
                    $column = $sqlHelper->quote($column);
                }
                unset($column);
                $queryString = 'INSERT' . $this->getDelayed() . ($this->isDelayed() ? '' : $this->getLowPriority()) . $this->getIgnore()
                    . ' INTO ' . $sqlHelper->quote($this->getTable()) . '(' . implode(', ', $columns) . ')'
                    . ' VALUES ' . implode(', ', $values);
                $connection->queryExecute($queryString);
            }
        }
    }

    /**
     * Получение части массива по лимтам
     *
     * @param array $items
     *
     * @return array
     */
    public function getPart(array $items): array
    {
        $limit = $this->getLimit();
        if ($limit > 0 && count($items) > $limit) {
            $chunkItems = array_chunk($items, $limit);
            $countChunkItems = count($chunkItems);
            $step = $this->getStep();
            if ($countChunkItems > $step) {
                $items = $chunkItems[$step];
                $this->increaseStep();
            } else {
                $items = [];
                $this->clearStep();
            }
        }
        return $items;
    }

    /**
     * Получаем ограничение в limit
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Устанавливаем ограничение в limit
     *
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Проверяем LOW_PRIORITY
     * @return bool
     */
    public function isLowPriority(): bool
    {
        return $this->lowPriority;
    }

    /**
     * Устанавливаем LOW_PRIORITY
     *
     * @param bool $lowPriority
     */
    public function setLowPriority(bool $lowPriority): void
    {
        $this->lowPriority = $lowPriority;
    }

    /**
     * Првоеряем QUICK
     * @return bool
     */
    public function isQuick(): bool
    {
        return $this->quick;
    }

    /**
     * Устанавливаем QUICK
     *
     * @param bool $quick
     */
    public function setQuick(bool $quick): void
    {
        $this->quick = $quick;
    }

    /**
     * Получаем имя таблицы
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Устанавливаем имя таблицы
     *
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * Проверка установки DELAYED
     * @return bool
     */
    public function isDelayed(): bool
    {
        return $this->delayed;
    }

    /**
     * Установка DELAYED
     *
     * @param bool $delayed
     */
    public function setDelayed(bool $delayed): void
    {
        $this->delayed = $delayed;
    }

    /**
     * Проверка установки IGNORE
     * @return bool
     */
    public function isIgnore(): bool
    {
        return $this->ignore;
    }

    /**
     * Получение строки IGNORE
     * @return string
     */
    public function getIgnore(): string
    {
        return $this->isIgnore() ? ' IGNORE' : '';
    }

    /**
     * Установка IGNORE
     *
     * @param bool $ignore
     */
    public function setIgnore(bool $ignore): void
    {
        $this->ignore = $ignore;
    }

    /**
     * Получение установленного объекта Query
     * @return ExtendsBitrixQuery
     */
    public function getQuery(): ExtendsBitrixQuery
    {
        return $this->query;
    }

    /**
     * Установка объекта Query
     *
     * @param Query $query
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function setQuery(Query $query): void
    {
        $this->query = new ExtendsBitrixQuery($query);
        $this->setTable($this->query->quoteTableSource($this->query->getEntity()->getDBTableName()));
    }

    /**
     * Получение строки LOW_PRIORITY
     * @return string
     */
    private function getLowPriority(): string
    {
        return $this->isLowPriority() ? ' LOW_PRIORITY' : '';
    }

    /**
     * получение строки QUICK
     * @return string
     */
    private function getQuick(): string
    {
        return $this->isQuick() ? ' QUICK' : '';
    }

    /**
     * Првоерка существования таблицы
     * @return bool
     */
    private function hasTable(): bool
    {
        return !empty($this->getTable());
    }

    /**
     * Получение строки DELAYED
     * @return string
     */
    private function getDelayed(): string
    {
        return $this->isDelayed() ? ' DELAYED' : '';
    }

    /**
     * Получение шага
     * @return int
     */
    private function getStep(): int
    {
        return $this->step;
    }

    /**
     * Установка шага
     *
     * @param int $step
     */
    private function setStep(int $step): void
    {
        $this->step = $step;
    }

    /** Наращивание шага */
    private function increaseStep(): void
    {
        $this->setStep($this->getStep() + 1);
    }

    /** Очистка шага */
    private function clearStep(): void
    {
        $this->setStep(0);
    }

    /**
     * Получение строки LIMIT
     * @return string
     */
    private function getLimitString(): string
    {
        if ($this->query instanceof ExtendsBitrixQuery) {
            return $this->query->getLimit() > 0 ? ' LIMIT ' . $this->query->getLimit() : '';
        }

        return '';
    }

    /**
     * Получение строки WHERE
     * @return string
     */
    private function getWhere(): string
    {
        return $this->query->getBuildWhere();
    }

    /**
     * Получение стркои ORDER
     * @return string
     */
    private function getOrder(): string
    {
        return $this->query->getBuildOrder();
    }
}
