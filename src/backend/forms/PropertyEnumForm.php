<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;

/**
 * Class PropertyEnumForm
 * @package bulldozer\catalog\backend\forms
 */
class PropertyEnumForm extends Form
{
    /**
     * @var string
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['value', 'required'],
            ['value', 'string'],
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'value',
        ];
    }
}