<?php

use yii\helpers\Html;

/**
 * @var \bulldozer\catalog\backend\forms\SectionForm $model
 * @var array $sections
 * @var \bulldozer\files\models\Watermark[] $watermarks
 * @var array $properties
 * @var bool $isNew
 * @var \bulldozer\seo\backend\services\SeoService $seoService
 * @var \bulldozer\catalog\common\ar\Section $parentSection
 */

$this->title = Yii::t('catalog', 'Create section');

if ($parentSection !== null) {
    foreach ($parentSection->parents()->all() as $parent) {
        $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['view', 'id' => $parent->id]];
    }

    $this->params['breadcrumbs'][] = ['label' => $parentSection->name, 'url' => ['view', 'id' => $parentSection->id]];
}

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
                <?= $this->render('_form', [
                    'seoService' => $seoService,
                    'model' => $model,
                    'isNew' => true,
                    'sections' => $sections,
                    'properties' => $properties,
                    'section' => null,
                ]) ?>
            </div>
        </section>
    </div>
</div>