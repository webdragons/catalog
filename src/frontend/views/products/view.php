<?php

/**
 * @var \yii\web\View $this
 * @var \bulldozer\catalog\frontend\ar\Product $product
 * @var \bulldozer\seo\frontend\services\SeoService $seoService
 * @var \bulldozer\catalog\frontend\entities\PropertyGroup[] $properties
 */

$section = $product->section;

/* @var \bulldozer\pages\common\ar\Section[] $parents */
$parents = $section->parents()->all();

foreach ($parents as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => $parent->viewUrl];
}

$this->params['breadcrumbs'][] = ['label' => $section->name, 'url' => $section->viewUrl];
$this->params['breadcrumbs'][] = ['label' => $seoService->getH1()];
?>
<div>
    <div class="row">
        <div class="col-md-11 col-md-offset-1">
            <h1>
                <?=$product->name?>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="product-card__image">
                <div class="slides">
                    <?php foreach ($product->images as $image): ?>
                        <img src="<?=$image->getThumbnail(720, 480, false, false)?>">
                    <?php endforeach ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <div class="product-card__text">
                <?php if (!$product->getPrice()): ?>
                    <p style="font-weight: bold">Товар недоступен для покупки</p>
                <?php else: ?>
                    <div class="product-card__price">
                        <div class="product-card__price__wrapper">
                            <div class="product-card__price__wrapper__price-wrapper">
                                <?php if ($product->getPrice()->isWithDiscount()): ?>
                                    <div class="product-card__price__wrapper__price-wrapper__prev">
                                <span>
                                    <?= $product->getPrice()->getOldPrice()->getFormattedValue() ?>
                                </span>
                                    </div>
                                <?php endif ?>

                                <div class="product-card__price__wrapper__price-wrapper__current">
                                    <?= $product->getPrice()->getPrice()->getFormattedValue() ?>
                                </div>
                            </div>

                            <?php if ($product->getPrice()->getDiff()->more(0)): ?>
                                <div class="product-card__price__wrapper__economy">
                                    Экономия<br />
                                    <div class="product-card__price__wrapper__economy__value">
                                        <?= $product->getPrice()->getDiff()->getFormattedValue() ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="product-card__price__button-wrapper" style="margin-top: 10px;">
                            <button class="btn btn-primary"
                                    href="#order-product-popup">
                                Заказать
                            </button>
                        </div>
                    </div>
                <?php endif ?>

                <div class="product-card__description">
                    <?= $product->description ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 20px;">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <?php if (count($properties)): ?>
                    <a href="#props" aria-controls="home" role="tab" data-toggle="tab">Характеристики</a>
                    <?php endif ?>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <?php if (count($properties)): ?>
                <div role="tabpanel" class="tab-pane active" id="props" style="padding-top: 10px;">
                    <ul>
                        <?php foreach ($properties as $propertyGroup): ?>
                            <?php if ($propertyGroup->isGroup()): ?>
                                <li style="list-style: none; margin-left: -20px; font-weight: bold;"><?= $propertyGroup->getName() ?></li>
                            <?php endif ?>

                            <?php foreach ($propertyGroup->getProperties() as $property): ?>
                                <li>
                                    <?= $property->getName() ?>: <?= $property->getValue() ?>
                                </li>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </ul>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
