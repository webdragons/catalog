<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\catalog\frontend\ar\Section;
use yii\helpers\ArrayHelper;
use yii\mongodb\Query;

/**
 * Class ProductsMongoDBService
 * @package bulldozer\catalog\frontend\services
 */
class ProductsMongoDBService extends \bulldozer\catalog\common\services\ProductsMongoDBService
{
    /**
     * @param Section|null $section
     * @param array $filter
     * @return array
     */
    public function getFilteredProductIds(?Section $section, array $filter): array
    {
        $query = new Query();
        $query->select(['id'])
            ->from(self::COLLECTION_NAME);

        if ($section) {
            $sectionIds = [(int)$section->id];

            $query->where(['sections' => $sectionIds]);
        }

        foreach ($filter as $code => $value) {
            if ($code == 'properties') {
                foreach ($value as $propertyId => $propertyValue) {
                    if (is_array($propertyValue) && count($propertyValue) > 0) {
                        $query->andWhere(['properties' => [
                            '$elemMatch' => [
                                'id' => $propertyId,
                                'value' => [
                                    '$in' => $propertyValue
                                ],
                            ],
                        ]]);
                    }
                }
            }
        }

        return ArrayHelper::getColumn($query->all(), 'id');
    }

    /**
     * @param Section|null $section
     * @return array
     */
    public function getPropertyValues(?Section $section): array
    {
        return $this->buildPropertyValues($section);
    }

    /**
     * @param Section|null $section
     * @return array
     */
    protected function buildPropertyValues(?Section $section): array
    {
        $query = new Query();
        $query->select(['properties'])
            ->from(self::COLLECTION_NAME);

        if ($section) {
            $sectionIds = [(int)$section->id];

            $query->where(['sections' => $sectionIds]);
        }

        $products = $query->all();
        $properties = [];

        foreach ($products as $product) {
            foreach ($product['properties'] as $property) {
                $id = $property['id'];

                if (!isset($properties[$id])) {
                    $properties[$id] = [];
                }

                $properties[$id][] = $property['value'];
            }
        }

        return $properties;
    }
}