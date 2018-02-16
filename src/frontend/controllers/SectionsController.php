<?php

namespace bulldozer\catalog\frontend\controllers;

use bulldozer\App;
use bulldozer\catalog\frontend\ar\Product;
use bulldozer\catalog\frontend\ar\Section;
use bulldozer\catalog\frontend\services\FilterService;
use bulldozer\catalog\frontend\services\ItemsPerPageService;
use bulldozer\catalog\frontend\services\SortService;
use bulldozer\seo\frontend\services\SeoService;
use bulldozer\web\Controller;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

/**
 * Class SectionsController
 * @package bulldozer\catalog\frontend\controllers
 */
class SectionsController extends Controller
{
    /**
     * @var ItemsPerPageService
     */
    private $itemsPerPageService;

    /**
     * @var SortService
     */
    private $sortService;

    /**
     * @var FilterService
     */
    private $filterService;

    /**
     * SectionsController constructor.
     * @param $id
     * @param $module
     * @param ItemsPerPageService $itemsPerPageService
     * @param SortService $sortService
     * @param FilterService $filterService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ItemsPerPageService $itemsPerPageService,
        SortService $sortService,
        FilterService $filterService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->itemsPerPageService = $itemsPerPageService;
        $this->sortService = $sortService;
        $this->filterService = $filterService;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $sections = Section::find()
            ->roots()
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $query = Product::find()
            ->select([
                Product::tableName() . '.*'
            ])
            ->groupBy([Product::tableName() . '.id']);

        $this->sortService->setQuery($query);
        $this->sortService->applySort(App::$app->request->getQueryParams());

        $this->filterService->setQuery($query);
        $this->filterService->applyFilter();

        /** @var Pagination $pagination */
        $pagination = App::createObject([
            'class' => Pagination::class,
            'defaultPageSize' => $this->itemsPerPageService->getValue(),
            'totalCount' => $query->count(),
            'forcePageParam' => false,
        ]);

        $query->offset($pagination->offset)
            ->limit($pagination->limit);

        $products = $query->all();

        /** @var SeoService $seoService */
        $seoService = App::createObject([
            'class' => SeoService::class,
            'pagination' => $pagination,
            'defaultValues' => [
                'title' => Yii::t('catalog', 'Catalog'),
                'h1' => Yii::t('catalog', 'Catalog'),
            ],
        ]);

        return $this->render('index', [
            'sections' => $sections,
            'products' => $products,
            'pagination' => $pagination,
            'seoService' => $seoService,
            'filterService' => $this->filterService,
        ]);
    }

    /**
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView(string $slug)
    {
        $section = Section::findOne(['slug' => $slug]);

        if ($section === null) {
            throw new NotFoundHttpException();
        }

        $sections = $section
            ->children(1)
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $allChildsIds = $section->children()->asArray()->select(['id'])->column();
        $allChildsIds[] = $section->id;

        $query = Product::find()
            ->select([Product::tableName() . '.*'])
            ->andWhere([
                Product::tableName() . '.section_id' => $allChildsIds,
            ])
            ->groupBy([Product::tableName() . '.id']);

        $this->sortService->setQuery($query);
        $this->sortService->applySort(App::$app->request->getQueryParams());

        $this->filterService->setSection($section);
        $this->filterService->setQuery($query);
        $this->filterService->applyFilter();

        /** @var Pagination $pagination */
        $pagination = App::createObject([
            'class' => Pagination::class,
            'defaultPageSize' => $this->itemsPerPageService->getValue(),
            'totalCount' => $query->count(),
            'forcePageParam' => false,
        ]);

        $query->offset($pagination->offset)
            ->limit($pagination->limit);

        $products = $query->all();

        /** @var SeoService $seoService */
        $seoService = App::createObject([
            'class' => SeoService::class,
            'model' => $section,
            'pagination' => $pagination,
            'defaultValues' => [
                'h1' => $section->name,
                'title' => $section->name,
            ],
        ]);

        return $this->render('view', [
            'section' => $section,
            'sections' => $sections,
            'products' => $products,
            'pagination' => $pagination,
            'seoService' => $seoService,
            'filterService' => $this->filterService,
        ]);
    }
}