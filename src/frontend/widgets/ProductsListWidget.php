<?php

namespace bulldozer\catalog\frontend\widgets;

use bulldozer\App;
use bulldozer\catalog\frontend\ar\ProductList;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Class ProductsListWidget
 * @package bulldozer\catalog\frontend\widgets
 */
class ProductsListWidget extends Widget
{
    /**
     * @var int
     */
    public $list_id;

    /**
     * @var string
     */
    public $view_path;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        if (!is_numeric($this->list_id)) {
            throw new InvalidConfigException('list_id must be number');
        }
    }

    /**
     * @inheritdoc
     */
    public function run(): string
    {
        $list = ProductList::find()
            ->where(['id' => $this->list_id])
            ->one();

        if ($list === null) {
            $list = new ProductList([
                'id' => $this->list_id,
                'active' => 0,
                'name' => 'List #' . $this->list_id,
            ]);

            if (!$list->save()) {
                throw new Exception('Cant save new list!');
            }
        }

        $products = $list->getProductsList()
            ->with(['prices', 'images', 'discounts', 'prices.priceType', 'prices.priceType.currency', 'section'])
            ->all();

        if ($list->active == 1 || App::$app->user->can('catalog_manage')) {
            return $this->render($this->view_path, [
                'list' => $list,
                'products' => $products
            ]);
        } else {
            return '';
        }
    }
}