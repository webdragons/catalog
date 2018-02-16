<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\SectionForm;
use bulldozer\catalog\common\ar\Section;
use bulldozer\catalog\common\ar\SectionProperty;
use bulldozer\files\models\Image;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class SectionService
 * @package bulldozer\catalog\backend\services
 */
class SectionService
{
    /**
     * @param Section|null $section
     * @return SectionForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(?Section $section = null): SectionForm
    {
        /** @var SectionForm $form */
        $form = App::createObject([
            'class' => SectionForm::class,
        ]);

        if ($section) {
            $form->setAttributes($section->getAttributes($form->getSavedAttributes()));
            $form->setId($section->id);

            $parent = $section->parents(1)->one();

            if ($parent !== null) {
                $form->parent_id = $parent->id;
            } else {
                $form->parent_id = 0;
            }

            $form->properties = ArrayHelper::getColumn(SectionProperty::find()->where(['section_id' => $section->id])->all(), 'property_id');
        } else {
            $lastSection = Section::find()->orderBy(['sort' => SORT_DESC])->one();

            if ($lastSection) {
                $form->sort = $lastSection->sort + 100;
            } else {
                $form->sort = 100;
            }
        }

        return $form;
    }

    /**
     * @param SectionForm $form
     * @param Section|null $section
     * @return Section
     * @throws Exception
     * @throws StaleObjectException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function save(SectionForm $form, Section $section = null): Section
    {
        if ($section === null) {
            $section = App::createObject([
                'class' => Section::class,
            ]);
        }

        $form->image = UploadedFile::getInstance($form, 'image');

        $parent = null;
        $old_parent = $section->parents(1)->one();

        if ($form->parent_id > 0) {
            $parent = Section::findOne($form->parent_id);
        }

        $needUpdateWatermark = $section->watermark_id != $form->watermark_id
            || $section->watermark_position != $form->watermark_position
            || $section->watermark_transparency != $form->watermark_transparency;

        $section->setAttributes($form->getAttributes($form->getSavedAttributes()));

        $transaction = App::$app->db->beginTransaction();

        if ($form->parent_id == 0 && !$section->isRoot()) {
            $result = $section->makeRoot();
        } elseif ($old_parent !== $parent) {
            $result = $section->appendTo($parent);
        } else {
            $result = $section->save();
        }

        if ($result) {
            SectionProperty::deleteAll(['section_id' => $section->id]);

            if (is_array($form->properties)) {
                foreach ($form->properties as $property_id) {
                    /** @var SectionProperty $prop */
                    $prop = App::createObject([
                        'class' => SectionProperty::class,
                        'section_id' => $section->id,
                        'property_id' => $property_id,
                    ]);

                    if (!$prop->save()) {
                        throw new Exception('Cant save section property. Errors: ' . json_encode($prop->getErrors()));
                    }
                }
            }

            if ($form->image !== null) {
                if ($section->image) {
                    $section->image->delete();
                }

                /** @var Image $file */
                $file = App::createObject([
                    'class' => Image::class,
                ]);

                if ($file->upload($form->image) && $file->save()) {
                    $section->image_id = $file->id;
                    $section->save();
                } else {
                    throw new Exception('Cant save image. Errors: ' . json_encode($file->getErrors()));
                }
            }

            /** @todo Implement in file module */
            /*if ($needUpdateWatermark && $this->section->watermark) {
                $queue = Yii::$app->tasks_queue;
                $queue->push(new UpdateWatermarkForSectionTask([
                    'section_id' => $this->section->id,
                ]));
            }*/

            $transaction->commit();
            return $section;
        } else {
            throw new Exception('Cant save section. Errors: ' . json_encode($section->getErrors()));
        }
    }

    /**
     * @return array
     */
    public function getSectionsTree(): array
    {
        $sections = Section::find()->orderBy(['tree' => SORT_ASC, 'left' => SORT_ASC])->all();
        $tmp = [
            0 => Yii::t('catalog', 'Root section'),
        ];

        foreach ($sections as $_section) {
            $tmp[$_section->id] = str_repeat('--', $_section->depth + 1) . ' ' . $_section->name;
        }

        return $tmp;
    }
}