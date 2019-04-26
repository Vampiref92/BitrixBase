<?php

namespace Vf92\BitrixUtils\Orm\Model;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\FileTable;
use Vf92\BitrixUtils\Orm\Model\Exceptions\FileNotFoundException;
use Vf92\BitrixUtils\Orm\Model\Interfaces\FileInterface;

/**
 * Class File
 *
 * @package Vf92\BitrixUtils\OldOrm\Model
 */
class File implements FileInterface
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @var string
     */
    protected $src;

    /**
     * File constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        if ($fields['src']) {
            $this->setSrc($fields['src']);
        }
        
        $this->fields = $fields;
    }

    /**
     * @param string $primary
     *
     * @return static
     * @throws FileNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createFromPrimary($primary)
    {
        $fields = FileTable::getById($primary)->fetch();

        if (!$fields) {
            throw new FileNotFoundException(sprintf('File with id %s is not found', $primary));
        }

        return new static($fields);
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        if ($this->src === null) {
            try {
                $src = sprintf(
                    '/%s/%s/%s',
                    Option::get('main', 'upload_dir', 'upload'),
                    $this->getSubDir(),
                    $this->getFileName()
                );
                $this->setSrc($src);
            } catch (ArgumentNullException $e) {
            } catch (ArgumentOutOfRangeException $e) {
            }
        }

        return $this->src;
    }

    /**
     * @param string $src
     *
     * @return static
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubDir()
    {
        return (string)$this->fields['SUBDIR'];
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return (string)$this->fields['FILE_NAME'];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->fields['ID'];
    }

    /**
     * @param int $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->fields['ID'] = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSrc();
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
