<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\widgets\components\forms\flights\ducklett;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\modules\widgets\components\BaseWidgetShortcodeModel;

class WidgetIata extends BaseWidgetShortcodeModel
{
    public $marker;
    public $host;
    public $widget_type;
    public $powered_by;
    public $origin_iatas;
    public $destination_iatas;

    /**
     * @Inject
     * @param TpDucklettWidget $value
     */
    public function setSection($value)
    {
        $this->section = $value;
    }

    public function rules()
    {
        return [
            [
                [
                    'marker',
                    'host',
                    'currency',
                    'widget_type',
                    'origin_iatas',
                    'destination_iatas',
                ],
                'required',
            ],
            [
                [
                    'powered_by',
                    'origin',
                    'destination',
                    'widget_url',
                ],
                'safe',
            ],
        ];
    }

    public function set_origin($value)
    {
        $this->origin_iatas = $value;
    }

    public function set_destination($value)
    {
        $this->destination_iatas = $value;
    }

    public function render()
    {
        if (!$this->widget_url) {
            $this->add_error('widget_url', 'widget_url must be set');
        }

        if ($this->validate()) {
            $url = implode('', [
                $this->widget_url,
                http_build_query($this->attributes),
            ]);
            return $this->render_script($url);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function shortcodeTags()
    {
        return ['tp_ducklett_widget'];
    }
}
