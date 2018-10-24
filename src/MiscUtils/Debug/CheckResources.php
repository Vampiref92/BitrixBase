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
    protected $resources;
    protected $formattedResources;
    protected $use;

    /**
     * @return \Vf92\MiscUtils\Debug\CheckResources
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function setStep()
    {
        if ($this->use) {
            if (!isset($this->resources['START'])) {
                $this->init();
            } else {
                $memoryValue = memory_get_usage();
                $time = getmicrotime();
                $usedMemory = $memoryValue - $this->resources['START']['MEMORY']['CURRENT'];
                $this->resources['STEP-' . $this->step] = [
                    'TIME'   => [
                        'CURRENT' => $time,
                    ],
                    'MEMORY' => [
                        'CURRENT' => $memoryValue,
                        'USED'    => $usedMemory,
                    ],
                ];
                $differenceTime = $time - $this->resources['START']['TIME']['CURRENT'];
                $this->formattedResources['STEP-' . $this->step] = [
                    'TIME'   => [
                        'USED' => ($differenceTime/1000). 'с ['.$differenceTime.' мс]',
                    ],
                    'MEMORY' => [
                        'CURRENT' => WordHelper::formatSize($memoryValue),
                        'USED'    => WordHelper::formatSize($usedMemory),
                    ],
                ];
                if ($this->step > 1) {
                    $lastStep = $this->step - 1;
                    $this->formattedResources['STEP-' . $this->step]['TIME']['USED_INTERVAL_STEP'] =
                        (($time - $this->resources['STEP-' . $lastStep]['TIME']['CURRENT'])/1000) . 'с';
                    $this->formattedResources['STEP-' . $this->step]['MEMORY']['USED_INTERVAL_STEP'] =
                        WordHelper::formatSize(
                            $memoryValue - $this->resources['STEP-' . $lastStep]['MEMORY']['CURRENT']
                        );
                }
                $this->step++;
            }
        }
    }

    public function init()
    {
        if ($this->use) {
            if (!isset($this->resources['START'])) {
                $memoryValue = memory_get_usage();
                $time = getmicrotime();
                $this->resources['START'] = [
                    'TIME'   => [
                        'CURRENT' => $time,
                    ],
                    'MEMORY' => [
                        'CURRENT' => $memoryValue,
                    ],
                ];
                $this->formattedResources['START'] = [
                    'TIME'   => [
                        'USED' => (string)0 . 'c',
                    ],
                    'MEMORY' => [
                        'CURRENT' => WordHelper::formatSize($memoryValue),
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
        if ($this->use) {
            if (function_exists('pp')) {
                pp($this->formattedResources, $expand);
            } else {
                echo '<pre>';
                print_r($this->formattedResources);
                echo '</pre>';
            }
        }
    }

    /**
     * @return bool
     */
    public function get()
    {
        if ($this->use) {
            return $this->formattedResources;
        }

        return false;
    }

    /**
     * @param bool $bUse
     */
    public function setUse($bUse = true)
    {
        $this->use = $bUse;
    }
}