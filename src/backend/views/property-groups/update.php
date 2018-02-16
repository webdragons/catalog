<?php

use common\enums\PropertyTypesEnum;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyGroupForm $model
 * @var \bulldozer\catalog\common\ar\PropertyGroup $propertyGroup
 */

$this->title = Yii::t('catalog', 'Update properties group: {name}', ['name' => $propertyGroup->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Property groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $propertyGroup->name, 'url' => ['view', 'id' => $propertyGroup->id]];
$this->params['breadcrumbs'][] = Yii::t('catalog', 'Update');
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
                    'model' => $model,
                    'isNew' => false,
                ]) ?>
            </div>
        </section>
    </div>
</div>
