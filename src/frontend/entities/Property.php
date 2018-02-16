<?php

namespace bulldozer\catalog\frontend\entities;

use bulldozer\catalog\common\ar\PropertyEnum;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use yii\helpers\Html;

/**
 * Class Property
 * @package bulldozer\catalog\frontend\entities
 */
class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var PropertyEnum[]
     */
    private $enums = [];

    /**
     * Property constructor.
     * @param string $name
     * @param int $type
     * @param bool $multiple
     * @param int $groupId
     * @param array $enums
     */
    public function __construct(string $name, int $type, bool $multiple, ?int $groupId, array $enums)
    {
        $this->name = $name;
        $this->type = $type;
        $this->multiple = $multiple;
        $this->groupId = $groupId;
        $this->enums = $enums;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param int $id
     */
    public function setGroupId(?int $id): void
    {
        $this->groupId = $id;
    }

    /**
     * @param string $value
     */
    public function setName(string $value)
    {
        $this->name = $value;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @param bool $value
     */
    public function setMultiple(bool $value)
    {
        $this->multiple = $value;
    }

    /**
     * @param $value
     */
    public function addValue($value)
    {
        $this->values[] = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param array $enums
     */
    public function setEnums(array $enums): void
    {
        $this->enums = $enums;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        if (!$this->multiple) {
            $value = $this->values[0] ?? null;

            if ($value === null) {
                return null;
            }

            switch ($this->type) {
                case PropertyTypesEnum::TYPE_STRING:
                case PropertyTypesEnum::TYPE_NUMBER:
                case PropertyTypesEnum::TYPE_ENUM:
                    return $value;
                case PropertyTypesEnum::TYPE_BOOLEAN:
                    return $value == 1 ? 'Да' : 'Нет';
                case PropertyTypesEnum::TYPE_URL:
                    $data = json_decode($value, true);
                    return Html::a($data['name'], $data['link']);
            }
        } else {
            switch ($this->type) {
                case PropertyTypesEnum::TYPE_STRING:
                case PropertyTypesEnum::TYPE_NUMBER:
                case PropertyTypesEnum::TYPE_ENUM:
                    return implode(', ', $this->values);
                case PropertyTypesEnum::TYPE_URL:
                    $urls = [];

                    foreach ($this->values as $value) {
                        $data = json_decode($value, true);
                        $urls[] = Html::a($data['name'], $data['link']);
                    }

                    return implode(', ', $urls);
            }
        }
    }
}