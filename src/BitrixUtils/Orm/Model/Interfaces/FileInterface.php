<?php

namespace Vf92\BitrixUtils\Orm\Model\Interfaces;

/**
 * Interface FileInterface
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
interface FileInterface extends ActiveReadModelInterface
{
    /**
     * @return int
     */
    public function getId();
    
    /**
     * @return string
     */
    public function getSrc();
    
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @return string
     */
    public function getSubDir();
}
