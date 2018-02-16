<?php

use bulldozer\catalog\frontend\widgets\forms\ItemsPerPageForm;
use yii\bootstrap\ActiveForm;

/**
 * @var \bulldozer\catalog\frontend\widgets\forms\ItemsPerPageForm $model
 */

$this->registerJS( <<< EOT_JS
     $('#itemsperpageform-itemsperpage').on('change', function(ev) {
         $(this).closest('form').submit();
    });
EOT_JS
);
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'itemsPerPage', [
    'template' => '{label} {input} {error}',
    'inputOptions' => [
        'style' => 'width: 64px; display: inline-block;'
    ],
])->dropDownList(ItemsPerPageForm::getCounts()) ?>

<?php ActiveForm::end(); ?>
