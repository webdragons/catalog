<?php

/**
 * @var \bulldozer\catalog\frontend\ar\Section[] $sections
 * @var \bulldozer\catalog\frontend\ar\Product[] $products
 * @var \yii\web\View $this
 * @var \yii\data\Pagination $pagination
 * @var \bulldozer\seo\frontend\services\SeoService $seoService
 * @var \bulldozer\catalog\frontend\services\FilterService $filterService
 */

use bulldozer\catalog\frontend\widgets\ItemsPerPageWidget;
use bulldozer\catalog\frontend\widgets\SortWidget;

?>
<h1><?= $seoService->getH1() ?></h1>

<?= $this->render('_sections', [
    'sections' => $sections,
]) ?>

<div class="row">
    <div class="col-md-4">
        <?= SortWidget::widget() ?>
    </div>

    <div class="col-md-4 col-md-offset-4 text-right">
        <?= ItemsPerPageWidget::widget() ?>
    </div>
</div>

<?= $this->render('_products', [
    'products' => $products,
    'pagination' => $pagination,
    'filterService' => $filterService,
]) ?>

