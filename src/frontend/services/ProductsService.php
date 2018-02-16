<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\catalog\common\ar\ProductPropertyValue;
use bulldozer\catalog\common\ar\PropertyGroup;
use bulldozer\catalog\common\ar\SectionProperty;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\catalog\frontend\ar\Product;
use bulldozer\catalog\frontend\entities\Property;
use yii\helpers\ArrayHelper;

/**
 * Class ProductsService
 * @package bulldozer\catalog\frontend\services
 */
class ProductsService
{
    /**
     * @param Product $product
     * @return \bulldozer\catalog\frontend\entities\PropertyGroup[]
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function getProperties(Product $product): array
    {
        $groups = $this->getGroups();
        $properties = $this->getConvertedProperties($product);

        foreach ($properties as $property) {
            if (isset($groups[$property->getGroupId()])) {
                $group = $groups[$property->getGroupId()];
                $group->addProperty($property);
            } else {
                $group = $groups[0];
                $group->addProperty($property);
            }
        }

        foreach ($groups as $key => $group) {
            if (count($group->getProperties()) == 0) {
                unset($groups[$key]);
            }
        }

        return $groups;
    }

    /**
     * @param Product $product
     * @return Property[]
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function getConvertedProperties(Product $product): array
    {
        $properties = [];

        $propertyValues = $this->getValues($product);

        foreach ($propertyValues as $propertyValue) {
            $_property = $propertyValue->property;

            if ($_property === null) {
                continue;
            }

            if (!isset($properties[$_property->id])) {
                $properties[$_property->id] = new Property(
                    $_property->name,
                    $_property->type,
                    $_property->multiple == 1,
                    ArrayHelper::getValue($_property, 'group.id'),
                    $_property->enums
                );
            }

            /** @var Property $property */
            $property = $properties[$_property->id];

            switch ($property->getType()) {
                case PropertyTypesEnum::TYPE_STRING:
                case PropertyTypesEnum::TYPE_NUMBER:
                case PropertyTypesEnum::TYPE_BOOLEAN:
                case PropertyTypesEnum::TYPE_URL:
                    $property->addValue($propertyValue->value);
                    break;
                case PropertyTypesEnum::TYPE_ENUM:
                    $property->addValue($propertyValue->enumValue);
                    break;
            }
        }

        return $properties;
    }

    /**
     * @return \bulldozer\catalog\frontend\entities\PropertyGroup[]
     */
    protected function getGroups(): array
    {
        $groups = [];

        $groups[0] = new \bulldozer\catalog\frontend\entities\PropertyGroup('Без группы', false);

        /** @var PropertyGroup[] $_groups */
        $_groups = PropertyGroup::find()->orderBy(['sort' => SORT_ASC])->all();
        foreach ($_groups as $_group) {
            $groups[$_group->id] = new \bulldozer\catalog\frontend\entities\PropertyGroup($_group->name);
        }

        return $groups;
    }

    /**
     * @param Product $product
     * @return ProductPropertyValue[]
     */
    protected function getValues(Product $product): array
    {
        $sectionProps = SectionProperty::find()->where(['section_id' => $product->section->id])->all();

        return ProductPropertyValue::find()
            ->joinWith(['property p'])
            ->andWhere([
                'product_id' => $product->id,
                'property_id' => ArrayHelper::getColumn($sectionProps, 'property_id'),
            ])
            ->orderBy(['p.sort' => SORT_ASC])
            ->all();
    }
}