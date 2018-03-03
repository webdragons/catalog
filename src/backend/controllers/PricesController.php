<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\PriceService;
use bulldozer\catalog\common\ar\Currency;
use bulldozer\catalog\common\ar\Price;
use bulldozer\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class PricesController
 * @package bulldozer\catalog\backend\controllers
 */
class PricesController extends Controller
{
    /**
     * @var PriceService
     */
    private $priceService;

    /**
     * PricesController constructor.
     * @param string $id
     * @param $module
     * @param PriceService $priceService
     * @param array $config
     */
    public function __construct(string $id, $module, PriceService $priceService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->priceService = $priceService;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['catalog_manage'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Price models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' => Price::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Price model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = $this->priceService->getPriceForm();

        $currencies = Currency::find()->all();
        $prices = Price::find()->all();

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $price = $this->priceService->save($model);

            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Price type successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $price->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'price' => null,
            'currencies' => $currencies,
            'prices' => $prices,
        ]);
    }

    /**
     * Updates an existing Price model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id)
    {
        $price = $this->findModel($id);

        $model = $this->priceService->getPriceForm($price);

        $currencies = Currency::find()->all();
        $prices = Price::find()->all();

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->priceService->save($model, $price);
            Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Price type successful updated'));

            if (!Yii::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $price->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'price' => $price,
            'currencies' => $currencies,
            'prices' => $prices,
        ]);
    }

    /**
     * Deletes an existing Price model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id)
    {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Price type successful deleted'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Price model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Price the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Price::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('catalog', 'The requested page does not exist.'));
        }
    }
}