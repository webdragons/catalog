<?php

namespace bulldozer\catalog\frontend\controllers;

use bulldozer\App;
use bulldozer\catalog\frontend\ar\Product;
use bulldozer\catalog\frontend\services\ProductsService;
use bulldozer\seo\frontend\services\SeoService;
use bulldozer\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ProductsController
 * @package bulldozer\catalog\frontend\controllers
 */
class ProductsController extends Controller
{
    /**
     * @var ProductsService
     */
    private $productsService;

    /**
     * ProductsController constructor.
     * @param string $id
     * @param $module
     * @param ProductsService $productsService
     * @param array $config
     */
    public function __construct(string $id, $module, ProductsService $productsService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->productsService = $productsService;
    }

    /**
     * @param string $slug
     * @param string $section_slug
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionView(string $slug, string $section_slug)
    {
        $product = Product::findOne(['slug' => $slug]);

        if ($product === null || $product->section->slug != $section_slug) {
            throw new NotFoundHttpException();
        }

        /** @var SeoService $seoService */
        $seoService = App::createObject([
            'class' => SeoService::class,
            'model' => $product,
            'defaultValues' => [
                'h1' => $product->name,
                'title' => $product->name,
            ],
        ]);

        $properties = $this->productsService->getProperties($product);

        return $this->render('view', [
            'product' => $product,
            'seoService' => $seoService,
            'properties' => $properties,
        ]);
    }
}