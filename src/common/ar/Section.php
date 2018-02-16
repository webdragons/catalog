<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\queries\SectionQuery;
use bulldozer\catalog\common\traits\UsersRelationsTrait;
use bulldozer\db\ActiveRecord;
use bulldozer\files\models\Image;
use bulldozer\users\models\User;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_sections}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $name
 * @property string $slug
 * @property integer $left
 * @property integer $right
 * @property integer $depth
 * @property integer $tree
 * @property integer $sort
 * @property integer $image_id
 * @property integer $active
 * @property integer $watermark_id
 * @property integer $watermark_position
 * @property integer $watermark_transparency
 *
 * @property string $viewUrl
 * @property string $fullViewUrl
 * @property User $creator
 * @property User $updater
 * @property Image $image
 * @property Image $watermark
 * @property SectionProperty[] $sectionProperties
 * @property Property[] $properties
 *
 * @mixin NestedSetsBehavior
 */
class Section extends ActiveRecord
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
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree',
                'leftAttribute' => 'left',
                'rightAttribute' => 'right',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_sections}}';
    }

    /**
     * @inheritdoc
     * @return SectionQuery the active query used by this AR class.
     */
    public static function find(): SectionQuery
    {
        return new SectionQuery(get_called_class());
    }

    /**
     * @return ActiveQuery
     */
    public function getImage(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWatermark(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['id' => 'watermark_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSectionProperties(): ActiveQuery
    {
        return $this->hasMany(SectionProperty::class, ['section_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProperties(): ActiveQuery
    {
        return $this->hasMany(Property::class, ['id' => 'property_id'])->via('sectionProperties');
    }

    /**
     * @inheritdoc
     */
    public function afterDelete(): void
    {
        if ($this->image) {
            $this->image->delete();
        }

        $products = Product::find()->where(['section_id' => $this->id])->all();

        foreach ($products as $product) {
            $product->delete();
        }

        SectionProperty::deleteAll(['section_id' => $this->id]);

        parent::afterDelete();
    }
}
