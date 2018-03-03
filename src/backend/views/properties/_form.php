<?php

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyForm $model
 * @var array $groups
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

<?= $form->field($model, 'type')->dropDownList(PropertyTypesEnum::$list, [
    'prompt' => Yii::t('catalog', 'Not selected'),
]) ?>

<?= $form->field($model, 'sort')->textInput(['type' => 'integer']) ?>

<?= $form->field($model, 'multiple')->checkbox() ?>

<?= $form->field($model, 'filtered')->checkbox() ?>

<?= $form->field($model, 'group_id')->dropDownList($groups, [
    'prompt' => 'Не выбрано'
]) ?>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>