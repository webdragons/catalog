<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\common\ar\Property $property
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('catalog', 'Values of property: {name}', ['name' => $property->name]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $property->name;
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
                    <?= Html::a(Yii::t('catalog', 'Create property value'), ['create-enum-value', 'property_id' => $property->id], ['class' => 'btn btn-success']) ?>
                </p>

                <div class="table-responsive">
                    <?php Pjax::begin(); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                [
                                    'label' => Yii::t('catalog', 'Value'),
                                    'attribute' => 'value',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update} {delete}',
                                    'urlCreator' => function($action, $model, $key, $index) {
                                        if ($action == 'update')
                                            return Url::to(['update-enum-value', 'id' => $model->id]);
                                        else if ($action == 'delete')
                                            return Url::to(['delete-enum-value', 'id' => $model->id]);
                                    },
                                ],
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </section>
    </div>
</div>
