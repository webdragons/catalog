<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\SectionService;
use bulldozer\catalog\common\ar\Product;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\ar\Section;
use bulldozer\files\models\Image;
use bulldozer\files\models\Watermark;
use bulldozer\seo\backend\services\SeoService;
use bulldozer\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class SectionsController
 * @package bulldozer\catalog\backend\controllers
 */
class SectionsController extends Controller
{
    /**
     * @var SectionService
     */
    private $sectionService;

    /**
     * @var SeoService
     */
    private $seoService;

    /**
     * SectionsController constructor.
     * @param string $id
     * @param $module
     * @param SectionService $sectionService
     * @param SeoService $seoService
     * @param array $config
     */
    public function __construct(string $id, $module, SectionService $sectionService, SeoService $seoService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->sectionService = $sectionService;
        $this->seoService = $seoService;
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['catalog_manage'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-section' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $query = Section::find()
            ->roots()
            ->with(['creator', 'updater']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('view', [
            'sectionsDataProvider' => $dataProvider,
            'productsDataProvider' => null,
            'section' => null,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        $section = Section::findOne($id);

        if ($section === null) {
            throw new NotFoundHttpException();
        }

        $sectionsDataProvider = new ActiveDataProvider([
            'query' => $section->children(1)->with(['creator', 'updater']),
        ]);

        $productsDataProvider = new ActiveDataProvider([
            'query' => Product::find()->where(['section_id' => $section->id])->with(['creator', 'updater']),
        ]);

        return $this->render('view', [
            'section' => $section,
            'sectionsDataProvider' => $sectionsDataProvider,
            'productsDataProvider' => $productsDataProvider,
        ]);
    }

    /**
     * @param int|null $parent_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionCreate(int $parent_id = null)
    {
        $section = null;

        if ($parent_id !== null) {
            $section = Section::findOne($parent_id);

            if ($section === null) {
                throw new NotFoundHttpException();
            }
        }

        $model = $this->sectionService->getForm();
        $seoForm = $this->seoService->getForm();

        if ($parent_id !== null) {
            $model->parent_id = $parent_id;
        } else {
            $model->parent_id = 0;
        }

        if ($model->load(App::$app->request->post()) && $model->validate() && $seoForm->load(App::$app->request->post()) && $seoForm->validate()) {
            $section = $this->sectionService->save($model);
            $this->seoService->save($section);

            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Section successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['view', 'id' => $section->id]);
            } else {
                return $this->redirect(['update', 'id' => $section->id]);
            }
        }

        $properties = ArrayHelper::map(Property::find()->all(), 'id', 'name');

        $watermarks = Image::find()
            ->joinWith('watermark')
            ->where(['>', Watermark::tableName() . '.id', 0])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'parentSection' => $section,
            'properties' => $properties,
            'watermarks' => $watermarks,
            'seoService' => $this->seoService,
            'sections' => $this->sectionService->getSectionsTree(),
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionUpdate(int $id)
    {
        $section = Section::findOne($id);

        if ($section == null) {
            throw new NotFoundHttpException();
        }

        $model = $this->sectionService->getForm($section);
        $this->seoService->load($section);
        $seoForm = $this->seoService->getForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $seoForm->load(App::$app->request->post()) && $seoForm->validate()) {
            $this->sectionService->save($model, $section);
            $this->seoService->save($section);

            Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Section successful updated'));

            if (!Yii::$app->request->post('here-btn')) {
                return $this->redirect(['view', 'id' => $section->id]);
            } else {
                return $this->redirect(['update', 'id' => $section->id]);
            }
        }

        $properties = ArrayHelper::map(Property::find()->all(), 'id', 'name');

        $watermarks = Image::find()
            ->joinWith('watermark')
            ->where(['>', Watermark::tableName() . '.id', 0])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'section' => $section,
            'sections' => $this->sectionService->getSectionsTree(),
            'properties' => $properties,
            'watermarks' => $watermarks,
            'seoService' => $this->seoService,
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete(int $id)
    {
        $section = Section::findOne($id);

        if ($section == null) {
            throw new NotFoundHttpException();
        }

        $parent = null;

        if (!$section->isRoot()) {
            $parent = $section->parents(1)->one();
        }

        $section->deleteWithChildren();

        Yii::$app->getSession()->addFlash('success', Yii::t('catalog', 'Section successful deleted'));

        if ($parent) {
            return $this->redirect(['view', 'id' => $parent->id]);
        } else {
            return $this->redirect(['index']);
        }
    }
}