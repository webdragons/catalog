<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\ProductForm $model
 * @var \bulldozer\catalog\common\ar\Property[] $properties
 * @var array $sections
 * @var \bulldozer\catalog\common\ar\Price[] $prices
 * @var \bulldozer\catalog\common\ar\Product $product
 */

$this->title = Yii::t('catalog', 'Update product: {name}', ['name' => $product->name]);

foreach ($product->section->parents()->all() as $parent)
{
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['/catalog/view', 'id' => $parent->id]];
}

$this->params['breadcrumbs'][] = ['label' => $product->section->name, 'url' => ['/catalog/view', 'id' => $product->section->id]];

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
                <?= $this->render('_form', [
                    'model' => $model,
                    'properties' => $properties,
                    'sections' => $sections,
                    'prices' => $prices,
                    'seoService' => $seoService,
                    'isNew' => false,
                ]) ?>
            </div>
        </section>
    </div>
</div>
