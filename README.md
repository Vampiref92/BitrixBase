# BitrixBase
Расширенный набор функций для Битиркса

**папке additionalFiles**
В папке содержатся доп. файлы которые могут понадобиться на проекте - это базовый композер, gitignore для битрикса и cs_fixer

# Конструктор
**Конструктор datamanager**

`$dataManager = \Vf92\Constructor\EntityConstructor::compileEntityDataClass('Form', 'b_form');`

дальше работаем как обычно с объектом
`(int)$dataManager::query()->setSelect(['ID'])->setFilter(['SID' => $code])->exec()->fetch()['ID'];` 

**Упрощенный конструктор для свойств инфоблок в отдельной таблице**

`\Vf92\Constructor\IblockPropEntityConstructor::getDataClass($iblockId);`
`\Vf92\Constructor\IblockPropMultipleEntityConstructor::getDataClass($iblockId);`

# Декораторы
**FullHrefDecorator - позволяет получить абсолютный путь сайта по относительнмоу**
`(new FullHrefDecorator($path))->getFullPublicPath()`

# Helpers
**ClassFinderHelper**
Получение списка классов

**DateHelper**
Хелпер для работы с датами

**PhoneHelper**
Обработка и нормализация телефонов

**TaggedCacheHelper**
Класс для упрощенной работы с тегирвоанным кешем

**WordHelper**
Класс для работы со словами - например окончания

# Form
**FormHelper**

# HLBlock
**HLBlockHelper**
получение информации о highload блкое - например id по названию таблицы

**HLBlockFactory**
создание объекта dataManager

# Iblock
**IblockHelper**
Хелпер для инфоблока

# Log
**LoggerFactory**
получение логгера
`$logger = \Vf92\Log\LoggerFactory::create('название лога');`

**LazyLoggerAwareTrait**
можно в классе подключить трейт 
`use \Vf92\Log\LazyLoggerAwareTrait`
тогда будут доступны методы получения логгера
`$this->withLogName('name');
$this->log() // это логгер`

# Mysql
**MysqlBatchOperations**
Массовые операции над таблицами с поддержкой условий

**ExtendsBitrixQuery**
Получение сформированных запросов(селекта,фильтра)

#User
**UserGroupHelper**
хелпер для получения данных из групп пользователя

**UserHelper**
Хелпер для получения данных пользователя

# Другие возможности
**BitrixUtils**
Нераспределенные функции:
-isAjax - битровая проверка на аякс
-bool2BitrixBool - преобразование из буля в битровый буль
-bitrixBool2bool - преобразование из битрового буля в буль

**MiscUtils**
Нераспределенные функции:
-getClassName - получение имени класса

# Component
**BaseBitrixComponent**
Базовый класс для упрощения создания компонентов и их унификации