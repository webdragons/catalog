<?php

namespace bulldozer\catalog\backend;

use bulldozer\App;
use bulldozer\base\BackendModule;
use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Module
 * @package bulldozer\catalog\backend
 */
class Module extends BackendModule
{
    public $defaultRoute = 'sections';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        if (empty(App::$app->i18n->translations['catalog'])) {
            App::$app->i18n->translations['catalog'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/../messages',
            ];
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function createController($route)
    {
        $validRoutes = ['products', 'sections', 'currencies', 'prices', 'products-list', 'markup', 'properties', 'property-groups'];
        $isValidRoute = false;

        foreach ($validRoutes as $validRoute) {
            if (strpos($route, $validRoute) === 0) {
                $isValidRoute = true;
                break;
            }
        }

        return (empty($route) or $isValidRoute)
            ? parent::createController($route)
            : parent::createController("{$this->defaultRoute}/{$route}");
    }

    /*
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $action->controller->view->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Catalog'), 'url' => ['/catalog']];

        return parent::beforeAction($action);
    }

    /**
     * @return array
     */
    public function getMenuItems(): array
    {
        $moduleId = isset(App::$app->controller->module) ? App::$app->controller->module->id : '';
        $controllerId = isset(App::$app->controller) ? App::$app->controller->id : '';

        return [
            [
                'label' => Yii::t('catalog', 'Catalog'),
                'icon' => 'fa fa-book',
                'child' => [
                    [
                        'label' => Yii::t('catalog', 'Assortment'),
                        'icon' => 'fa fa-product-hunt',
                        'url' => ['/catalog'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['sections', 'products'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Product groups'),
                        'icon' => 'fa fa-object-group',
                        'url' => ['/catalog/products-list'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['products-list'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Price types'),
                        'icon' => 'fa fa-money',
                        'url' => ['/catalog/prices'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['prices'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Markup'),
                        'icon' => 'fa fa-caret-square-o-up',
                        'url' => ['/catalog/markup'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['markup'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Currencies'),
                        'icon' => 'fa fa-usd',
                        'url' => ['/catalog/currencies'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['currencies'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Properties'),
                        'icon' => 'fa fa-bandcamp',
                        'url' => ['/catalog/properties'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['properties'])),
                    ],
                    [
                        'label' => Yii::t('catalog', 'Property groups'),
                        'icon' => 'fa fa-bandcamp',
                        'url' => ['/catalog/property-groups'],
                        'rules' => ['catalog_manage'],
                        'active' => $moduleId == 'catalog' && (in_array($controllerId, ['property-groups'])),
                    ],
                ],
            ],
        ];
    }
}