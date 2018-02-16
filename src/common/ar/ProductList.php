<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\traits\UsersRelationsTrait;
use bulldozer\db\ActiveRecord;
use bulldozer\users\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_product_lists}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property integer $active
 * @property string $name
 * @property string $more_url
 * @property string $products
 *
 * @property User $creator
 * @property User $updater
 * @property Product[] $productsList
 */
class ProductList extends ActiveRecord
{
    use UsersRelationsTrait;

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
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_product_lists}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getProductsList(): ActiveQuery
    {
        $ids = json_decode($this->products);
        $query = Product::find()->where(['id' => $ids]);

        if (count($ids)) {
            $query->addOrderBy(['FIELD(id, ' . implode(', ', $ids) . ')' => SORT_ASC]);
        }

        $query->multiple = true;

        return $query;
    }
}
