<?php


namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use mysql_xdevapi\Result;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Iblock\Exception\IblockNotFoundException;

/**
 * Class ElementHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class ElementHelper
{
    /**
     * @param        $iblockId
     * @param string $code
     *
     * @return int|null
     */
    public static function getIdByCode($iblockId, $code)
    {
        //SetFilter т.к. минимальная версия 16.5
        $id = 0;
        try {
            $query = ElementTable::query()->setSelect(['ID']);
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $query->where('CODE', $code)
                    ->where('IBLOCK_ID', $iblockId);
            } else {
                $query->setFilter(['=CODE' => $code, '=IBLOCK_ID' => $iblockId]);
            }
            $id = (int)$query->exec()->fetch()['ID'];
        } catch (ObjectPropertyException $e) {
            return null;
        } catch (ArgumentException $e) {
            return null;
        } catch (SystemException $e) {
            return null;
        }
        return $id > 0 ? $id : null;
    }
    public static function copy($elementId,$sectionId = false,$iblockId = false){
        $result = new Result();
        if((int)$iblockId>0){
            $queryResult = IblockTable::query()
                ->setSelect(['ID'])
                ->where('ID',$iblockId)
                ->exec();
            if(!$queryResult->fetch()){
                throw new IblockNotFoundException();
            }
        }
        elseif ($iblockId!==false){
            throw new ArgumentException('Идентификатор инфоблока не является числом, большим 0','iblockId');
        }
        return $result;
    }
}