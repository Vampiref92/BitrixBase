# минимальная версия 18.0.4, без нее не будет работать ряд функций
# BitrixBase
Расширенный набор функций для Битиркса

## Log - Логгер
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

## BitrixUtils
### Component - Компонент
#### BaseBitrixComponent
Базовый класс для упрощения создания компонентов и их унификации
Особенности:
- Включен логер
- Можно задать ключи кеширования
- Можно переопределить вызываемый шаблон через метод
- Изначально включен кеш
- Все необходимые действия делать в этом классе prepareResult, если логика сложнее, то переопределяем execute

### Constructor - Конструктор
Делает возможным работы с dataManager если сущность не описана
#### Базовый конструктор(EntityConstructor)
```php
$dataManager = \Vf92\Constructor\EntityConstructor::compileEntityDataClass('Form', 'b_form');
//дальше работаем как обычно с объектом
$id = (int)$dataManager::query()->setSelect(['ID'])->setFilter(['SID' => $code])->exec()->fetch()['ID'];
```

#### Упрощенный конструктор для свойств инфоблок в отдельной таблице(IblockPropEntityConstructor и IblockPropMultipleEntityConstructor)
```php
$dataManager = \Vf92\Constructor\IblockPropEntityConstructor::getDataClass($iblockId);
$dataManager = \Vf92\Constructor\IblockPropMultipleEntityConstructor::getDataClass($iblockId);
//дальше работаем как обычно с объектом
$id = (int)$dataManager::query()->setSelect(['ID'])->setFilter(['CODE' => $code])->exec()->fetch()['ID'];
```

### User - Пользователь и группа пользователя
#### UserGroupHelper
хелпер для получения данных из групп пользователя
- getGroupIdByCode - Возвращает id группы пользователей по её коду

#### UserHelper
Хелпер для получения данных пользователя
- isInGroup - Проверяет вхождение пользователя в группу
- getLoginByHash - Возвращает логин пользователя по хешу его запомненной авторизации

### Iblock - Инфоблоки
#### IblockHelper
Хелпер для инфоблока
- getIblockId - Возвращает id инфоблока по его типу и символьному коду
- getIblockXmlId - Возвращает xml id инфоблока по его типу и символьному коду
- getPropertyId - Возвращает id свойства инфоблока по символьному коду
- isIblockTypeExists - Проверка существования типа инфоблоков

### HLBlock - Хайлоад блоки
#### HLBlockHelper
получение информации о highload блкое - например id по названию таблицы
- getIdByName - Получение ID Хайлоад блока по имени
- getIdByTableName - Получение ID Хайлоад блока по таблице

#### HLBlockFactory
создание объекта dataManager
- createTableObject - Возвращает скомпилированную сущность HL-блока по имени его сущности.
- createTableObjectByTable - Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.

### Form - Форма
#### FormHelper
- getIdByCode - Получение ID формы по коду
- checkRequiredFields - Проверка обязательных полей формы
- validEmail - Валидация email
- addResult - Добавление результат(заполенние формы)
- saveFile - Сохранение файла
- addForm - Добавление формы
- addStatuses - Добавление статусов
- addQuestions - Добавление вопросов
- addAnswers - Добавление ответов
- addMailTemplate - Генерация почтового шаблона
- deleteForm - Удаление формы
- getRealNamesFields - Получить реальные названия полей формы
- getQuestions - Получение вопросов

### Decorators - Декораторы
#### FullHrefDecorator 
позволяет получить абсолютный путь сайта по относительному
```php
$fullPath = (new \Vf92\Decorators\FullHrefDecorator($path))->getFullPublicPath();
```

### Config - работа с конфигурационными файлами
#### Dbconn - работа с dbconn
- get - получение данных в виде массива
- save - сохранение данных из массива в файл

### Helpers - Хелперы
#### DateHelper - наследуеся от misc datehelper
- convertToDateTime - Преобразование битриксового объекта даты в Php
- formatDate - Враппер для FormatDate. Доп. возможности
    - ll - отображение для недели в винительном падеже (в пятницу, в субботу)
    - XX - 'Сегодня', 'Завтра'
