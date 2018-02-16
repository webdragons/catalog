<?php
/**
 * @var \yii\web\View $this
 * @var string $priceSort
 * @var string $newSort
 * @var string $discountPercentSort
 * @var \bulldozer\catalog\frontend\widgets\SortWidget $widget
 */
?>
<div class="sort-order">
    Сортировать:
    <a href="<?= $widget->buildUrl('price') ?>" rel="nofollow">
        <?php if ($priceSort == 'desc'): ?>▾<?php elseif ($priceSort == 'asc'): ?>▴<?php endif ?> по цене
    </a>
    <a href="<?= $widget->buildUrl('new') ?>">
        <?php if ($newSort == 'desc'): ?>▾<?php elseif ($newSort == 'asc'): ?>▴<?php endif ?> по новизне
    </a>
    <a href="<?= $widget->buildUrl('discount_percent') ?>">
        <?php if ($discountPercentSort == 'desc'): ?>▾<?php elseif ($discountPercentSort == 'asc'): ?>▴<?php endif ?> по скидке
    </a>
</div>
