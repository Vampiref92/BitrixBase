<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Vf92\BitrixUtils\OldOrm\Model\CatalogGroup;
use Generator;

class CatalogGroupCollection extends CdbResultCollectionBase
{
    /**
     * @return Generator CatalogGroup[]
     */
    protected function fetchElement(): Generator
    {
        while ($fields = $this->getCdbResult()->GetNext()) {
            yield new CatalogGroup($fields);
        }
    }

}
