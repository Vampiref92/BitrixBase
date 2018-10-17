<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Vf92\BitrixUtils\OldOrm\Model\IblockElement;
use Generator;

class IblockElementCollection extends CdbResultCollectionBase
{
    /**
     * @inheritdoc
     */
    protected function fetchElement(): Generator
    {
        while ($fields = $this->getCdbResult()->GetNext()) {
            yield new IblockElement($fields);
        }
    }

    /**
     * @param $id
     *
     * @return null|IblockElement
     */
    public function getById($id) {
        return $this->filter(function (IblockElement $element) use ($id) {
            return $element->getId() == $id;
        })->first();
    }
}
