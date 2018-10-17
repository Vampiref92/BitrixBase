<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

interface ItemInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     *
     * @return static
     */
    public function withId(int $id);

    /**
     * @return string
     */
    public function getXmlId(): string;

    /**
     * @param string $xmlId
     *
     * @return static
     */
    public function withXmlId(string $xmlId);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return static
     */
    public function withName(string $name);

    /**
     * @return int
     */
    public function getSort(): int;

    /**
     * @param int $sort
     *
     * @return static
     */
    public function withSort(int $sort);
}
