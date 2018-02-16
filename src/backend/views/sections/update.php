<?php

use yii\helpers\Html;

/**
 * @var \bulldozer\catalog\backend\forms\SectionForm $model
 * @var array $sections
 * @var \bulldozer\files\models\Watermark[] $watermarks
 * @var array $properties
 * @var bool $isNew
 * @var \bulldozer\seo\backend\services\SeoService $seoService
 * @var \bulldozer\catalog\common\ar\Section $section
 */

$parents = $section->parents()->all();

foreach ($parents as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['view', 'id' => $parent->id]];
}

$this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => ['view', 'id' => $section->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

$this->title = Yii::t('catalog', 'Update section: {name}', ['name' => $section->name]);
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
                    'seoService' => $seoService,
                    'model' => $model,
                    'isNew' => false,
                    'sections' => $sections,
                    'properties' => $properties
                ]) ?>
            </div>
        </section>
    </div>
</div>