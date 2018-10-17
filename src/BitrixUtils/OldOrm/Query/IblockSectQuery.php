<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use Vf92\BitrixUtils\OldOrm\Collection\CollectionBase;
use Vf92\BitrixUtils\OldOrm\Collection\IblockSectCollection;

/**
 * Class IblockElementQuery
 *
 * @package Vf92\BitrixUtils\OldOrm\Query
 */
class IblockSectQuery extends IblockSectionQuery
{
    /**
     * @var int
     */
    protected $iblockId;
    
    public function __construct($iblockId = null)
    {
        parent::__construct();
        
        $this->iblockId = $iblockId;
    }
    
    /**
     * Исполняет запрос и возвращает коллекцию сущностей. Например, элементов инфоблока.
     *
     * @return IblockSectCollection
     */
    public function exec() : CollectionBase
    {
        return new IblockSectCollection($this->doExec());
    }
    
    /**
     * Возвращает базовый фильтр - та его часть, которую нельзя изменить. Например, ID инфоблока.
     *
     * @return array
     */
    public function getBaseFilter() : array
    {
        $filter = [
            'GLOBAL_ACTIVE' => 'Y',
        ];
    
        if ($this->iblockId) {
            $filter['IBLOCK_ID'] = $this->iblockId;
        }
    
        return $filter;
    }
    
    /**
     * Возвращает базовую выборку полей. Например, те поля, которые обязательно нужны для создания сущности.
     *
     * @return array
     */
    public function getBaseSelect() : array
    {
        return [
            'ID',
            'ACTIVE',
            'IBLOCK_ID',
            'IBLOCK_SECTION_ID',
            'NAME',
            'XML_ID',
            'CODE',
            'SORT',
            'PICTURE',
            'DESCRIPTION',
            'DESCRIPTION_TYPE',
            'DEPTH_LEVEL',
            'SECTION_PAGE_URL',
            'DETAIL_PICTURE',
        ];
    }
}
