<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\traits\UsersRelationsTrait;
use bulldozer\db\ActiveRecord;
use bulldozer\users\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_prices}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $name
 * @property integer $base
 * @property integer $currency_id
 *
 * @property User $creator
 * @property User $updater
 * @property Currency $currency
 */
class Price extends ActiveRecord
{
    use UsersRelationsTrait;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_prices}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'updater_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $productPrices = ProductPrice::find()->where(['price_id' => $this->id])->all();

        foreach ($productPrices as $productPrice) {
            $productPrice->delete();
        }
    }
}
