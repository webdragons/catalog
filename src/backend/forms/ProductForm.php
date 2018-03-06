<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\backend\validators\DiscountsValidator;
use bulldozer\catalog\backend\validators\PricesValidator;
use bulldozer\catalog\common\ar\ProductImage;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\ar\Section;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use Yii;
use yii\web\UploadedFile;

/**
 * Class ProductForm
 * @package bulldozer\catalog\backend\forms
 */
class ProductForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $section_id;

    /**
     * @var array
     */
    public $prices;

    /**
     * @var array
     */
    public $discounts;

    /**
     * @var
     */
    public $properties;

    /**
     * @var string
     */
    public $description;

    /**
     * @var UploadedFile[]
     */
    public $images;

    /**
     * @var ProductImage[]
     */
    public $uploadedImages = [];

    /**
     * @var int
     */
    public $sort;

    /**
     * @var int
     */
    public $active;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['section_id', 'required'],
            ['section_id', 'sectionsValidator'],

            ['sort', 'integer'],
            ['sort', 'required'],

            ['description', 'string', 'max' => 60000],

            ['images', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 20],

            ['active', 'boolean'],

            ['prices', PricesValidator::class],
            ['discounts', DiscountsValidator::class],

            ['properties', 'propertiesValidator'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function sectionsValidator(string $attribute): void
    {
        $section = Section::findOne($this->$attribute);

        if ($section === null) {
            $this->addError($attribute, Yii::t('catalog', 'Section not found'));
        }
    }

    /**
     * @param string $attribute
     */
    public function propertiesValidator(string $attribute)
    {
        if (is_array($this->$attribute)) {
            $properties = [];
            $_properties = Property::find()->all();

            foreach ($_properties as $property) {
                $properties[$property->id] = $property;
            }

            foreach ($this->$attribute as $propertyId => $propertyValue) {
                if (isset($properties[$propertyId])) {
                    /** @var Property $property */
                    $property = $properties[$propertyId];

                    if ($property->multiple == 0) {
                        switch ($property->type) {
                            case PropertyTypesEnum::TYPE_STRING:
                                if (!is_string($propertyValue)) {
                                    $this->addError($attribute, Yii::t('catalog', 'The value must be a string'));
                                }
                                break;
                            case PropertyTypesEnum::TYPE_URL:
                                if (!isset($propertyValue['name'])) {
                                    $this->addError($attribute, Yii::t('catalog', 'You must provide a title for the link'));
                                }

                                if (!isset($propertyValue['link'])) {
                                    $this->addError($attribute, Yii::t('catalog', 'You must provide a link address'));
                                }
                                break;
                            case PropertyTypesEnum::TYPE_BOOLEAN:
                                if (!in_array($propertyValue, [0, 1])) {
                                    $this->addError($attribute, Yii::t('catalog', 'Value must be Yes or No'));
                                }
                                break;
                        }
                    } else {
                        if (is_array($propertyValue) && count($propertyValue) > 0) {
                            foreach ($propertyValue as $value) {
                                switch ($property->type) {
                                    case PropertyTypesEnum::TYPE_STRING:
                                        if (!is_string($value)) {
                                            $this->addError($attribute, Yii::t('catalog', 'The value must be a string'));
                                        }
                                        break;
                                    case PropertyTypesEnum::TYPE_URL:
                                        if (!isset($value['name'])) {
                                            $this->addError($attribute, Yii::t('catalog', 'You must provide a title for the link'));
                                        }

                                        if (!isset($value['link'])) {
                                            $this->addError($attribute, Yii::t('catalog', 'You must provide a link address'));
                                        }
                                        break;
                                    case PropertyTypesEnum::TYPE_BOOLEAN:
                                        if (!in_array($value, [0, 1])) {
                                            $this->addError($attribute, Yii::t('catalog', 'Value must be Yes or No'));
                                        }
                                        break;
                                }
                            }
                        }
                    }
                } else {
                    $this->addError($attribute, Yii::t('catalog', 'Property not found'));
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'section_id' => Yii::t('catalog', 'Section'),
            'description' => Yii::t('catalog', 'Description'),
            'images' => Yii::t('catalog', 'Images'),
            'sort' => Yii::t('catalog', 'Display order'),
            'active' => Yii::t('catalog', 'Active'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'description',
            'section_id',
            'sort',
            'active',
            'section_id',
        ];
    }
}