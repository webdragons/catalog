<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\CurrencyService;
use bulldozer\catalog\common\ar\Currency;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CurrenciesController implements the CRUD actions for Currency model.
 */
class CurrenciesController extends Controller
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    /**
     * CurrenciesController constructor.
     * @param string $id
     * @param $module
     * @param CurrencyService $currencyService
     * @param array $config
     */
    public function __construct(string $id, $module, CurrencyService $currencyService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->currencyService = $currencyService;
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
     * Lists all Currency models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' =>  Currency::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Currency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = $this->currencyService->getForm();

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $currency = $this->currencyService->save($model);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Currency successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $currency->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existi\ng Currency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id)
    {
        $currency = $this->findModel($id);

        $model = $this->currencyService->getForm($currency);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->currencyService->save($model, $currency);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Currency successful updated'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $currency->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'currency' => $currency,
            ]);
        }
    }

    /**
     * Deletes an existing Currency model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id)
    {
        $this->findModel($id)->delete();
        App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Currency successful deleted'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Currency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Currency
    {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