- getFormattedActiveDate - Формирвоание периода дат
    - "с 01 по 31 сентября"
    - "с 01 июня по 31 сентября" 
    - "с 01 июня 2017 по 31 сентября 2018" 
    - "с 01 июня по 31 сентября 2018" 
    - "с 01 июня" 
    - "с 01 июня 2018" 
    - "до 01 июня 2018" 
    - "до 01 июня" 
    
#### MenuHelper
- getMultiLvlArray - получает многомерный массив меню из одномерного
- countMultiArray - считает количество элементов текущих + вложенных

#### TaggedCacheHelper
Класс для упрощенной работы с тегирвоанным кешем
есть 2 режима работы как static так и dinamic(через объект)
- addManagedCacheTags - Добавление тегов массивом
- clearManagedCache - Очистка кеша по тегам
- addManagedCacheTag - Добавление одного тега
- getTagCacheInstance - Получение объекта тегированного кеша
- start - Начинаем тегирвоанный кеш
- end - Завершаем тегирвоанный кеш
- addTags - Добавляем теги
- addTag - Добавляем тег
- abortTagCache - прерываем тегированный кеш(abort)

### Mysql - Дополнительные возможности для запросов к Mysql через объект dataQuery
#### MysqlBatchOperations
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

#### ExtendsBitrixQuery
Получение сформированных запросов(селекта,фильтра)
- getBuildWhere - Получаем сформированное условие по запросу(where)
- getBuildOrder - Получаем сформированную сортировку(order)

### Migration
#### SprintMigrationBase - Базовый класс помошника миграции
- getHelper - получение хелпера
- log - получение логгера
Содержит дополнительные хелперы

### BitrixUtils - Другие возможности
Нераспределенные функции:
- isAjax - битровая проверка на аякс
- bool2BitrixBool - преобразование из буля в битровый буль
- bitrixBool2bool - преобразование из битрового буля в буль
- extractErrorMessage - получение ошибок из объекта результата

## MiscUtils
### EvType - определение типа проекта(dev,prod)
- getServerType - получение типа сервера
- isProd - это prod(bool)
- isDev - это dev(bool)
- isStage - это stage(bool)

### MiscUtils - Другие возможности
- getClassName - получение имени класса без namespace
- trimArrayStrings - рекурсивный trim значений массива
- getFormattedSize - получение форматированного размера(Kb,Mb...)
- eraseArray - рекурсивное удаление пустых элементов
- getUniqueArray - сравнение массива и получений расхождения, рекурсивно

### Debug
####Logger - старый логгер
- getInstance - получение объекта
- activate - включене и отклоючение
- setType - установка названия файла лога
- write - Запись данных - строки или массива
- writeEndLine - запись длинного разделителя
- writeSeparator - запись разделителя

####CheckResources - снимок ресурсов
- getInstance - получение объекта
- setStep - установка следующего шага
- init - запуск первого шага
- show - вывод на экран
- get - получение значений массивом
- setUse - включене и отклоючение

### Helpers
#### WordHelper
Класс для работы со словами - например окончания
- declension - Возвращает нужную форму существительного, стоящего после числительного
- showWeight - Возвращает отформатированный вес
- showLengthByMillimeters - Возвращает отформатированную длину в см - задается в мм
- numberFormat - Форматированный вывод чиел, с возможностью удаления незначащих нулей и с округлением до нужной точности
- clear - Очистка текста от примесей(тегов, лишних спец. символов)
- formatSize - получение форматированного размера(Kb,Mb...)

#### ClassFinderHelper
Получение списка классов
- getClasses - Поиск классов с совпадением имени в определенной папке

#### DateHelper
Хелпер для работы с датами
- replaceRuMonth - Подстановка русских месяцев по шаблону
- replaceRuDayOfWeek - Подстановка дней недели по шаблону

#### PhoneHelper
Обработка и нормализация телефонов
- isPhone - Проверяет телефон по правилам нормализации. Допускаются только десятизначные номера с ведущими 7 или 8
- normalizePhone - Нормализует телефонный номер.
    - Возвращает телефонный номер в формате xxxxxxxxxx (10 цифр без разделителя)
    - Кидает исключение, если $phone - не номер
- formatPhone - Форматирует телефон по шаблону