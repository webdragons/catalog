<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $sectionsDataProvider
 * @var yii\data\ActiveDataProvider $productsDataProvider
 * @var \bulldozer\catalog\common\ar\Section $section
 */

if ($section !== null) {
    foreach ($section->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['view', 'id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = $section->name;
    $this->title = $section->name;
} else {
    $this->title = Yii::t('catalog', 'Assortment');
}
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
                    <?php if ($section !== null): ?>
                        <?= Html::a(Yii::t('catalog', 'Create product'), ['/catalog/products/create', 'section_id' => $section->id], ['class' => 'btn btn-success']) ?>
                    <?php endif ?>

                    <?php if ($section): ?>
                        <?= Html::a(Yii::t('catalog', 'Create subsection'), ['create', 'parent_id' => $section->id], ['class' => 'btn btn-success']) ?>
                    <?php else: ?>
                        <?= Html::a(Yii::t('catalog', 'Create section'), ['create'], ['class' => 'btn btn-success']) ?>
                    <?php endif ?>
                </p>

                <?php if ($sectionsDataProvider->count > 0): ?>
                    <div class="table-responsive">
                        <?= GridView::widget([
                            'dataProvider' => $sectionsDataProvider,
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
                                    'content' => function($model) {
                                        return Html::a($model->name, ['view', 'id' => $model->id]);
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
                                    'label' => Yii::t('app', 'Creator'),
                                    'attribute' => 'creator.email'
                                ],
                                [
                                    'label' => Yii::t('app', 'Updater'),
                                    'attribute' => 'updater.email'
                                ],
                                [
                                    'label' => Yii::t('catalog', 'Display order'),
                                    'attribute' => 'sort',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update} {delete}',
                                ],
                            ],
                        ]); ?>
                    </div>
                <?php endif ?>

                <?php if ($productsDataProvider !== null && $productsDataProvider->count > 0): ?>
                    <div class="table-responsive">
                        <?= GridView::widget([
                            'dataProvider' => $productsDataProvider,
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
                                    'label' => Yii::t('catalog', 'Display order'),
                                    'attribute' => 'sort',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update} {delete}',
                                    'urlCreator' => function($action, $model, $key, $index) {
                                        if ($action == 'update')
                                            return Url::to(['/catalog/products/update', 'id' => $model->id]);
                                        else if ($action == 'delete')
                                            return Url::to(['/catalog/products/delete', 'id' => $model->id]);
                                    },
                                ],
                            ],
                        ]); ?>
                    </div>
                <?php endif ?>
            </div>
        </section>
    </div>
</div>
