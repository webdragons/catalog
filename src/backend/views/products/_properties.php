<?php

/**
 * @var \yii\bootstrap\ActiveForm $form
 * @var \bulldozer\catalog\backend\forms\ProductForm $model
 * @var \bulldozer\catalog\common\ar\Property[] $properties
 * @var array $brands
 */

use bulldozer\catalog\common\enums\PropertyTypesEnum;
use yii\helpers\ArrayHelper;

?>
<?php foreach ($properties as $property): ?>
    <?php if ($property->type == PropertyTypesEnum::TYPE_ENUM): ?>
        <?php if ($property->multiple == 0): ?>
            <?= $form->field($model, 'properties[' . $property->id . ']')
                ->dropDownList(ArrayHelper::map($property->enums, 'id', 'value'), [
                    'prompt' => Yii::t('catalog', 'Not selected')
                ])
                ->label($property->name) ?>
        <?php else: ?>
            <label for=""><?= $property->name ?></label>

            <?= $form->field($model, 'properties[' . $property->id . ']')
                ->checkboxList(ArrayHelper::map($property->enums, 'id', 'value'))
                ->label(false) ?>
        <?php endif ?>
    <?php elseif ($property->type == PropertyTypesEnum::TYPE_BOOLEAN): ?>
        <?= $form->field($model, 'properties[' . $property->id . ']')
            ->dropDownList([1 => Yii::t('catalog', 'Yes'), 0 => Yii::t('catalog', 'No')], [
                'prompt' => Yii::t('catalog', 'Not selected')
            ])
            ->label($property->name) ?>
    <?php elseif ($property->type == PropertyTypesEnum::TYPE_URL): ?>
        <label for=""><?= $property->name ?></label>
        <table class="table no-border">
            <tbody>
            <tr>
                <td><?= $form->field($model, 'properties[' . $property->id . '][name]')->textInput()->label(Yii::t('catalog', 'Name')) ?></td>
                <td><?= $form->field($model, 'properties[' . $property->id . '][link]')->textInput()->label(Yii::t('catalog', 'Link')) ?></td>
            </tr>
            </tbody>
        </table>
    <?php else: ?>
        <?php if ($property->multiple == 0): ?>
            <?= $form->field($model, 'properties[' . $property->id . ']')->textInput()->label($property->name) ?>
        <?php else: ?>
            <label for=""><?= $property->name ?></label>
            <?php
            $key = 0;
            ?>
            <?php if (isset($model->properties[$property->id]) && is_array($model->properties[$property->id])): ?>
                <?php foreach ($model->properties[$property->id] as $key => $val): ?>
                    <?= $form->field($model, 'properties[' . $property->id . '][' . $key . ']')
                        ->textInput()
                        ->label(false) ?>
                <?php endforeach ?>
            <?php endif ?>

            <?php for ($i = $key + 1; $i < ($key + 4); $i++): ?>
                <?= $form->field($model, 'properties[' . $property->id . '][' . $i . ']')
                    ->textInput()
                    ->label(false) ?>
            <?php endfor ?>
        <?php endif ?>
    <?php endif ?>
<?php endforeach ?>