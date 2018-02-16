<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\catalog\frontend\ar\Section;
use bulldozer\menu\frontend\services\MenuServiceInterface;
use yii\db\ActiveQuery;

/**
 * Class CatalogMenuService
 * @package bulldozer\catalog\frontend\services
 */
class CatalogMenuService implements MenuServiceInterface
{
    /**
     * @param int|null $id
     * @return array
     */
    public function getMenuItems(?int $id = null): array
    {
        /** @var ActiveQuery $query */
        $query = Section::find()->roots();

        /* @var Section[] */
        $sections = $query->orderBy(['sort' => SORT_ASC])->all();

        $result = [];

        foreach ($sections as $section) {
            $childs = $section->children(1)->orderBy(['sort' => SORT_ASC])->all();

            $item = [
                'label' => $section->name,
                'url' => $section->viewUrl,
                'childs' => [],
            ];

            foreach ($childs as $child) {
                $item['childs'][] = [
                    'label' => $child->name,
                    'url' => $child->viewUrl,
                ];
            }

            $result[] = $item;
        }

        return $result;
    }
}