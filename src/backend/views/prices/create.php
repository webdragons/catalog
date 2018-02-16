<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\controllers\PriceForm $model
 * @var \bulldozer\catalog\common\ar\Currency[] $currencies
 * @var \bulldozer\catalog\common\ar\Price[] $prices
 */

$this->title = Yii::t('catalog', 'Create price type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Price types'), 'url' => ['index']];
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
                    'currencies' => $currencies,
                    'prices' => $prices,
                    'isNew' => true,
                ]) ?>
            </div>
        </section>
    </div>
</div>
