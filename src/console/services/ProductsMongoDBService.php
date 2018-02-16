<?php

namespace bulldozer\catalog\console\services;

use bulldozer\catalog\common\ar\Product;
use ProgressBar\Manager;

/**
 * Class ProductsMongoDBService
 * @package bulldozer\catalog\backend\services
 */
class ProductsMongoDBService extends \bulldozer\catalog\common\services\ProductsMongoDBService
{
    /**
     * @throws \yii\mongodb\Exception
     */
    public function updateAll(): void
    {
        $products = $this->getProducts();

        $progressBar = new Manager(0, count($products));
        $i = 0;

        foreach ($products as $product) {
            $this->updateProduct($product);
            $progressBar->update(++$i);
        }
    }

    /**
     * @return Product[]
     */
    protected function getProducts(): array
    {
        return Product::find()->all();
    }
}