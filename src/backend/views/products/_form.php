<?php

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use bulldozer\seo\backend\widgets\SeoUpdateWidget;
use dosamigos\ckeditor\CKEditor;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\ProductForm $model
 * @var array $sections
 * @var \bulldozer\catalog\common\ar\Price[] $prices
 * @var \bulldozer\catalog\common\ar\Property[] $properties
 * @var bool $isNew
 * @var \bulldozer\seo\backend\services\SeoService $seoService
 */
?>
<style>
    table.no-border tr:first-child td {
        border-top: none;
    }
</style>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?php if ($model->hasErrors()): ?>
    <div class="alert alert-danger">
        <?= $form->errorSummary($model) ?>
    </div>
<?php endif ?>

<?= $form->field($model, 'active')->checkbox() ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>

<?= $form->field($model, 'section_id')->dropDownList($sections, [
    'prompt' => 'Не выбрано',
]) ?>

<?= $form->field($model, 'description')->widget(CKEditor::className(), [
    'options' => ['rows' => 12],
]) ?>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#prices" aria-controls="home" role="tab"
                                              data-toggle="tab">Цены</a></li>
    <li role="presentation"><a href="#discounts" aria-controls="home" role="tab" data-toggle="tab">Скидки</a></li>
    <li role="presentation"><a href="#images" aria-controls="home" role="tab" data-toggle="tab">Изображения</a></li>
    <li role="presentation"><a href="#seo" aria-controls="home" role="tab" data-toggle="tab">SEO</a></li>
    <li role="presentation"><a href="#props" aria-controls="home" role="tab" data-toggle="tab">Характеристики</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="prices">
        <?php foreach ($prices as $price): ?>
            <?= $form->field($model, 'prices[' . $price->id . ']')->textInput()->label($price->name) ?>
        <?php endforeach ?>
    </div>

    <div role="tabpanel" class="tab-pane" id="discounts">
        <?php foreach ($prices as $price): ?>
            <?= $form->field($model, 'discounts[' . $price->id . ']')->textInput()->label($price->name) ?>
        <?php endforeach ?>
    </div>

    <div role="tabpanel" class="tab-pane" id="images">
        <div class="row">
            <?php foreach ($model->uploadedImages as $uploadedImage): ?>
                <div class="col-md-2 text-center">
                    <img src="<?= $uploadedImage->file->getThumbnail(219, 108) ?>" alt="" class="img-responsive"/>
                    <a href="<?= \yii\helpers\Url::to([
                        '/catalog/products/image-delete',
                        'id' => $uploadedImage->id
                    ]) ?>" data-confirm="Вы уверены, что хотите удалить?">Удалить</a>
                </div>
            <?php endforeach ?>
        </div>

        <?= $form->field($model, 'images[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
    </div>

    <div role="tabpanel" class="tab-pane" id="seo">
        <?= SeoUpdateWidget::widget(['form' => $form, 'seoService' => $seoService]) ?>
    </div>

    <div role="tabpanel" class="tab-pane" id="props">
        <?= $this->render('_properties', [
            'form' => $form,
            'model' => $model,
            'properties' => $properties,
        ]) ?>
    </div>
</div>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>
