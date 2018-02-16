<?php

namespace bulldozer\catalog\common\enums;

use yii2mod\enum\helpers\BaseEnum;

/**
 * Class PropertyTypesEnum
 * @package bulldozer\catalog\common\enums
 */
class PropertyTypesEnum extends BaseEnum
{
    const TYPE_STRING       = 1;
    const TYPE_NUMBER       = 2;
    const TYPE_ENUM         = 3;
    const TYPE_FILE         = 4;
    const TYPE_PRODUCT_LINK = 5;
    const TYPE_SECTION_LINK = 6;
    const TYPE_DATE         = 7;
    const TYPE_DATETIME     = 8;
    const TYPE_BOOLEAN      = 9;
    const TYPE_URL          = 10;

    /**
     * @var array
     */
    public static $list = [
        self::TYPE_STRING => 'Строка',
        //self::TYPE_NUMBER => 'Число',
        self::TYPE_ENUM => 'Список',
        //self::TYPE_FILE => 'Файл',
        //self::TYPE_PRODUCT_LINK => 'Привязка к товару',
        //self::TYPE_SECTION_LINK => 'Привязка к разделу',
        //self::TYPE_DATE => 'Дата',
        //self::TYPE_DATETIME => 'Дата и время',
        self::TYPE_BOOLEAN => 'Логическое',
        self::TYPE_URL => 'Ссылка',
    ];
}