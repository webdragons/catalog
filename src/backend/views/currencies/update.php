<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\CurrencyForm $model
 * @var \bulldozer\catalog\common\ar\Currency $currency
 */

$this->title = Yii::t('catalog', 'Update currency: {name}', ['name' => $currency->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Currencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $currency->name;
$this->params['breadcrumbs'][] = Yii::t('catalog', 'Update');
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
                    'isNew' => false,
                ]) ?>
            </div>
        </section>
    </div>
</div>
