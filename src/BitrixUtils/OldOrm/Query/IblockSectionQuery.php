<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use CDBResult;
use CIBlockSection;

/**
 * Class IblockSectionQuery
 *
 * @package Vf92\BitrixUtils\OldOrm\Query
 */
abstract class IblockSectionQuery extends IblockQueryBase
{
    protected $countElements = false;
    
    /**
     * @inheritdoc
     */
    public function doExec() : CDBResult
    {
        return CIBlockSection::GetList($this->getOrder(),
                                       $this->getFilterWithBase(),
                                       $this->isCountElements(),
                                       $this->getSelectWithBase(),
                                       $this->getNav() ?: false);
    }
    
    /**
     * @return bool
     */
    public function isCountElements() : bool
    {
        return $this->countElements;
    }
    
    /**
     * @param bool $countElements
     *
     * @return $this
     */
    public function withCountElements(bool $countElements) : IblockQueryBase
    {
        $this->countElements = $countElements;
        
        return $this;
    }
    
}
