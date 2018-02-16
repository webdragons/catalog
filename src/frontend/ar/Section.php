<?php

namespace bulldozer\catalog\frontend\ar;

use bulldozer\App;
use bulldozer\catalog\common\queries\SectionQuery;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Url;

/**
 * Class Section
 * @package bulldozer\catalog\frontend\ar
 *
 * @property-read string $viewUrl
 * @property-read string $fullViewUrl
 *
 * @mixin NestedSetsBehavior
 */
class Section extends \bulldozer\catalog\common\ar\Section
{
    /**
     * @inheritdoc
     */
    public static function find(): SectionQuery
    {
        $query = parent::find();

        if (!App::$app->user->can('catalog_manage')) {
            $query->andWhere(['active' => 1]);
        }

        return $query;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getViewUrl($full = false): string
    {
        return Url::to(['/catalog/sections/view', 'slug' => $this->slug], $full);
    }

    /**
     * @return string
     */
    public function getFullViewUrl(): string
    {
        return $this->getViewUrl(true);
    }
}