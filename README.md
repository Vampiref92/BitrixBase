# BitrixBase
Расширенный набор функций для Битиркса

**папке additionalFiles**
В папке содержатся доп. файлы которые могут понадобиться на проекте - это базовый композер, gitignore для битрикса и cs_fixer

## Конструктор
### Базовый конструктор()EntityConstructor)
```php
$dataManager = \Vf92\Constructor\EntityConstructor::compileEntityDataClass('Form', 'b_form');
//дальше работаем как обычно с объектом
$id = (int)$dataManager::query()->setSelect(['ID'])->setFilter(['SID' => $code])->exec()->fetch()['ID'];
```

### Упрощенный конструктор для свойств инфоблок в отдельной таблице(IblockPropEntityConstructor и IblockPropMultipleEntityConstructor)
```php
$dataManager = \Vf92\Constructor\IblockPropEntityConstructor::getDataClass($iblockId);
$dataManager = \Vf92\Constructor\IblockPropMultipleEntityConstructor::getDataClass($iblockId);
```

## Декораторы
### FullHrefDecorator 
позволяет получить абсолютный путь сайта по относительному
```php
$fullPath = (new \Vf92\Decorators\FullHrefDecorator($path))->getFullPublicPath();
```

## Helpers
### ClassFinderHelper
Получение списка классов

### DateHelper
Хелпер для работы с датами

### PhoneHelper
Обработка и нормализация телефонов

### TaggedCacheHelper
Класс для упрощенной работы с тегирвоанным кешем

### WordHelper
Класс для работы со словами - например окончания

## Form
### FormHelper

## HLBlock
### HLBlockHelper
получение информации о highload блкое - например id по названию таблицы

### HLBlockFactory
создание объекта dataManager

## Iblock
### IblockHelper
Хелпер для инфоблока

## Log
### LoggerFactory
получение логгера
```php
$logger = \Vf92\Log\LoggerFactory::create('название лога');
```

### LazyLoggerAwareTrait
можно в классе подключить трейт 
```php
use \Vf92\Log\LazyLoggerAwareTrait
```
тогда будут доступны методы получения логгера
```php
$this->withLogName('name');
$this->log() // это логгер
```

### Использование логгера
```php
$logger = LoggerFactory::create('fullHrefDecorator');
$logger->critical('Системная ошибка при получении пукбличного пути ' . $e->getTraceAsString());
```

## Mysql
### MysqlBatchOperations
Массовые операции над таблицами с поддержкой условий
- batchUpdate - Делаем массовое обновление данных по условию
- batchDelete - Делаем массовое удаление по условию
- batchInsert - Делаем массовую вставку
- getPart - Получение части массива по лимтам
- getLimit - Получаем ограничение в limit
- setLimit - Устанавливаем ограничение в limit
- getTable - Получаем имя таблицы
- setTable - Устанавливаем имя таблицы
- getQuery - Получение установленного объекта Query
- setQuery - Установка объекта Query

### ExtendsBitrixQuery
Получение сформированных запросов(селекта,фильтра)
- getBuildWhere - Получаем сформированное условие по запросу(where)
- getBuildOrder - Получаем сформированную сортировку(order)

## User
### UserGroupHelper
хелпер для получения данных из групп пользователя
- getGroupIdByCode - Возвращает id группы пользователей по её коду

### UserHelper
Хелпер для получения данных пользователя
- isInGroup - Проверяет вхождение пользователя в группу
- getLoginByHash - Возвращает логин пользователя по хешу его запомненной авторизации

## Другие возможности
### BitrixUtils
Нераспределенные функции:
- isAjax - битровая проверка на аякс
- bool2BitrixBool - преобразование из буля в битровый буль
- bitrixBool2bool - преобразование из битрового буля в буль

### MiscUtils
Нераспределенные функции:
- getClassName - получение имени класса без namespace

## Component
### BaseBitrixComponent
Базовый класс для упрощения создания компонентов и их унификации
Особенности:
- Включен логер
- Можно задать ключи кеширования
- Можно переопределить вызываемый шаблон через метод
- Изначально включен кеш
- Все необходимые действия делать в этом классе prepareResult, если логика сложнее, то переопределяем execute