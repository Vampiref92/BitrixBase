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

    /**
     * @return bool
     */
    public function isNews()
    {
        return $this->isPartitionDir('/news');
    }

    /**
     * @return bool
     */
    public function isListNews()
    {
        return $this->isDir('/news');
    }

    /**
     * @return bool
     */
    public function isCatalog()
    {
        return $this->isPartitionDir('/catalog');
    }

    /**
     * @return bool
     */
    public function isDetailNews()
    {
        return $this->isPartitionDir('/news');
    }

    /**
     * @return bool
     */
    public function isPersonalDirectory()
    {
        return $this->isPartitionDir('/personal');
    }

    /**
     * @return bool
     */
    public function isPersonal()
    {
        return $this->isDir('/personal');
    }

    /**
     * @return bool
     */
    public function isRegister()
    {
        return $this->isDir('/personal/register');
    }

    /**
     * @return bool
     */
    public function isForgotPassword()
    {
        return $this->isDir('/personal/forgot-password');
    }

    /**
     * @return bool
     */
    public function hasPersonalProfile()
    {
        return $this->isPersonal();
    }

    /**
     * @return bool
     */
    public function isOrderPage()
    {
        return $this->isDir('/sale/order') || $this->isPartitionDir('/sale/order');
    }

    /**
     * @return bool
     */
    public function isPaymentPage()
    {
        return $this->isDir('/sale/payment') || $this->isPartitionDir('/sale/payment');
    }

    /**
     * @return bool
     */
    public function isOrderDeliveryPage()
    {
        return $this->isDir('/sale/order/delivery');
    }

    /**
     * @return bool
     */
    public function hasOrderDeliveryPage()
    {
        return $this->isOrderDeliveryPage();
    }

    /**
     * @return bool
     */
    public function isBasket()
    {
        return $this->isDir('/cart');
    }

    /**
     * @return bool
     */
    public function hasUserAuth()
    {
        return $this->isPartitionDirByFilePath('/ajax/user/auth/login') || $this->isPartitionDirByFilePath('/personal') || $this->isPartitionDirByFilePath('/sale');
    }

    /**
     * @return bool
     */
    public function isSearchPage()
    {
        return $this->isDir('/catalog/search');
    }
}
