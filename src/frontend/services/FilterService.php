<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\App;
use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\ProductPrice;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\catalog\frontend\ar\Product;
use bulldozer\catalog\frontend\ar\Section;
use bulldozer\catalog\frontend\entities\FilterProperty;
use yii\caching\Cache;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class FilterService
 * @package bulldozer\catalog\frontend\services
 */
class FilterService
{
    const CACHE_DURATION = 1200;

    /**
     * @var ActiveQuery
     */
    private $query;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var ProductsMongoDBService
     */
    private $productsMongoDBService;

    /**
     * @var Cache
     */
    private $cacheProvider;

    /**
     * FilterService constructor.
     * @param ProductsMongoDBService $productsMongoDBService
     */
    public function __construct(ProductsMongoDBService $productsMongoDBService)
    {
        $this->productsMongoDBService = $productsMongoDBService;

        $this->cacheProvider = App::$app->cache;
    }

    /**
     * @return void
     */
    public function applyFilter(): void
    {
        $params = $this->getFilterParams();

        if (isset($params['price']) && (!empty($params['price']['from']) || !empty($params['price']['to']))) {
            $value = $params['price'];

            $this->query->joinWith(['prices', 'discounts']);
            $this->query->addSelect(['IF(' . Discount::tableName() . '.value > 0,
                ' . Discount::tableName() . '.value,
                ' . ProductPrice::tableName() . '.value) as filtered_price']);

            if (!empty($value['from'])) {
                $fromPrice = intval($value['from']);

                $this->query->andHaving(['>=', 'filtered_price', $fromPrice]);
            }

            if (!empty($value['to'])) {
                $fromPrice = intval($value['to']);

                $this->query->andHaving(['<=', 'filtered_price', $fromPrice]);
            }
        }

        $productIds = $this->productsMongoDBService->getFilteredProductIds($this->section, $params);

        if (count($productIds) > 0) {
            $this->query->andWhere([Product::tableName() . '.id' => $productIds]);
        } else {
            $this->query->andWhere([Product::tableName() . '.id' => 0]);
        }
    }

    /**
     * @param string $filterName
     * @param int|null $id
     * @return bool
     */
    public function isActiveFilter(string $filterName, int $id = null): bool
    {
        $params = $this->getFilterParams();

        if (isset($params[$filterName])) {
            if ($filterName == 'properties') {
                return isset($params[$filterName][$id]) && is_array($params[$filterName][$id]) && count($params[$filterName][$id]) > 0;
            } else {
                return isset($params[$filterName]) && is_array($params[$filterName]) && count($params[$filterName]) > 0;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getFilterParams(): array
    {
        $params = App::$app->request->getQueryParams();

        $filterParams = $params['filter'] ?? [];

        $allowedParams = ['price', 'properties'];

        foreach ($filterParams as $param => $value) {
            if (!in_array($param, $allowedParams)) {
                unset($filterParams[$param]);
            }
        }

        return $filterParams;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        if ($this->section) {
            $key = 'catalog.filter.properties.' . $this->section->id;
        } else {
            $key = 'catalog.filter.properties.root';
        }

        if (!$data = $this->cacheProvider->get($key)) {
            $data = $this->loadProperties();
            $this->cacheProvider->set($key, $data, self::CACHE_DURATION);
        }

        return $data;
    }

    /**
     * @param ActiveQuery $query
     */
    public function setQuery(ActiveQuery $query): void
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getPricesRange(): array
    {
        if ($this->section) {
            $key = 'catalog.filter.price.' . $this->section->id;
        } else {
            $key = 'catalog.filter.price.root';
        }

        if (!$data = $this->cacheProvider->get($key)) {
            $query = Product::find()
                ->select([
                    Product::tableName() . '.id',
                    'min(IF(di.value > 0, di.value, pr.value)) as min_price',
                    'max(IF(di.value > 0, di.value, pr.value)) as max_price'
                ])
                ->joinWith(['prices pr', 'discounts di'])
                ->groupBy([])
                ->orderBy([])
                ->asArray();

            if ($this->section) {
                $allChildsIds = $this->section->children()->asArray()->select(['id'])->column();
                $allChildsIds[] = $this->section->id;

                $query->andWhere([
                    Product::tableName() . '.section_id' => $allChildsIds,
                ]);
            }

            $data = $query->one();
            $this->cacheProvider->set($key, $data, self::CACHE_DURATION);
        }

        return $data;
    }

    /**
     * @param Section $section
     */
    public function setSection(Section $section): void
    {
        $this->section = $section;
    }

    /**
     * @return Property[]
     */
    protected function loadProperties(): array
    {
        $propertyValues = $this->productsMongoDBService->getPropertyValues($this->section);
        $ids = array_keys($propertyValues);

        if ($this->section) {
            $sectionPropertyIds = ArrayHelper::getColumn($this->section->properties, 'id');

            foreach ($ids as $key => $id) {
                $found = false;

                foreach ($sectionPropertyIds as $key1 => $sectionPropertyId) {
                    if ($sectionPropertyId == $id) {
                        $found = true;
                    }
                }

                if ($found == false) {
                    unset($ids[$key]);
                }
            }
        }

        $properties = [];

        if (count($ids) > 0) {
            $propertiesAR = Property::find()
                ->andWhere(['filtered' => 1])
                ->andWhere(['id' => $ids])
                ->orderBy(['sort' => SORT_ASC])
                ->all();

            foreach ($propertiesAR as $propertyAR) {
                $property = new FilterProperty($propertyAR->id, $propertyAR->name, $propertyAR->property_type);

                if ($property->getType() == PropertyTypesEnum::TYPE_ENUM) {
                    foreach ($propertyAR->enums as $propertyEnum) {
                        foreach ($propertyValues[$propertyAR->id] as $propertyValue) {
                            if ($propertyEnum->id == $propertyValue) {
                                $property->addValue($propertyEnum);
                            }
                        }
                    }
                } elseif ($property->getType() != PropertyTypesEnum::TYPE_BOOLEAN) {
                    foreach ($propertyValues[$propertyAR->id] as $propertyValue) {
                        $property->addValue($propertyValue);
                    }
                }

                $properties[] = $property;
            }
        }

        return $properties;
    }
}