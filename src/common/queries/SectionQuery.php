<?php

namespace bulldozer\catalog\common\queries;

use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Section]].
 *
 * @see Section
 * @mixin NestedSetsQueryBehavior
 */
class SectionQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}
