<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\PropertyService;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\ar\PropertyEnum;
use bulldozer\catalog\common\ar\PropertyGroup;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\web\Controller;
use Yii;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class PropertiesController
 * @package bulldozer\catalog\backend\controllers
 */
class PropertiesController extends Controller
{
    /**
     * @var PropertyService
     */
    private $propertyService;

    /**
     * PropertiesController constructor.
     * @param string $id
     * @param $module
     * @param PropertyService $propertyService
     * @param array $config
     */
    public function __construct(string $id, $module, PropertyService $propertyService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->propertyService = $propertyService;
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
                            'enum-list',
                            'create-enum-value',
                            'update-enum-value',
                            'delete-enum-value'
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
                    'delete-enum-value' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Property models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' => Property::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Property model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = $this->propertyService->getForm();

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $property = $this->propertyService->save($model);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Property successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $property->id]);
            }
        }

        $groups = ArrayHelper::map(PropertyGroup::find()->all(), 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'groups' => $groups,
        ]);
    }

    /**
     * Updates an existing Property model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id)
    {
        $property = $this->findModel($id);

        $model = $this->propertyService->getForm($property);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->propertyService->save($model, $property);
            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Property successful updated'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['update', 'id' => $property->id]);
            }
        }

        $groups = ArrayHelper::map(PropertyGroup::find()->all(), 'id', 'name');

        return $this->render('update', [
            'model' => $model,
            'groups' => $groups,
            'property' => $property,
        ]);
    }

    /**
     * Deletes an existing Property model.
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

        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Property successful deleted'));
        return $this->redirect(['index']);
    }

    /**
     * @param int $property_id
     * @return string
     * @throws NotSupportedException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEnumList(int $property_id)
    {
        $property = $this->findModel($property_id);

        if ($property->type != PropertyTypesEnum::TYPE_ENUM) {
            throw new NotSupportedException();
        }

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = App::createObject([
            'class' => ActiveDataProvider::class,
            'query' => PropertyEnum::find()->where(['property_id' => $property_id]),
        ]);

        return $this->render('enum-list', [
            'property' => $property,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $property_id
     * @return string|\yii\web\Response
     * @throws NotSupportedException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreateEnumValue(int $property_id)
    {
        $property = $this->findModel($property_id);

        if ($property->type != PropertyTypesEnum::TYPE_ENUM) {
            throw new NotSupportedException();
        }

        $model = $this->propertyService->getPropertyEnumForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->propertyService->savePropertyEnum($model, $property->id);
            Yii::$app->getSession()->setFlash('success',  Yii::t('catalog', 'Property value successful created'));

            return $this->redirect(['enum-list', 'property_id' => $property_id]);
        }

        return $this->render('create-enum-value', [
            'property' => $property,
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionUpdateEnumValue(int $id)
    {
        $propertyEnum = PropertyEnum::findOne($id);

        if ($propertyEnum === null) {
            throw new NotFoundHttpException();
        }

        $model = $this->propertyService->getPropertyEnumForm($propertyEnum);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->propertyService->savePropertyEnum($model, $id, $propertyEnum);
            Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Property value successful updated'));

            return $this->redirect(['enum-list', 'property_id' => $propertyEnum->property_id]);
        }

        return $this->render('update-enum-value', [
            'model' => $model,
            'property' => $propertyEnum->property,
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteEnumValue(int $id)
    {
        $model = PropertyEnum::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException();
        }

        $property_id = $model->property_id;
        $model->delete();

        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Property value successful deleted'));
        return $this->redirect(['enum-list', 'property_id' => $property_id]);
    }

    /**
     * Finds the Property model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Property the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        if (($model = Property::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}