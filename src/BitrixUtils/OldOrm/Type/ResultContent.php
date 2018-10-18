<?php

namespace Vf92\BitrixUtils\OldOrm\Type;

/**
 * Class ResultContent
 *
 * @package Vf92\BitrixUtils\OldOrm\Type
 */
class ResultContent
{
    const TYPE_SUCCESS = 'OK';
    
    const TYPE_ERROR   = 'ERROR';
    
    /**
     * @var string Тип содержимого
     *
     * @see TextContent::TYPE_*
     */
    private $type = self::TYPE_SUCCESS;
    
    /**
     * @var string
     */
    private $message = '';
    
    /**
     * TextContent constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        if (isset($fields['TYPE'])) {
            $this->withType($fields['TYPE']);
        }
        
        if (isset($fields['MESSAGE'])) {
            $this->withMessage($fields['MESSAGE']);
        }
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     *
     * @return ResultContent
     */
    public function withType($type)
    {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @param string $message
     *
     * @return ResultContent
     */
    public function withMessage($message)
    {
        $this->message = $message;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isError()
    {
        return $this->type === self::TYPE_ERROR;
    }
    
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->type === self::TYPE_SUCCESS;
    }
}
