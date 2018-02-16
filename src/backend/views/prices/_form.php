<?php

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\controllers\PriceForm $model
 * @var bool $isNew
 * @var \bulldozer\catalog\common\ar\Currency[] $currencies
 * @var \bulldozer\catalog\common\ar\Price[] $prices
 */
?>
<?php $form = ActiveForm::begin(); ?>

<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?= $form->errorSummary($model) ?>
    </div>
<?php endif ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'base')->checkbox() ?>

<?= $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map($currencies, 'id', 'name'), [
    'prompt' => 'Не выбрано',
]) ?>

<?php if ($isNew): ?>
    <?= $form->field($model, 'priceForCopy')->dropDownList(ArrayHelper::map($prices, 'id', 'name'), [
        'prompt' => 'Не выбрано',
    ])->hint(Yii::t('catalog', 'You can select the price type based on which a new price for all products will be filled.')) ?>

    <?= $form->field($model, 'extraCharge')->input(['type' => 'number'])
        ->hint(Yii::t('catalog', 'You can specify the value of the mark-up in percent. The new price will automatically be greater by the value of the mark-up')) ?>
<?php endif ?>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>
