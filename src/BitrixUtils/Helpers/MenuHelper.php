<?php

namespace Vf92\BitrixUtils\Helpers;

use function count;

/**
 * Class MenuHelper
 * @package Vf92\BitrixUtils\Helpers
 */
Class MenuHelper
{
    /**
     * @var
     */
    protected $lastKey;
    /**
     * @var
     */
    protected $countSubArrayItems;
    /**
     * @var
     */
    protected $arSubArrayItems;
    /**
     * @var
     */
    protected $childrenSectionsName;
    /**
     * @var
     */
    protected $depthLvlName;

    /**
     * @param array $array
     * @param string $childrenSectionsName
     * @param string $depthLvlName
     *
     * @return array
     */
    public function getMultiLvlArray(
        array $array,
        string $childrenSectionsName = 'SECTIONS',
        string $depthLvlName = 'DEPTH_LEVEL'
    ): array {
        $this->childrenSectionsName = $childrenSectionsName;
        $this->depthLvlName = $depthLvlName;
        $this->lastKey = 0;
        $this->arSubArrayItems = $array;
        $this->countSubArrayItems = count($this->arSubArrayItems);
        return $this->returnSubArray();
    }

    /**
     * @param array $array
     *
     * @return int
     */
    public function countMultiArray(array $array): int
    {
        $count = 0;
        foreach ($array as $arItem) {
            if (isset($arItem[$this->childrenSectionsName]) && !empty($arItem[$this->childrenSectionsName])) {
                $count += $this->countMultiArray($arItem[$this->childrenSectionsName]);
            }
            $count++;
        }
        return $count;
    }

    /**
     * @return array
     */
    protected function returnSubArray(): array
    {
        $k = $this->lastKey;
        $arSubMenu = [];
        while ($this->countSubArrayItems > $k) {
            if (!empty($arSubMenu) && count($arSubMenu) !== 0 && $arSubMenu[0][$this->depthLvlName] < $this->arSubArrayItems[$k][$this->depthLvlName]) {
                $this->lastKey = $k;
                $arSubMenu[count($arSubMenu) - 1][$this->childrenSectionsName] = $this->returnSubArray();
                $k += $this->countMultiArray($arSubMenu[count($arSubMenu) - 1][$this->childrenSectionsName]);
                continue;
            } elseif (!empty($arSubMenu) && count($arSubMenu) !== 0 && $arSubMenu[0][$this->depthLvlName] > $this->arSubArrayItems[$k][$this->depthLvlName]) {
                return $arSubMenu;
            } else {
                $arSubMenu[] = $this->arSubArrayItems[$k];
            }
            $k++;
        }
        return $arSubMenu;
    }
}