<?php
namespace Vf92\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

/**
 * Class HighloadHelper
 *
 * @package Vf92\Helpers
 */
class HighloadHelper
{
    /**
     * @param string $name
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @return int
     */
    public static function getIdByName($name)
    {
        $params = [
            'select' => ['ID'],
            'filter' => ['NAME' => $name],
            'cache'  => ['ttl' => 360000],
        ];
        
        return (int)static::getHighloadTableRes($params)->fetch()['ID'];
    }
    
    /**
     * @param array $params
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @return Result
     */
    public static function getHighloadTableRes(array $params)
    {
        Loader::includeModule('highloadblock');
        
        return HighloadBlockTable::getList($params);
    }
    
    /**
     * @param string $name
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @return int
     */
    public static function getIdByTableName($name)
    {
        $params = [
            'select' => ['ID'],
            'filter' => ['TABLE_NAME' => $name],
            'cache'  => ['ttl' => 360000],
        ];
        
        return (int)static::getHighloadTableRes($params)->fetch()['ID'];
    }
}
