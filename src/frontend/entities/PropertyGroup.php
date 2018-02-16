<?php

namespace bulldozer\catalog\frontend\entities;

/**
 * Class PropertyGroup
 * @package bulldozer\catalog\frontend\entities
 */
class PropertyGroup
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Property[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $isGroup;

    /**
     * PropertyGroup constructor.
     * @param string $name
     * @param bool $isGroup
     */
    public function __construct(string $name, bool $isGroup = true)
    {
        $this->name = $name;
        $this->isGroup = $isGroup;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
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
     * @param Property $property
     */
    public function addProperty(Property $property): void
    {
        $this->properties[] = $property;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param bool $isGroup
     */
    public function setIsGroup(bool $isGroup): void
    {
        $this->isGroup = $isGroup;
    }

    /**
     * @return bool
     */
    public function isGroup(): bool
    {
        return $this->isGroup;
    }
}