<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Interfaces;

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
     * @param int $id
     */
    public function setId($id);
    
    /**
     * @return string
     */
    public function getSrc();
    
    /**
     * @param string $src
     */
    public function setSrc($src);
    
    /**
     * @return string
     */
    public function __toString();
}
