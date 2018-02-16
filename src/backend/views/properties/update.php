<?php

use bulldozer\catalog\common\enums\PropertyTypesEnum;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var \bulldozer\catalog\backend\forms\PropertyForm $model
 * @var \bulldozer\catalog\common\ar\Property $property
 * @var array $groups
 */

$this->title = Yii::t('catalog', 'Update property: {name}', ['name' => $property->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('catalog', 'Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $property->name, 'url' => ['view', 'id' => $property->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
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
                <?php if ($property->type == PropertyTypesEnum::TYPE_ENUM): ?>
                    <a class="btn btn-default" href="<?= Url::to(['enum-list', 'property_id' => $property->id]) ?>">
                        <?= Yii::t('catalog', 'Update property values') ?>
                    </a>
                <?php endif ?>

                <?= $this->render('_form', [
                    'model' => $model,
                    'groups' => $groups,
                    'isNew' => false,
                ]) ?>
            </div>
        </section>
    </div>
</div>
