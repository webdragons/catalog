<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\traits\UsersRelationsTrait;
use bulldozer\db\ActiveRecord;
use bulldozer\files\models\Image;
use bulldozer\users\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_products}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $name
 * @property string $slug
 * @property integer $section_id
 * @property string $description
 * @property integer $sort
 * @property integer $active
 *
 * @property User $creator
 * @property User $updater
 * @property Section $section
 * @property Section[] $sections
 * @property ProductPrice[] $prices
 * @property ProductImage[] $productImages
 * @property Image[] $images
 * @property Image $image
 * @property Discount[] $discounts
 * @property ProductPropertyValue[] $propertyValues
 */
class Product extends ActiveRecord
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
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_products}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getPrices(): ActiveQuery
    {
        return $this->hasMany(ProductPrice::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSection(): ActiveQuery
    {
        return $this->hasOne(Section::class, ['id' => 'section_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductImages(): ActiveQuery
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getImages(): ActiveQuery
    {
        return $this->hasMany(Image::class, ['id' => 'file_id'])->via('productImages');
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->images[0] ?? null;
    }

    /**
     * @return ActiveQuery
     */
    public function getDiscounts(): ActiveQuery
    {
        return $this->hasMany(Discount::class, ['product_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPropertyValues(): ActiveQuery
    {
        return $this->hasMany(ProductPropertyValue::class, ['product_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete(): void
    {
        $productImages = ProductImage::find()->where(['product_id' => $this->id])->all();

        foreach ($productImages as $productImage) {
            $productImage->delete();
        }

        $productPrices = ProductPrice::find()->where(['product_id' => $this->id])->all();

        foreach ($productPrices as $productPrice) {
            $productPrice->delete();
        }

        $productPropertyValues = ProductPropertyValue::find()->where(['product_id' => $this->id])->all();

        foreach ($productPropertyValues as $productPropertyValue) {
            $productPropertyValue->delete();
        }

        parent::afterDelete();
    }
}
