<?php

namespace bulldozer\catalog\frontend\widgets;

use bulldozer\App;
use bulldozer\catalog\frontend\services\ItemsPerPageService;
use bulldozer\catalog\frontend\widgets\forms\ItemsPerPageForm;
use yii\base\Widget;

/**
 * Class ItemsPerPageWidget
 * @package bulldozer\catalog\frontend\widgets
 */
class ItemsPerPageWidget extends Widget
{
    /**
     * @var ItemsPerPageService
     */
    private $itemsPerPageService;

    /**
     * ItemsPerPageWidget constructor.
     * @param ItemsPerPageService $itemsPerPageService
     * @param array $config
     */
    public function __construct(ItemsPerPageService $itemsPerPageService, array $config = [])
    {
        parent::__construct($config);

        $this->itemsPerPageService = $itemsPerPageService;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var ItemsPerPageForm $model */
        $model = App::createObject([
            'class' => ItemsPerPageForm::class,
            'itemsPerPage' => $this->itemsPerPageService->getValue(),
        ]);

        if ($model->load(App::$app->request->post()) && $model->validate()) {
            $this->itemsPerPageService->setValue($model->itemsPerPage);

            App::$app->getResponse()->redirect(App::$app->request->absoluteUrl, 302);
        }

        return $this->render('items_per_page', [
            'model' => $model,
        ]);
    }
}