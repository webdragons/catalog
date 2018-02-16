<?php

namespace bulldozer\catalog\frontend\ar;

use yii\db\ActiveQuery;

/**
 * Class ProductList
 * @package bulldozer\catalog\frontend\ar
 */
class ProductList extends \bulldozer\catalog\common\ar\ProductList
{
    /**
     * @return ActiveQuery
     */
    public function getProductsList(): ActiveQuery
    {
        $ids = json_decode($this->products);
        $query = Product::find()->andWhere(['id' => $ids]);

        if (count($ids)) {
            $query->addOrderBy(['FIELD(id, ' . implode(', ', $ids) . ')' => SORT_ASC]);
        }

        $query->multiple = true;
        return $query;
    }
}