<?php

namespace Vf92\BitrixUtils\Component;


use Vf92\Log\LazyLoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use RuntimeException;

/**
 * Class BaseBitrixComponent
 *
 * Default component for current project
 *
 * @package Vf92\BitrixUtils
 */
abstract class BaseBitrixComponent extends \CBitrixComponent implements LoggerAwareInterface
{
    use LazyLoggerAwareTrait;

    /**
     * @var string
     */
    private $templatePage = '';

    /**
     * @param $params
     *
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $this->withLogName(
            \sprintf(
                'component_%s',
                static::class
            )
        );

        $params['return_result'] = $params['return_result'] === true || $params['return_result'] === 'Y';

        return parent::onPrepareComponentParams($params);
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException
     * @return null|array
     */
    public function executeComponent()
    {
        if ($this->startResultCache()) {

            try {
                parent::executeComponent();

                $this->prepareResult();

                $this->includeComponentTemplate($this->templatePage);
            } catch (\Exception $e) {
                $this->log()->error(sprintf('%s: %s', \get_class($e), $e->getMessage()), [
                    'trace' => $e->getTrace()
                ]);
                $this->abortResultCache();
            }

            $this->setResultCacheKeys($this->getResultCacheKeys());
        }

        if ($this->arParams['return_result']) {
            return $this->arResult;
        }

        return null;
    }

    /**
     * Prepare component result
     */
    abstract public function prepareResult();

    /**
     * @return array
     */
    public function getResultCacheKeys()
    {
        return [];
    }

    /**
     * @param string $page
     */
    protected function setTemplatePage($page) {
        $this->templatePage = $page;
    }
}
