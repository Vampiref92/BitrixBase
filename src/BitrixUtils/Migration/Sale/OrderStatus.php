<?php

namespace Vf92\BitrixUtils\Migration\Sale;

use Vf92\BitrixUtils\Exception\MigrationFailureException;
use CSaleStatus;

class OrderStatus
{
    /**
     * @var CSaleStatus
     */
    private $CSaleStatus;

    /**
     * @param $id
     * @param array $fields Массив вида
     *
     *  [
     *      'ID' => 'X',
     *      'SORT' => 145,
     *      'NOTIFY' => 'Y|N', //Уведомлять ли покупателя о переходе в этот статус
     *      'LANG' => [
     *          [
     *              'LID' => 'ru',
     *              'NAME' => 'Частично продан',
     *              'DESCRIPTION' => ''
     *          ]
     *      ],
     *      'PERMS' => [
     *          //массив ассоциативных массивов прав на доступ к изменению заказа в данном статусе
     *          //с ключами GROUP_ID и PERM_TYPE
     *      ]
     *  ]
     *
     * @return string Буквенный код изменённого или добавленного статуса
     * @throws MigrationFailureException
     */
    public function setOrderStatus($id, array $fields)
    {

        //TODO Позже переделать на D7

        global $APPLICATION;

        $id = trim($id);

        if ($id == '') {
            throw new MigrationFailureException('Status id is empty');
        }

        $arStatusThisLang = $this->getStatusNameInLang(LANGUAGE_ID, $fields);

        if (!isset($arStatusThisLang['NAME']) || trim($arStatusThisLang['NAME']) == "") {
            throw new MigrationFailureException('Status name for current lang is not set.');
        }

        //TODO Предусмотреть, чтобы для случая обновления не было неявных действий из-за merge-подобного задания названий для других языков

        //Простановка названия для всех системных языков
        foreach ($this->getLangList() as $arLang) {
            //Если для этого языка не задано ничего, то скопировать данные из текущего языка
            $arCurLang = $this->getStatusNameInLang($arLang['LID'], $fields);
            if (is_array($arCurLang) && count($arCurLang) == 0) {
                $arCurLang = $arStatusThisLang;
                $arCurLang['LID'] = $arLang['LID'];
                $fields['LANG'][] = $arCurLang;
            }
        }

        $dbStatus = CSaleStatus::GetList([], ['ID' => $fields['ID']]);

        if ($arExStatus = $dbStatus->Fetch()) {
            $res = $this->CSaleStatus()->Update(
                $arExStatus['ID'],
                $fields
            );
            if ($res == false) {
                throw new MigrationFailureException($APPLICATION->GetException()->GetString());
            }
            $affectedID = $arExStatus['ID'];
        } else {
            $res = $this->CSaleStatus()->Add($fields);
            if ($res == false) {
                throw new MigrationFailureException($APPLICATION->GetException()->GetString());
            }
            $affectedID = $res;
        }

        //TODO Добавить генерацию типа и шаблона почтового события для статуса.
        /**
         * Битриксовый g-код в админке генерирует эти шаблоны, но молчит об этом. Надо его изучить и добавить сюда
         * либо копию, либо вызов нужных методов.
         */

        return $affectedID;
    }

    /**
     * Возвращает название статуса на указанном языке из массива в формате для CSaleStatus::Add или CSaleStatus::Update
     *
     * @param $lang
     * @param array $fields
     *
     * @return array
     * @throws MigrationFailureException
     */
    private function getStatusNameInLang($lang, array $fields)
    {
        $lang = trim($lang);

        if (!isset($fields['LANG']) || !is_array($fields['LANG'])) {
            throw new MigrationFailureException("Language dependent names are not set.");
        }

        foreach ($fields['LANG'] as $arLangItem) {
            if ($arLangItem['LID'] == $lang) {
                return $arLangItem;
            }
        }

        return [];
    }

    /**
     * Список всех системных языков
     * @return array
     *
     * TODO Вынести в более общий хелпер
     */
    protected function getLangList()
    {
        $arLangList = [];

        $dbLangList = \CLanguage::GetList(
            $by = 'LID',
            $order = 'ASC'
        );
        while ($arLang = $dbLangList->Fetch()) {
            $arLangList[$arLang['LID']] = $arLang;
        }

        return $arLangList;
    }

    /**
     * @return CSaleStatus
     */
    private function CSaleStatus()
    {
        if (is_null($this->CSaleStatus)) {
            $this->CSaleStatus = new CSaleStatus();
        }

        return $this->CSaleStatus;
    }
}
