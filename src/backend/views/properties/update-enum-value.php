<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyEnumForm $model
 * @var \bulldozer\catalog\common\ar\Property $property
 */

$this->title = Yii::t('catalog', 'Update value for property: {name}', ['name' => $property->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $property->name, 'url' => ['update', 'id' => $property->id]];
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
                <?= $this->render('_enum_form', [
                    'model' => $model,
                    'isNew' => false,
                ]) ?>
            </div>
        </section>
    </div>
</div>
