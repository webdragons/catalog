<?php
/**
 * @var \bulldozer\catalog\backend\forms\SectionForm $model
 * @var array $sections
 * @var \bulldozer\files\models\Watermark[] $watermarks
 * @var array $properties
 * @var bool $isNew
 * @var \bulldozer\seo\backend\services\SeoService $seoService
 * @var \bulldozer\catalog\common\ar\Section $section
 */

use bulldozer\catalog\backend\widgets\SaveButtonsWidget;
use bulldozer\seo\backend\widgets\SeoUpdateWidget;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$script = <<< JS
    $('div.watermarks img').each(function() {
       if ($(this).attr('data-id') == $('#formcatalogsection-watermark_id').val())
           $(this).css('border', '2px solid #ff0000');
    });

    $('div.watermarks img').click(function() {
        $('div.watermarks img').css('border', 'none');
        $('#formcatalogsection-watermark_id').val($(this).attr('data-id'));
        $(this).css('border', '2px solid #ff0000');
    });
JS;

$this->registerJs($script, View::POS_READY);

?>
<style>
    div.watermarks img {
        display: inline-block;
        margin: 10px;
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

<?= $form->field($model, 'parent_id')->dropDownList($sections, [
    'prompt' => 'Не выбрано',
]) ?>

<?php if ($section && $section->image): ?>
    <label for="">Текущее изображение</label>
    <img src="<?= $section->image->getThumbnail(219, 108) ?>" class="img-responsive"/>
    <p>При загрузке нового изображения текущее будет удалено.</p>
<?php endif ?>

<?= $form->field($model, 'image')->fileInput(['accept' => 'image/*']) ?>

<?= $form->field($model, 'watermark_id')->hiddenInput()->label(false) ?>

<?= $form->field($model, 'properties')->checkboxList($properties) ?>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#seo" aria-controls="home" role="tab" data-toggle="tab">SEO</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="seo">
        <?= SeoUpdateWidget::widget(['seoService' => $seoService, 'form' => $form]) ?>
    </div>
</div>

<?= SaveButtonsWidget::widget(['isNew' => $isNew]) ?>

<?php ActiveForm::end(); ?>
