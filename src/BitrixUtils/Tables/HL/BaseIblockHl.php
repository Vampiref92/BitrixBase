<?php

namespace Vf92\BitrixUtils\Tables\HL;

use Bitrix\Main;

/**
 * Class BaseIblockHl
 * @package Vf92\BitrixUtils\Tables\HL
 */
abstract class BaseIblockHl extends BaseHl
{
    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws Main\SystemException
     */
    public static function getMap()
    {
        return [
            'ID'                  => new Main\Entity\IntegerField('ID', [
                'primary'      => true,
                'autocomplete' => true,
            ]),
            'UF_NAME'             => new Main\Entity\StringField('UF_NAME', ['required' => true]),
            'UF_SORT'             => new Main\Entity\IntegerField('UF_SORT', ['default_value' => 500]),
            'UF_XML_ID'           => new Main\Entity\StringField('UF_XML_ID'),
            'UF_DEF'              => new Main\Entity\BooleanField('UF_DEF', [
                'values'        => [0, 1],
                'default_value' => 0,
            ]),
            'UF_DESCRIPTION'      => new Main\Entity\StringField('UF_DESCRIPTION'),
            'UF_FULL_DESCRIPTION' => new Main\Entity\StringField('UF_FULL_DESCRIPTION'),
        ];
    }
}