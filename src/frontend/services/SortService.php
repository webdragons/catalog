<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\ProductPrice;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class SortService
 * @package bulldozer\catalog\frontend\services
 */
class SortService
{
    /**
     * @var ActiveQuery
     */
    private $query;

    /**
     * @param array $params
     */
    public function applySort(array $params): void
    {
        $priceSort = $this->normalizeSort(ArrayHelper::getValue($params, 'price'));
        $newSort = $this->normalizeSort(ArrayHelper::getValue($params, 'new'));
        $discountPercentSort = $this->normalizeSort(ArrayHelper::getValue($params, 'discount_percent'));

        $this->query->joinWith(['prices', 'discounts']);
        $this->query->addSelect(['IF(' . Discount::tableName() . '.value > 0,
                ' . Discount::tableName() . '.value,
                ' . ProductPrice::tableName() . '.value) as price']);

        if ($priceSort !== null) {
            if ($priceSort === 'asc') {
                $this->query->addOrderBy(['price' => SORT_ASC]);
            } else {
                $this->query->addOrderBy(['price' => SORT_DESC]);
            }
        } elseif ($newSort !== null) {
            if ($newSort === 'asc') {
                $this->query->addOrderBy(['created_at' => SORT_DESC]);
            } else {
                $this->query->addOrderBy(['created_at' => SORT_ASC]);
            }
        } elseif ($discountPercentSort !== null) {
            $this->query->addSelect(['((' . ProductPrice::tableName() . '.value - ' . Discount::tableName() . '.value)
                / ' . ProductPrice::tableName() . '.value) as discount']);

            if ($discountPercentSort == 'asc') {
                $this->query->addOrderBy(['discount' => SORT_ASC]);
            } else {
                $this->query->addOrderBy(['discount' => SORT_DESC]);
            }
        } else {
            $this->query->addOrderBy(['sort' => SORT_ASC]);
            $this->query->addOrderBy(['price' => SORT_ASC]);
        }

        $this->query->addOrderBy(['name' => SORT_ASC]);
    }

    /**
     * @param ActiveQuery $query
     */
    public function setQuery(ActiveQuery $query): void
    {
        $this->query = $query;
    }

    /**
     * @param null|string $value
     * @return null|string
     */
    protected function normalizeSort(?string $value): ?string
    {
        if (in_array($value, ['asc', 'desc'])) {
            return $value;
        }

        return null;
    }
}