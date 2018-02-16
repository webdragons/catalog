<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\db\ActiveRecord;
use bulldozer\files\models\Image;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_product_images}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $file_id
 *
 * @property Image $file
 */
class ProductImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_product_images}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getFile(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['id' => 'file_id']);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete(): void
    {
        if ($this->file) {
            $this->file->delete();
        }

        parent::afterDelete();
    }
}
