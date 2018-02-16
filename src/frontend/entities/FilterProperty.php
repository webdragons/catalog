<?php

namespace bulldozer\catalog\frontend\entities;

use bulldozer\catalog\common\ar\PropertyEnum;
use bulldozer\catalog\common\enums\PropertyTypesEnum;

/**
 * Class FilterProperty
 * @package bulldozer\catalog\frontend\entities
 */
class FilterProperty
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var array
     */
    private $values = [];

    /**
     * FilterProperty constructor.
     * @param int $id
     * @param string $name
     * @param string $type
     */
    public function __construct(int $id, string $name, string $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        if ($this->type != PropertyTypesEnum::TYPE_ENUM) {
            $this->values = array_unique($this->values);
        }

        usort($this->values, function($a, $b) {
            if (is_string($a) && is_string($b)) {
                return strcmp($a, $b);
            } elseif ($a instanceof PropertyEnum && $b instanceof PropertyEnum) {
                return strcmp($a->value, $b->value);
            }
        });

        return $this->values;
    }

    /**
     * @param mixed $value
     */
    public function addValue($value): void
    {
        $this->values[] = $value;
    }
}