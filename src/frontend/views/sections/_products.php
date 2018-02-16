<?php

/**
 * @var \bulldozer\catalog\frontend\ar\Product[] $products
 * @var \bulldozer\catalog\frontend\services\FilterService $filterService
 * @var \yii\data\Pagination $pagination
 */

use bulldozer\catalog\frontend\widgets\FilterWidget;
use yii\widgets\LinkPager;

?>
<div class="row">
    <?php if (isset($filterService)): ?>
        <div class="col-md-3">
            <?= FilterWidget::widget(['filterService' => $filterService]) ?>
        </div>
    <?php endif ?>

    <div class="col-md-9">
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-3">
                    <a href="<?= $product->viewUrl ?>">
                        <?php if ($product->image): ?>
                            <div class="catalog__item__image">
                                <img src="<?= $product->image->getThumbnail(320, 240) ?>" alt="" style="width: 320px;">
                            </div>
                        <?php else: ?>
                            <div class="catalog__item__image">
                            </div>
                        <?php endif ?>
                    </a>

                    <div class="catalog__item__info">
                        <div class="catalog__item__info__title">
                            <?= $product->name ?>
                        </div>
                    </div>

                    <?php if ($product->getPrice()): ?>
                        <div>
                            <div>
                                <?php if ($product->getPrice()->isWithDiscount()): ?>
                                    <span>
                                        <?= $product->getPrice()->getPrintOldPrice() ?>
                                    </span>
                                    <br>
                                <?php endif ?>
                                <p class="catalog__item__info__price-title">
                                    Цена:
                                </p>
                                <p>
                                    <?= $product->getPrice()->getPrintPrice() ?>
                                </p>
                            </div>

                            <a href="<?= $product->viewUrl ?>" class="btn btn-primary">
                                Подробнее
                            </a>
                        </div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>

<div style="text-align: center;">
    <?= LinkPager::widget(['pagination' => $pagination]) ?>
</div>