<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyEnumForm $model
 * @var bool $isNew
 */
?>
<?php $form = ActiveForm::begin(); ?>

<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?= $form->errorSummary($model) ?>
    </div>
<?php endif ?>

<?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

<div class="form-group">
    <?= Html::submitButton($isNew ? Yii::t('app', 'Create') : Yii::t('catalog', 'Update'),
        ['class' => $isNew ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>