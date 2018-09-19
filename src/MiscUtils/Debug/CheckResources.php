<?php

namespace Vf92\MiscUtils\Debug;

use Vf92\MiscUtils\Helpers\WordHelper;

/**
 * Class CheckResources
 * @package Vf92\MiscUtils\Debug
 */
class CheckResources
{
    protected static $instance = null;
    protected $step = 1;
    protected $arResources;
    protected $arFormatedResources;
    protected $bUse;

    /**
     * @return \Vf92\MiscUtils\Debug\CheckResources
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setStep()
    {
        if ($this->bUse) {
            if (!isset($this->arResources['START'])) {
                $this->init();
            } else {
                $memory_value = memory_get_usage();
                $time = time();
                $used_memory = $memory_value - $this->arResources['START']['MEMORY']['CURRENT'];
                $this->arResources['STEP-' . $this->step] = [
                    'TIME'   => [
                        'CURRENT' => $time,
                    ],
                    'MEMORY' => [
                        'CURRENT' => $memory_value,
                        'USED'    => $used_memory,
                    ],
                ];
                $this->arFormatedResources['STEP-' . $this->step] = [
                    'TIME'   => [
                        'USED' => intval($time - $this->arResources['START']['TIME']['CURRENT']) . 'с',
                    ],
                    'MEMORY' => [
                        'CURRENT' => WordHelper::formatSize($memory_value),
                        'USED'    => WordHelper::formatSize($used_memory),
                    ],
                ];
                if ($this->step > 1) {
                    $lastStep = $this->step - 1;
                    $this->arFormatedResources['STEP-' . $this->step]['TIME']['USED_INTERVAL_STEP'] =
                        intval($time - $this->arResources['STEP-' . $lastStep]['TIME']['CURRENT']) . 'с';
                    $this->arFormatedResources['STEP-' . $this->step]['MEMORY']['USED_INTERVAL_STEP'] =
                        WordHelper::formatSize(
                            $memory_value - $this->arResources['STEP-' . $lastStep]['MEMORY']['CURRENT']
                        );
                }
                $this->step++;
            }
        }
    }

    public function init()
    {
        if ($this->bUse) {
            if (!isset($this->arResources['START'])) {
                $memory_value = memory_get_usage();
                $time = time();
                $this->arResources['START'] = [
                    'TIME'   => [
                        'CURRENT' => $time,
                    ],
                    'MEMORY' => [
                        'CURRENT' => $memory_value,
                    ],
                ];
                $this->arFormatedResources['START'] = [
                    'TIME'   => [
                        'USED' => (string)0 . 'c',
                    ],
                    'MEMORY' => [
                        'CURRENT' => WordHelper::formatSize($memory_value),
                    ],
                ];
            }
        }
    }

    /**
     * @param bool $expand
     */
    public function show($expand = true)
    {
        if ($this->bUse) {
            if (function_exists('pp')) {
                pp($this->arFormatedResources, $expand);
            } else {
                echo '<pre>';
                print_r($this->arFormatedResources);
                echo '</pre>';
            }
        }
    }

    /**
     * @return bool
     */
    public function get()
    {
        if ($this->bUse) {
            return $this->arFormatedResources;
        }

        return false;
    }

    /**
     * @param bool $bUse
     */
    public function setUse($bUse = true)
    {
        $this->bUse = $bUse;
    }
}