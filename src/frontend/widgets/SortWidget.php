<?php

namespace bulldozer\catalog\frontend\widgets;

use bulldozer\App;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class SortWidget
 * @package bulldozer\catalog\frontend\widgets
 */
class SortWidget extends Widget
{
    /**
     * @return string
     */
    public function run()
    {
        $params = App::$app->request->getQueryParams();

        $priceSort = $this->normalizeSort(ArrayHelper::getValue($params, 'price'));
        $newSort = $this->normalizeSort(ArrayHelper::getValue($params, 'new'));
        $discountPercentSort = $this->normalizeSort(ArrayHelper::getValue($params, 'discount_percent'));

        return $this->render('sort', [
            'priceSort' => $priceSort,
            'newSort' => $newSort,
            'discountPercentSort' => $discountPercentSort,
            'widget' => $this,
        ]);
    }

    /**
     * @param string $key
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function buildUrl(string $key): string
    {
        $queryStr = App::$app->request->getQueryString();
        $params = [];
        parse_str($queryStr, $params);

        $value = ArrayHelper::getValue($params, $key);

        foreach ($params as $key1 => $value) {
            if (in_array($key1, ['price', 'new', 'discount_percent'])) {
                unset($params[$key1]);
            }
        }

        if ($value === null) {
            $params[$key] = 'desc';
        } elseif ($value === 'asc') {
            $params[$key] = 'desc';
        } elseif ($value === 'desc') {
            $params[$key] = 'asc';
        }

        $url = App::$app->request->getUrl();
        $urlParts = parse_url($url);

        return $urlParts['path'] . '?' . http_build_query($params);
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