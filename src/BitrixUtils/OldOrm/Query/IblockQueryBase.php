<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

abstract class IblockQueryBase extends QueryBase
{
    public function __construct()
    {
        /**
         * По умолчанию следует выбирать активные и доступные элементы.
         * При необходимости для конкретного Query можно просто вызвать withFilter([]), чтобы выбрать всё.
         */
        $this->withFilter(static::getActiveAccessableElementsFilter());
    }

    /**
     * Возвращает фильтр активных и доступных элементов инфоблока.
     *
     * Это базовая основа и в публичной части всегда рекомендуется использовать такой фильтр, чтобы можно было всегда
     * управлять доступами, а также флажком и датами активности.
     *
     * @return array
     */
    public static function getActiveAccessableElementsFilter(): array
    {
        return [
            'CHECK_PERMISSIONS' => 'Y',
            'ACTIVE'            => 'Y',
            'ACTIVE_DATE'       => 'Y',
        ];
    }
}
