<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\App;
use yii\web\Cookie;

/**
 * Class ItemsPerPageService
 * @package bulldozer\catalog\frontend\services
 */
class ItemsPerPageService
{
    const ITEMS_PER_PAGE_COOKIE = 'items_per_page';
    const DEFAULT_VALUE = 24;

    /**
     * @return int
     */
    public function getValue(): int
    {
        $cookie = App::$app->request->cookies->get(self::ITEMS_PER_PAGE_COOKIE);

        if ($cookie) {
            return (int)$cookie->value;
        }

        return self::DEFAULT_VALUE;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        App::$app->response->cookies->add(new Cookie([
            'name' => self::ITEMS_PER_PAGE_COOKIE,
            'value' => $value,
            'expire' => time() + 180 * 60 * 60 * 24,
        ]));
    }
}