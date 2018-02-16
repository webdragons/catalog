<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\forms\MarkupForm;
use bulldozer\catalog\backend\services\PriceService;
use bulldozer\catalog\backend\services\SectionService;
use bulldozer\catalog\common\ar\Price;
use bulldozer\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class MarkupController
 * @package bulldozer\catalog\backend\controllers
 */
class MarkupController extends Controller
{
    /**
     * @var PriceService
     */
    private $priceService;

    /**
     * @var SectionService
     */
    private $sectionService;

    /**
     * MarkupController constructor.
     * @param string $id
     * @param $module
     * @param PriceService $priceService
     * @param SectionService $sectionService
     * @param array $config
     */
    public function __construct(string $id, $module, PriceService $priceService, SectionService $sectionService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->priceService = $priceService;
        $this->sectionService = $sectionService;
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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['catalog_manage'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        /** @var MarkupForm $model */
        $model = App::createObject([
            'class' => MarkupForm::class,
        ]);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            if ($this->priceService->markup($model)) {
                App::$app->getSession()->setFlash('success', 'Наценка выполнена');

                return $this->redirect(['index']);
            } else {
                App::$app->getSession()->setFlash('error', 'Произошла ошибка сервера при сохранении данных.');
            }
        }

        $prices = ArrayHelper::map(Price::find()->all(), 'id', 'name');

        return $this->render('index', [
            'model' => $model,
            'prices' => $prices,
            'sections' => $this->sectionService->getSectionsTree(),
        ]);
    }
}