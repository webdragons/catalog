<?php

namespace bulldozer\catalog\console\controllers;

use bulldozer\catalog\console\services\ProductsMongoDBService;
use bulldozer\console\Controller;

/**
 * Class ProductsController
 * @package bulldozer\catalog\console\controllers
 */
class ProductsController extends Controller
{
    /**
     * @var ProductsMongoDBService
     */
    private $productsMongoDBService;

    /**
     * ProductsController constructor.
     * @param string $id
     * @param $module
     * @param ProductsMongoDBService $productsMongoDBService
     * @param array $config
     */
    public function __construct(string $id, $module, ProductsMongoDBService $productsMongoDBService, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->productsMongoDBService = $productsMongoDBService;
    }

    /**
     * @throws \yii\mongodb\Exception
     */
    public function actionUpdateMongodb()
    {
        $this->productsMongoDBService->updateAll();
    }
}