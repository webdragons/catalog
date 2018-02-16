<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\ProductListService;
use bulldozer\catalog\common\ar\ProductList;
use bulldozer\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class ProductsListController
 * @package bulldozer\catalog\backend\controllers
 */
class ProductsListController extends Controller
{
    /**
     * @var ProductListService
     */
    private $productListService;

    /**
     * ProductsListController constructor.
     * @param string $id
     * @param $module
     * @param ProductListService $productListService
     * @param array $config
     */
    public function __construct(string $id, $module, ProductListService $productListService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->productListService = $productListService;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update'],
                        'allow' => true,
                        'roles' => ['catalog_manage'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' => ProductList::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id)
    {
        $list = ProductList::findOne($id);

        if ($list === null) {
            throw new NotFoundHttpException();
        }

        $model = $this->productListService->getForm($list);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->productListService->save($model, $list);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'List successful updated'));

            return $this->redirect(['/catalog/products-list']);
        }

        return $this->render('update', [
            'model' => $model,
            'list' => $list,
        ]);
    }
}