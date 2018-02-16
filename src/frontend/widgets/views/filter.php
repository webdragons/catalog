<?php

/**
 * @var \yii\web\View $this
 * @var \bulldozer\catalog\frontend\widgets\forms\FilterForm $filterForm
 * @var array $brands
 * @var array $pricesRange
 * @var \bulldozer\catalog\frontend\entities\FilterProperty[] $properties
 * @var \bulldozer\catalog\frontend\services\FilterService $filterService
 */

use bulldozer\catalog\common\enums\PropertyTypesEnum;
use yii\jui\JuiAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

$script = <<< JS
    var timer;

    function showFilterBtn(context) {
        $('.apply-filters-float-btn').show();
        $('.apply-filters-float-btn').offset({top: context.offset().top - 30});

        if (typeof timer !== 'undefined') {
            clearTimeout(timer);
        }

        timer = setTimeout(function() {
            $('.apply-filters-float-btn').fadeOut();            
        }, 10000);
    }

    $('input[type=checkbox]').click(function() {
        showFilterBtn($(this));
    });
    
    $('input[type=text],input[type=number]').change(function() {
        showFilterBtn($(this));
    });
    
    $('.apply-filters-float-btn').click(function() {
        $(this).closest('form').submit();
    });

    $('div.property label').click(function() {
        var items = $(this).parent().find('div.items');
        
        if (items.hasClass('in')) {
            items.slideUp(500, function() {
                $(this).removeClass('in');
            });
                
            $(this).parent().find('i').removeClass('rotated');
        } else {
            items.slideDown({
                duration: 500,
                start: function() {
                    $(this).height(0);
                    $(this).addClass('in');    
                }
            });
            
            $(this).parent().find('i').addClass('rotated');
        }
    });
    
    $('#filter-price-from, #filter-price-to').change(function() {
        $('#price-range').slider({
            values: [parseInt($('#filter-price-from').val()), parseInt($('#filter-price-to').val())]
        });
    });

    $('#price-range').slider({
        range: true,
        min: parseInt($('#filter-price-from').attr('data-val')),
        max: parseInt($('#filter-price-to').attr('data-val')),
        values: [parseInt($('#filter-price-from').val()), parseInt($('#filter-price-to').val())],
        slide: function( event, ui ) {
            $('#filter-price-from').val(ui.values[0]).trigger('change');
            $('#filter-price-to').val(ui.values[1]).trigger('change');
        }
    });
JS;

$this->registerJs($script, View::POS_READY);
JuiAsset::register($this);

?>
<div class="filter">
    <form method="get">
        <div class="apply-filters-float-btn">Показать<span class="corner"></span></div>

        <div class="property">
            <label for="">
                <span>
                    <i class="fa fa-angle-down rotated" aria-hidden="true"></i>
                    Цена
                </span>
            </label>

            <div class="items in" style="overflow: visible;">
                <div id="price-range" class="slider"></div>

                <?= Html::activeInput('text', $filterForm, 'price[from]', [
                    'type' => 'number',
                    'class' => 'from-field',
                    'data-val' => (int) $pricesRange['min_price'],
                ]) ?>
                <?= Html::activeInput('text', $filterForm, 'price[to]', [
                    'type' => 'number',
                    'class' => 'to-field',
                    'data-val' => (int) $pricesRange['max_price'],
                ]) ?>

                <div class="clearfix"></div>
            </div>
        </div>

        <?php foreach ($properties as $property): ?>
            <div class="property">
                <label for="">
                    <span>
                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                        <?= $property->getName() ?>
                    </span>
                </label>

                <div class="items <?= $filterService->isActiveFilter('properties', $property->getId()) ? 'in' : '' ?>">
                    <?php if ($property->getType() == PropertyTypesEnum::TYPE_ENUM): ?>
                        <?= Html::activeCheckboxList(
                            $filterForm,
                            'properties[' . $property->getId() . ']',
                            ArrayHelper::map($property->getValues(), 'id', 'value'), [
                                'separator' => '<br>',
                            ]
                        ) ?>
                    <?php elseif ($property->getType() == PropertyTypesEnum::TYPE_BOOLEAN): ?>
                        <?= Html::activeCheckboxList($filterForm, 'properties[' . $property->getId() . ']', [
                            1 => 'Да',
                            0 => 'Нет',
                        ], [
                            'separator' => '<br>',
                        ]) ?>
                    <?php else: ?>
                        <?= Html::activeCheckboxList(
                            $filterForm,
                            'properties[' . $property->getId() . ']',
                            $property->getValues(),
                            [
                                'separator' => '<br>',
                            ]
                        ) ?>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>

        <div class="buttons">
            <input class="button button_orange" type="submit" value="Показать"/>
            <input name="reset" class="button button_orange" type="submit" value="Сбросить"/>
        </div>
    </form>
</div>
