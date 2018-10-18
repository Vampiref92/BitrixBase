<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

interface ItemInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return static
     */
    public function withId($id);

    /**
     * @return string
     */
    public function getXmlId();

    /**
     * @param string $xmlId
     *
     * @return static
     */
    public function withXmlId($xmlId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return static
     */
    public function withName($name);

    /**
     * @return int
     */
    public function getSort();

    /**
     * @param int $sort
     *
     * @return static
     */
    public function withSort($sort);
}
