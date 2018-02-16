<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\ProductListForm;
use bulldozer\catalog\common\ar\ProductList;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class ProductListService
 * @package bulldozer\catalog\backend\services
 */
class ProductListService
{
    /**
     * @param ProductList $productList
     * @return ProductListForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(ProductList $productList): ProductListForm
    {
        /** @var ProductListForm $form */
        $form = App::createObject([
            'class' => ProductListForm::class,
        ]);

        $form->setAttributes($productList->getAttributes($form->getSavedAttributes()));

        if ($productList->productsList) {
            $form->products = ArrayHelper::getColumn($productList->productsList, 'id');
        }

        return $form;
    }

    /**
     * @param ProductListForm $form
     * @param ProductList $productList
     * @return ProductList
     * @throws Exception
     */
    public function save(ProductListForm $form, ProductList $productList): ProductList
    {
        $productList->setAttributes($form->getAttributes($form->getSavedAttributes()));
        $productList->products = json_encode($form->products);

        if ($productList->save()) {
            return $productList;
        }

        throw new Exception('Cant save product list. Errors: ' . json_encode($productList->getErrors()));
    }
}