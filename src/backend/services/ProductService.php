<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\ProductForm;
use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\Product;
use bulldozer\catalog\common\ar\ProductImage;
use bulldozer\catalog\common\ar\ProductPrice;
use bulldozer\catalog\common\ar\ProductPropertyValue;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\files\models\Image;
use yii\base\Exception;
use yii\web\UploadedFile;

/**
 * Class ProductService
 * @package bulldozer\catalog\backend\services
 */
class ProductService
{
    /**
     * @var ProductsMongoDBService
     */
    private $productsMongoDBService;

    /**
     * ProductService constructor.
     * @param ProductsMongoDBService $productsMongoDBService
     */
    public function __construct(ProductsMongoDBService $productsMongoDBService)
    {
        $this->productsMongoDBService = $productsMongoDBService;
    }

    /**
     * @param Product|null $product
     * @return ProductForm
     */
    public function getForm(?Product $product = null): ProductForm
    {
        /** @var ProductForm $form */
        $form = App::createObject([
            'class' => ProductForm::class,
        ]);

        if ($product) {
            $form->setAttributes($product->getAttributes($form->getSavedAttributes()));

            foreach ($product->prices as $price) {
                $form->prices[$price->price_id] = $price->value;
            }

            foreach ($product->discounts as $discount) {
                $form->discounts[$discount->price_id] = $discount->value;
            }

            $form->uploadedImages = $product->productImages;

            $propertyValues = ProductPropertyValue::find()->where(['product_id' => $product->id])->all();

            foreach ($propertyValues as $propertyValue) {
                if ($propertyValue->property === null) {
                    continue;
                }

                if ($propertyValue->property->multiple == 1) {
                    if (!isset($this->properties[$propertyValue->property_id])
                        && !is_array($form->properties[$propertyValue->property_id])
                    ) {
                        $form->properties[$propertyValue->property_id] = [];
                    }

                    switch ($propertyValue->property->type) {
                        case PropertyTypesEnum::TYPE_URL:
                            $form->properties[$propertyValue->property_id][] = json_decode($propertyValue->value, true);
                            break;
                        default:
                            $form->properties[$propertyValue->property_id][] = $propertyValue->value;
                    }
                } else {
                    switch ($propertyValue->property->type) {
                        case PropertyTypesEnum::TYPE_URL:
                            $form->properties[$propertyValue->property_id] = json_decode($propertyValue->value, true);
                            break;
                        default:
                            $form->properties[$propertyValue->property_id] = $propertyValue->value;
                    }
                }
            }
        } else {
            $lastProduct = Product::find()->orderBy(['sort' => SORT_DESC])->one();

            if ($lastProduct) {
                $form->sort = $lastProduct->sort + 100;
            } else {
                $form->sort = 100;
            }
        }

        return $form;
    }

    /**
     * @param ProductForm $form
     * @param Product|null $product
     * @return Product
     * @throws Exception
     * @throws \yii\mongodb\Exception
     */
    public function save(ProductForm $form, ?Product $product = null): Product
    {
        if ($product === null) {
            $product = App::createObject([
                'class' => Product::class,
            ]);
        }

        $transaction = App::$app->db->beginTransaction();

        $product->setAttributes($form->getAttributes($form->getSavedAttributes()));

        if ($product->save()) {
            $this->saveProductPrices($form, $product);
            $this->saveProductDiscounts($form, $product);
            $this->saveImages($form, $product);
            $this->saveProperties($form, $product);

            $transaction->commit();
            $this->productsMongoDBService->updateProduct($product);

            return $product;
        }

        throw new Exception('Failed save product. Errors: ' . json_encode($product->getErrors()));
    }

    /**
     * @param ProductForm $form
     * @param Product $product
     * @throws Exception
     */
    protected function saveProductPrices(ProductForm $form, Product $product): void
    {
        ProductPrice::deleteAll(['product_id' => $product->id]);

        foreach ($form->prices as $price_id => $value) {
            /** @var ProductPrice $price_model */
            $price_model = App::createObject([
                'class' => ProductPrice::class,
                'product_id' => $product->id,
                'price_id' => $price_id,
                'value' => $value,
            ]);

            if (!$price_model->save()) {
                throw new Exception('Cant save price. Errors: ' . json_encode($price_model->getErrors()));
            }
        }
    }

