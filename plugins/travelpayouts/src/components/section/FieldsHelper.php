<?php

namespace Travelpayouts\components\section;

use Travelpayouts;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\components\section\fields\BaseField;

class FieldsHelper
{

    public static function pre($content, $classNames = [])
    {
        $classNames = array_merge(['tp-pre'], $classNames);
        return HtmlHelper::tag('span', ['class' => implode(' ', $classNames)], $content);
    }

    public static function getMultilingualFields(BaseField $field): array
    {
        $languagesData = Travelpayouts::getInstance()->multiLang->multiLangData();

        $fields[] = $field->result();
        if (!empty($languagesData)) {
            foreach ($languagesData['languagesList'] as $language) {
                if ($language !== $languagesData['default']) {
                    $fields[] = $field->setID($field->id . '_' . $language)
                        ->setTitle($field->title . ' ' . $language)
                        ->result();
                }
            }
        }

        return $fields;
    }
}