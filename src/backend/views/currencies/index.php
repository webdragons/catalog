<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('catalog', 'Currencies');
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
                <p>
                    <?= Html::a(Yii::t('catalog', 'Create currency'), ['create'], ['class' => 'btn btn-success']) ?>
                </p>

                <div class="table-responsive">
                    <?php Pjax::begin(); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                [
                                    'label' => Yii::t('catalog', 'Name'),
                                    'attribute' => 'name',
                                ],
                                [
                                    'label' => Yii::t('catalog', 'Short name'),
                                    'attribute' => 'short_name',
                                ],
                                [
                                    'label' => Yii::t('catalog', 'Code'),
                                    'attribute' => 'code',
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
                                    'label' => Yii::t('app', 'Creator'),
                                    'attribute' => 'creator.email'
                                ],
                                [
                                    'label' => Yii::t('app', 'Updater'),
                                    'attribute' => 'updater.email'
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update} {delete}',
                                ],
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </section>
    </div>
</div>
