<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\MarkupForm $model
 * @var array $sections
 * @var array $prices
 */

$this->title = Yii::t('catalog', 'Markup');
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
                <?php $form = ActiveForm::begin(); ?>

                <?php if ($model->hasErrors()): ?>
                    <div class="alert alert-danger">
                        <?= $form->errorSummary($model) ?>
                    </div>
                <?php endif ?>

                <?= $form->field($model, 'price_id')->dropDownList($prices, [
                    'prompt' => Yii::t('app', 'Not selected'),
                ]) ?>

                <?= $form->field($model, 'sections')->dropDownList($sections, [
                    'multiple' => true,
                ]) ?>

                <?= $form->field($model, 'type')->radioList($model->getTypes()) ?>

                <?= $form->field($model, 'value')->textInput() ?>

                <?= $form->field($model, 'round')->checkbox() ?>

                <?= $form->field($model, 'updateDiscounts')->checkbox() ?>

                <div class="form-group" style="margin-top: 10px;">
                    <?= Html::submitButton(Yii::t('catalog', 'Recalculate'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </section>
    </div>
</div>

