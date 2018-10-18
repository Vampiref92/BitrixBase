<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Vf92\BitrixUtils\OldOrm\Model\IblockElement;
use Generator;

class IblockElementCollection extends CdbResultCollectionBase
{
    /**
     * @param $id
     *
     * @return null|IblockElement
     */
    public function getById($id)
    {
        return $this->filter(function (IblockElement $element) use ($id) {
            return $element->getId() == $id;
        })->first();
    }

    /**
     * @inheritdoc
     */
    protected function fetchElement()
    {
        while ($fields = $this->getCdbResult()->GetNext()) {
            yield new IblockElement($fields);
        }
    }
}
