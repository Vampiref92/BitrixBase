<?php

namespace Vf92\BitrixUtils\OldOrm\Model;

use Vf92\BitrixUtils\OldOrm\Model\Interfaces\SizeImageInterface;

/**
 * Class SizeFileDecorator
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class SizeImageDecorator extends Image implements SizeImageInterface
{
    /**
     * @return string
     */
    public function getSrc() : string
    {
        return sprintf('/size%s', parent::getSrc());
    }
}
