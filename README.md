# BitrixBase
Расширенный набор функций для Битиркса

**папке additionalFiles**
В папке содержатся доп. файлы которые могут понадобиться на проекте - это базовый композер, gitignore для битрикса и cs_fixer

# Конструктор
**Конструктор datamanager**

`$dataManager = \Vf92\Constructor\EntityConstructor::compileEntityDataClass('Form', 'b_form');`

дальше работаем как обычно с объектом
`(int)$dataManager::query()->setSelect(['ID'])->setFilter(['SID' => $code])->exec()->fetch()['ID'];` 

**Урощенный конструктор для свойств инфоблок в отдельной таблице**

`\Vf92\Constructor\IblockPropEntityConstructor::getDataClass($iblockId);`
`\Vf92\Constructor\IblockPropMultipleEntityConstructor::getDataClass($iblockId);`

# Декораторы
**FullHrefDecorator - позволяет получить абсолютный путь сайта по относительнмоу**
`(new FullHrefDecorator($path))->getFullPublicPath()`

# Helpers
**ClassFinderHelper**

**DateHelper**

**FormHelper**

**HighloadHelper**

**PhoneHelper**

**TaggedCacheHelper**

**WordHelper**

# HLBlockFactory

# Iblock

# Log

# Main

# Mysql

# Другие возможности
**BitrixUtils**

**MiscUtils**

**Vf92BitrixComponent**