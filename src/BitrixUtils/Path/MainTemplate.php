<?php namespace Vf92\BitrixUtils\Path;

/**
 * Class MainTemplate
 *
 * Класс для основного шаблона
 *
 * @package Vf92\BitrixUtils\Path
 */
class MainTemplate extends TemplateAbstract
{
    /**
     * @return bool
     */
    public function isIndex()
    {
        return $this->isPage('/');
    }

    /**
     * Страница 404
     *
     * @return bool
     */
    public function is404()
    {
        return \defined('ERROR_404') && ERROR_404 === 'Y';
    }

    /**
     * Страница, недоступная для неавторизованных
     *
     * @return bool
     */
    public function isForbidden()
    {
        /**
         * It's bitrix way
         */
        global $USER;

        return \defined('NEED_AUTH') && NEED_AUTH === true && !$USER->IsAuthorized();
    }

    public function isCatalog()
    {
        return $this->isDir('/catalog');
    }

    public function isCatalogPage()
    {
        return $this->isPartitionDir('/catalog');
    }

    public function hasCatalogPage()
    {
        return $this->isCatalog() || $this->isCatalogPage();
    }

    public function isPersonal()
    {
        return $this->isDir('/personal');
    }

    public function isPersonalPage()
    {
        return $this->isPartitionDir('/personal');
    }

    public function hasPersonalPage()
    {
        return $this->isPersonal() || $this->isPersonalPage();
    }

    public function isBasket()
    {
        return $this->isDir('/personal/cart');
    }

    public function isOrder()
    {
        return $this->isDir('/personal/order/make');
    }
}
