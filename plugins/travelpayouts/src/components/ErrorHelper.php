<?php

namespace Travelpayouts\components;

use Travelpayouts;
use Travelpayouts\components\HtmlHelper as Html;

class ErrorHelper
{
    public static function render_errors($shortcode_name, $shortcode_errors, $show_key = false)
    {
        $html = '[' . $shortcode_name . '] ';
        $html .= Travelpayouts::__('Shortcode validation failed:');

        $html = Html::tag('span', [], $html);

        $errors = [];
        foreach ($shortcode_errors as $key => $error) {

            $error_msg = implode(' ', $error);
            if($show_key) {
                $error_msg = Html::tag('span', [], "\"{$key}\": ") . $error_msg;
            }

            $errors[] = Html::tag('li', [], $error_msg);
        }

        $html .= Html::tag('ul', [], implode('', $errors));

        return Html::tag(
            'div',
            [
                'class' => TRAVELPAYOUTS_TEXT_DOMAIN . '-shortcode-validation-error'
            ],
            $html
        );
    }
}
