<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\forms\PropertyGroupForm;
use bulldozer\catalog\backend\services\PropertyGroupService;
use bulldozer\catalog\common\ar\PropertyGroup;
use bulldozer\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class PropertyGroupsController
 * @package bulldozer\catalog\backend\controllers
 */
class PropertyGroupsController extends Controller
{
    /**
     * @var PropertyGroupService
     */
    private $propertyGroupService;

    /**
     * PropertyGroupsController constructor.
     * @param string $id
     * @param $module
     * @param PropertyGroupService $propertyGroupService
     * @param array $config
     */
    public function __construct(string $id, $module, PropertyGroupService $propertyGroupService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->propertyGroupService = $propertyGroupService;
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
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'delete',
                        ],
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
     * Lists all PropertyGroup models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' => PropertyGroup::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new PropertyGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = $this->propertyGroupService->getForm();

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $propertyGroup = $this->propertyGroupService->save($model);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Properties group successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $propertyGroup->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PropertyGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id)
    {
        $propertyGroup = $this->findModel($id);

        /** @var PropertyGroupForm $model */
        $model = $this->propertyGroupService->getForm($propertyGroup);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->propertyGroupService->save($model, $propertyGroup);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Properties group successful updated'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $propertyGroup->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'propertyGroup' => $propertyGroup,
        ]);
    }

    /**
     * Deletes an existing PropertyGroup model.
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
        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Properties group successful deleted'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the Property model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PropertyGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): PropertyGroup
    {
        if (($model = PropertyGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('catalog', 'The requested page does not exist.'));
        }
    }
}