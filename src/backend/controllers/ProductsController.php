<?php

namespace bulldozer\catalog\backend\controllers;

use bulldozer\App;
use bulldozer\catalog\backend\services\ProductService;
use bulldozer\catalog\backend\services\SectionService;
use bulldozer\catalog\common\ar\Price;
use bulldozer\catalog\common\ar\Product;
use bulldozer\catalog\common\ar\ProductImage;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\ar\Section;
use bulldozer\seo\backend\services\SeoService;
use bulldozer\web\Controller;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class ProductsController
 * @package bulldozer\catalog\backend\controllers
 */
class ProductsController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var SectionService
     */
    private $sectionService;
    /**
     * @var SeoService
     */
    private $seoService;

    /**
     * ProductsController constructor.
     * @param string $id
     * @param $module
     * @param ProductService $productService
     * @param SectionService $sectionService
     * @param SeoService $seoService
     * @param array $config
     */
    public function __construct(string $id, $module, ProductService $productService, SectionService $sectionService, SeoService $seoService, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->productService = $productService;
        $this->sectionService = $sectionService;
        $this->seoService = $seoService;
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
                        'actions' => ['index', 'create', 'update', 'delete', 'image-delete'],
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
     * @param int $section_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate(int $section_id)
    {
        $section = Section::findOne($section_id);

        if ($section === null) {
            throw new NotFoundHttpException();
        }

        $model = $this->productService->getForm();
        $model->section_id = $section_id;
        $seoForm = $this->seoService->getForm();

        if ($model->load(App::$app->request->post()) && $model->validate() && $seoForm->load(App::$app->request->post()) && $seoForm->validate()) {
            $product = $this->productService->save($model);
            $this->seoService->save($product);

            App::$app->getSession()->setFlash('success', Yii::t('catalog', 'Product successful created'));

            if (!App::$app->request->post('here-btn')) {
                return $this->redirect(['/catalog/view', 'id' => $product->section_id]);
            } else {
                return $this->redirect(['update', 'id' => $product->id]);
            }
        }

        $properties = Property::find()
            ->joinWith(['sectionProperties ps'])
            ->andWhere(['ps.section_id' => $section->id])
            ->all();

        $prices = Price::find()->all();

        return $this->render('create', [
            'model' => $model,
            'section' => $section,
            'properties' => $properties,
            'sections' => $this->sectionService->getSectionsTree(),
            'prices' => $prices,
            'seoService' => $this->seoService,
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(int $id)
    {
        $product = Product::findOne($id);

        if ($product === null) {
            throw new NotFoundHttpException();
        }

        $model = $this->productService->getForm($product);
        $this->seoService->load($product);
        $seoForm = $this->seoService->getForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $seoForm->load(App::$app->request->post()) && $seoForm->validate()) {
            $this->productService->save($model, $product);
            $this->seoService->save($product);
            Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Product successful updated'));

            if (!Yii::$app->request->post('here-btn')) {
                return $this->redirect(['/catalog/view', 'id' => $product->section_id]);
            } else {
                return $this->redirect(['update', 'id' => $product->id]);
            }
        }

        $prices = Price::find()->all();

        $properties = Property::find()
            ->joinWith(['sectionProperties ps'])
            ->andWhere(['ps.section_id' => $product->section_id])
            ->all();

        return $this->render('update', [
            'product' => $product,
            'model' => $model,
            'properties' => $properties,
            'sections' => $this->sectionService->getSectionsTree(),
            'prices' => $prices,
            'seoService' => $this->seoService,
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
    public function actionDelete(int $id)
    {
        $product = Product::findOne($id);

        if ($product === null) {
            throw new NotFoundHttpException();
        }

        $section_id = $product->section_id;
        $product->delete();

        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Product successful deleted'));
        return $this->redirect(['/catalog/view', 'id' => $section_id]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionImageDelete(int $id)
    {
        $image = ProductImage::findOne($id);

        if ($image === null) {
            throw new NotFoundHttpException();
        }

        $product_id = $image->product_id;
        $image->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('catalog', 'Image successful deleted'));

        return $this->redirect(['update', 'id' => $product_id]);
    }
}