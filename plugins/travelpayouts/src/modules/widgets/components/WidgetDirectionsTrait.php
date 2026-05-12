<?php

namespace Travelpayouts\modules\widgets\components;

trait WidgetDirectionsTrait
{
    public function get_direction_from_autocomplete_json($value)
    {
        if ($value) {
            $data = json_decode($value, true);
            if ($data && isset($data['code'])) {
                return $data['code'];
            }
        }

        return null;
    }
}
