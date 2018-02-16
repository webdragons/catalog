<?php

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use yii\widgets\ActiveForm;

/**
 * @var \bulldozer\catalog\backend\forms\CurrencyForm $model
 * @var \yii\web\View $this
 * @var bool $isNew
 */
?>
<?php $form = ActiveForm::begin(); ?>

<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?= $form->errorSummary($model) ?>
    </div>
<?php endif ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>