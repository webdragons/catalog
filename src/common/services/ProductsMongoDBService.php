<?php

namespace bulldozer\catalog\common\services;

use bulldozer\App;
use bulldozer\catalog\common\ar\Product;
use yii\caching\Cache;
use yii\mongodb\Connection;

/**
 * Class ProductsMongoDBService
 * @package bulldozer\catalog\common\services
 */
class ProductsMongoDBService
{
    const COLLECTION_NAME = 'catalog_products';

    /**
     * @var Connection
     */
    private $mongodbComponent;

    /**
     * @var Cache
     */
    private $cacheProvider;

    /**
     * ProductsCacheService constructor.
     */
    public function __construct()
    {
        $this->mongodbComponent = App::$app->mongodb;
        $this->cacheProvider = App::$app->cache;
    }

    /**
     * @param Product $product
     * @throws \yii\mongodb\Exception
     */
    public function updateProduct(Product $product): void
    {
        $collection = $this->mongodbComponent->getCollection(self::COLLECTION_NAME);

        $collection->remove(['id' => $product->id]);

        $cacheProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'sections' => [(int) $product->section_id],
            'properties' => [],
        ];

        foreach ($product->propertyValues as $propertyValue) {
            $cacheProduct['properties'][] = [
                'id' => $propertyValue->property_id,
                'value' => $propertyValue->value,
            ];
        }

        $collection->insert($cacheProduct);
    }
}