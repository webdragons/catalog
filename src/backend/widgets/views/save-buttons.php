<?php
/**
 * @var bool $isNew
 */

use yii\helpers\Html;

?>
<div class="form-group" style="margin-top: 10px;">
    <?= Html::submitButton($isNew ? Yii::t('catalog', 'Create') : Yii::t('catalog', 'Update'),
        ['class' => $isNew ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?= Html::submitInput($isNew ? Yii::t('catalog', 'Create and stay here') : Yii::t('catalog', 'Update and stay here'),
        ['class' => $isNew ? 'btn btn-success' : 'btn btn-primary', 'name' => 'here-btn']) ?>
</div>