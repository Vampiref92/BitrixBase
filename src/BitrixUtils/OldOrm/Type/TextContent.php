<?php

namespace Vf92\BitrixUtils\OldOrm\Type;

/**
 * Class TextContent
 *
 * @package Vf92\BitrixUtils\OldOrm\Type
 */
class TextContent
{
    public const TYPE_HTML = 'html';

    public const TYPE_TEXT = 'text';

    /**
     * @var string Тип содержимого
     * @see TextContent::TYPE_*
     */
    private $type = self::TYPE_HTML;

    /**
     * @var string
     */
    private $text = '';

    /**
     * TextContent constructor.
     *
     * @param array|null $fields
     */
    public function __construct(?array $fields = null)
    {
        if (null !== $fields && isset($fields['TYPE'], $fields['TEXT'])) {
            $this->withType($fields['TYPE'])
                ->withText($fields['TEXT']);
        }
    }

    /**
     * @param string $text
     *
     * @return TextContent
     */
    public function withText(string $text): TextContent
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return TextContent
     */
    public function withType(string $type): TextContent
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return
            $this->matchType(self::TYPE_HTML)
                ? \html_entity_decode($this->text)
                : $this->text;
    }

    /**
     *
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getText();
    }


    /**
     * @param $type
     *
     * @return bool
     */
    private function matchType($type): bool
    {
        return \strtolower($this->getType()) === $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