    /**
     * @param ProductForm $form
     * @param Product $product
     * @throws Exception
     */
    protected function saveProductDiscounts(ProductForm $form, Product $product): void
    {
        Discount::deleteAll(['product_id' => $product->id]);

        foreach ($form->discounts as $price_id => $value) {
            /** @var Discount $discount_model */
            $discount_model = App::createObject([
                'class' => Discount::class,
                'product_id' => $product->id,
                'price_id' => $price_id,
                'value' => $value,
            ]);

            if (!$discount_model->save()) {
                throw new Exception('Cant save discount. Errors: ' . json_encode($discount_model->getErrors()));
            }
        }
    }

    /**
     * @param ProductForm $form
     * @param Product $product
     * @return void
     * @throws Exception
     */
    protected function saveImages(ProductForm $form, Product $product): void
    {
        $form->images = UploadedFile::getInstances($form, 'images');

        if (is_array($form->images)) {
            foreach ($form->images as $image) {
                /** @var Image $file */
                $file = App::createObject([
                    'class' => Image::class,
                ]);

                if ($file->upload($image) && $file->save()) {
                    if ($product->section->watermark) {
                        $file->setWatermark(App::getAlias('@uploads'
                            . $product->section->watermark->file_path),
                            $product->section->watermark_position,
                            $product->section->watermark_transparency);
                    }

                    /** @var ProductImage $product_image */
                    $product_image = App::createObject([
                        'class' => ProductImage::class,
                        'product_id' => $product->id,
                        'file_id' => $file->id,
                    ]);

                    if (!$product_image->save()) {
                        throw new Exception('Cant save product image. Errors: ' . json_encode($product_image->getErrors()));
                    }
                } else {
                    throw new Exception('Cant save image. Errors: ' . json_encode($file->getErrors()));
                }
            }
        }
    }

    /**
     * @param ProductForm $form
     * @param Product $product
     * @return void
     * @throws Exception
     */
    protected function saveProperties(ProductForm $form, Product $product): void
    {
        ProductPropertyValue::deleteAll(['product_id' => $product->id]);

        if (is_array($form->properties)) {
            /** @var Property[] $props */
            $props = Property::find()->all();

            foreach ($form->properties as $propertyId => $value) {
                $property = $propValue = null;

                foreach ($props as $prop) {
                    if ($prop->id == $propertyId) {
                        $property = $prop;
                        break;
                    }
                }

                if ($property->multiple == 1) {
                    if (is_array($value) && count($value) > 0) {
                        foreach ($value as $_value) {
                            if (is_string($_value) && strlen($_value) == 0) {
                                continue;
                            }

                            switch ($property->type) {
                                case PropertyTypesEnum::TYPE_URL:
                                    $propValue = json_encode($_value);
                                    break;
                                default:
                                    $propValue = $_value;
                            }

                            /** @var ProductPropertyValue $propertyValue */
                            $propertyValue = App::createObject([
                                'class' => ProductPropertyValue::class,
                                'product_id' => $product->id,
                                'property_id' => $propertyId,
                                'value' => $propValue,
                            ]);

                            if (!$propertyValue->save()) {
                                throw new Exception('Cant save property. Errors: ' . json_encode($propertyValue->getErrors()));
                            }
                        }
                    }
                } else {
                    if (is_string($value) && strlen($value) == 0) {
                        continue;
                    }

                    switch ($property->type) {
                        case PropertyTypesEnum::TYPE_URL:
                            $propValue = json_encode($value);
                            break;
                        default:
                            $propValue = $value;
                    }

                    /** @var ProductPropertyValue $propertyValue */
                    $propertyValue = App::createObject([
                        'class' => ProductPropertyValue::class,
                        'product_id' => $product->id,
                        'property_id' => $propertyId,
                        'value' => $propValue,
                    ]);

                    if (!$propertyValue->save()) {
                        throw new Exception('Cant save property. Errors: ' . json_encode($propertyValue->getErrors()));
                    }
                }
            }
        }
    }
}