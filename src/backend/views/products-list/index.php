<?php

use bulldozer\catalog\common\ar\ProductList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('catalog', 'Product lists');

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                </div>

                <h2 class="panel-title"><?= Html::encode($this->title) ?></h2>
            </header>

            <div class="panel-body">
                <div class="table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            [
                                'label' => Yii::t('catalog', 'ID'),
                                'attribute' => 'id',
                            ],
                            [
                                'label' => Yii::t('catalog', 'Active'),
                                'attribute' => 'active',
                                'format' => 'boolean',
                            ],
                            [
                                'label' => Yii::t('catalog', 'Name'),
                                'attribute' => 'name',
                            ],
                            [
                                'label' => Yii::t('catalog', 'More url'),
                                'attribute' => 'more_url',
                            ],
                            [
                                'label' => Yii::t('catalog', 'Products'),
                                'content' => function(ProductList $model) {
                                    $res = Html::beginTag('ul');

                                    if (count($model->productsList) > 0) {
                                        foreach ($model->productsList as $product) {
                                            $res .= Html::beginTag('li')
                                                . Html::a($product->name, Url::to(['/catalog/products/update', 'id' => $product->id]))
                                                . Html::endTag('li');
                                        }
                                    }

                                    return  $res . Html::endTag('ul');

                                }
                            ],
                            [
                                'label' => Yii::t('app', 'Created at'),
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                            ],
                            [
                                'label' => Yii::t('app', 'Updated at'),
                                'attribute' => 'updated_at',
                                'format' => 'datetime',
                            ],
                            [
                                'label' => Yii::t('app', 'Updater'),
                                'attribute' => 'updater.email'
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update}',
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </section>
    </div>
</div>
