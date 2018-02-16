<?php

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyGroupForm $model
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

<?= $form->field($model, 'sort')->textInput(['type' => 'integer']) ?>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>