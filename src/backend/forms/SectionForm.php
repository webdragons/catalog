<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\backend\validators\ParentSectionValidator;
use Yii;
use yii\web\UploadedFile;

/**
 * Class SectionForm
 * @package bulldozer\catalog\backend\forms
 */
class SectionForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $parent_id;

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var int
     */
    public $sort;

    /**
     * @var UploadedFile
     */
    public $image;

    /**
     * @var int
     */
    public $active;

    /**
     * @var int
     */
    public $watermark_id;

    /**
     * @var integer
     */
    public $watermark_position;

    /**
     * @var integer
     */
    public $watermark_transparency;

    /**
     * @var int
     */
    private $id;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['parent_id', 'required'],
            ['parent_id', 'integer'],
            ['parent_id', ParentSectionValidator::class, 'section_id' => $this->id],

            ['sort', 'integer'],
            ['sort', 'required'],

            ['active', 'boolean'],

            [
                'properties',
                'each',
                'rule' => [
                    'in',
                    'range' => Property::find()->asArray()->select(['id'])->column()
                ]
            ],

            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],

            /** @todo Implement watermark support in file module */
            /*['watermark_id', 'integer'],
            ['watermark_position', 'in', 'range' => array_keys(WatermarkPositionsEnum::$list)],
            ['watermark_transparency', 'integer', 'min' => 1, 'max' => 100],
            [
                ['watermark_position', 'watermark_transparency'],
                WatermarkValidator::class,
                'watermark_attribute' => 'watermark_id'
            ],*/
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'parent_id' => Yii::t('catalog', 'Parent section'),
            'properties' => Yii::t('catalog', 'Available properties'),
            'sort' => Yii::t('catalog', 'Display order'),
            'image' => Yii::t('catalog', 'Image'),
            'active' => Yii::t('catalog', 'Active'),
            'watermarkFile' => Yii::t('catalog', 'Watermark'),
            'watermark_position' => Yii::t('catalog', 'Watermark position'),
            'watermark_transparency' => Yii::t('catalog', 'Watermark transparency'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'sort',
            'active',
            'watermark_id',
            'watermark_position',
            'watermark_transparency'
        ];
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}