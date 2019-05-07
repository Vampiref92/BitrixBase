<?php

namespace Vf92\BitrixUtils\Orm\Collection;

use Bitrix\Main\FileTable;
use InvalidArgumentException;
use Vf92\BitrixUtils\Orm\Model\Image;
use Vf92\Enum\MediaEnum;
use Vf92\MiscUtils\Collection\ObjectArrayCollection;

/**
 * Class ImageCollection
 * @package Vf92\BitrixUtils\Orm\Collection
 */
class ImageCollection extends ObjectArrayCollection
{
    /**
     * @param array $ids
     *
     * @return ImageCollection
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createFromIds(array $ids = [])
    {
        $collection = new static();
        if ($ids) {
            $result = FileTable::query()->addFilter('ID', $ids)->addSelect('*')->exec();
            while ($item = $result->fetch()) {
                $collection->add(new Image($item));
            }
        }

        return $collection;
    }

    /**
     * Dirty hack
     *
     * @throws \InvalidArgumentException
     */
    public static function createNoImageCollection()
    {
        $collection = new static();

        $collection->add(new Image([
            'src'    => MediaEnum::NO_IMAGE_WEB_PATH,
            'width'  => 500,
            'height' => 362,
        ]));

        return $collection;
    }

    /**
     * @param mixed $object
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkType($object)
    {
        if (!($object instanceof Image)) {
            throw new InvalidArgumentException('Переданный объект не является картинкой');
        }
    }
}
