<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\jui\JuiAsset;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\ProductListForm $model
 * @var \bulldozer\catalog\common\ar\ProductList $list
 */

JuiAsset::register($this);

$this->title = Yii::t('catalog', 'Update product list: {name}', ['name' => $list->name]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Product lists'), 'url' => ['/catalog/products-list']];

$this->params['breadcrumbs'][] = $this->title;

$formName = $model->formName();

$script = <<< JS
    function removeCallback() {
        $(this).closest('div.form-group').remove();
    }
    
    function addCallbacks(context) {
        $('button.remove-btn', context).click(removeCallback);
        $('#products').sortable({
            axis: "y"
        });
    }

    $('span.add-btn').click(function() {
        var template = $('<li class="ui-sortable-handle"><div class="form-group">'
        + '<div class="input-group">'
        + '<input type="text" name="$formName\[products][]" class="form-control" />'
        + '<span class="input-group-btn"><button class="btn btn-default remove-btn" type="button">'
        + '<i class="fa fa-times" aria-hidden="true"></i></button></span>'
        + '</div>'
        + '</div></li>');
        
        addCallbacks(template);
        
        template.appendTo($('#products'));
    });
    
    addCallbacks(jQuery(this));
JS;

$this->registerJs($script, yii\web\View::POS_READY);

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
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <?php if ($model->hasErrors()): ?>
                    <div class="alert alert-danger">
                        <?= $form->errorSummary($model) ?>
                    </div>
                <?php endif ?>

                <?= $form->field($model, 'active')->checkBox() ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'more_url')->textInput() ?>

                <?= Html::label($model->attributeLabels()['products']) ?>

                <ul id="products">
                    <?php foreach ($model->products as $product_id): ?>
                        <li>
                            <?= $form->field($model, 'products[]', [
                                'template' => '{label}
                                <div class="input-group">
                                    {input}
                                    <span class="input-group-btn">
                                        <button class="btn btn-default remove-btn" type="button">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                </div>
                                {hint}
                                {error}',
                            ])->textInput(['value' => $product_id])->label(false) ?>
                        </li>
                    <?php endforeach ?>
                </ul>

                <span class="btn btn-default add-btn">Добавить</span>

                <div class="form-group" style="margin-top: 10px;">
                    <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </section>
    </div>
</div>
