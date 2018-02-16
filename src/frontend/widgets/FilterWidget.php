<?php

namespace bulldozer\catalog\frontend\widgets;

use bulldozer\App;
use bulldozer\catalog\frontend\services\FilterService;
use bulldozer\catalog\frontend\widgets\forms\FilterForm;
use yii\base\Widget;

/**
 * Class FilterWidget
 * @package bulldozer\catalog\frontend\widgets
 */
class FilterWidget extends Widget
{
    /**
     * @var FilterService
     */
    public $filterService;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        if (!($this->filterService instanceof FilterService)) {
            throw new \InvalidArgumentException('filterService must be instance of FilterService');
        }
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run(): string
    {
        /** @var FilterForm $filterForm */
        $filterForm = App::createObject([
            'class' => FilterForm::class,
        ]);

        $pricesRange = $this->filterService->getPricesRange();
        $filterForm->price['from'] = (int) $pricesRange['min_price'];
        $filterForm->price['to'] = (int) $pricesRange['max_price'];

        $filterForm->load(App::$app->request->getQueryParams()) && $filterForm->validate();

        return $this->render('filter', [
            'filterForm' => $filterForm,
            'properties' => $this->filterService->getProperties(),
            'pricesRange' => $pricesRange,
            'filterService' => $this->filterService,
        ]);
    }
}