<?php

namespace bulldozer\catalog\frontend\widgets\forms;

use bulldozer\base\Form;

/**
 * Class FilterForm
 * @package bulldozer\catalog\frontend\widgets\forms
 */
class FilterForm extends Form
{
    /**
     * @var array
     */
    public $price = [];

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['price', 'each', 'rule' => ['integer', 'min' => 0]],

            ['properties', 'each', 'rule' => ['safe']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName(): string
    {
        return 'filter';
    }
}