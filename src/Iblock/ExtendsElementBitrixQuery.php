<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.09.18
 * Time: 0:18
 */

namespace Vf92\Iblock;

use Bitrix\Main\Entity\Query;

class ExtendsElementBitrixQuery extends Query
{
    public function exec()
    {
        $select = $this->getSelect();
        $filter = $this->getFilter();
        parent::exec();
    }
}