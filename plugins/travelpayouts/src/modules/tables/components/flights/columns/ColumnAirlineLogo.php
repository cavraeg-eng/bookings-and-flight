<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\modules\tables\components\flights\columns;
use Travelpayouts\Vendor\DI\Annotation\Inject;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\modules\settings\SettingsForm;

class ColumnAirlineLogo extends ColumnAirline
{
    /**
     * @Inject
     * @var SettingsForm
     */
    protected $settings;
    /**
     * @var bool
     */
    protected $showAirlineName = false;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    public function init()
    {
        if (!$this->width) {
            $this->width = $this->settings->getAirlineLogoWidth();
        }
        if (!$this->height) {
            $this->height = $this->settings->getAirlineLogoHeight();
        }
    }

    public function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if ($value) {
            if ($this->showAirlineName) {
                return $this->getLogoWithAirlineNameElement($value);
            }
            return $this->getLogoElement($value);
        }

        return $value;
    }

    protected function getLogoElement($value): ?string
    {
        $logo = $this->getAirLogoUrl($value);
        $width = $this->width;
        $height= $this->height;
        if ($logo) {
            $airlineName = $this->getAirlineName($value);
            return HtmlHelper::tag(
                'img',
                [
                    'src' => $logo,
                    'alt' => $airlineName,
                    'title' => $airlineName,
                    'width' => $width,
                    'height' => $height,
                ]
            );
        }
        return null;
    }

    protected function getLogoWithAirlineNameElement($value)
    {
        return HtmlHelper::tagArrayContent('div',
            [
                'class'=> 'tp-flex tp-items-center tp-gap-2',
            ],
            [
                $this->getLogoElement($value, 30, 30),
                HtmlHelper::tag('div', [
                    'class' => 'tp-break-normal tp-flex-grow-1'
                ], $this->getAirlineName($value)),
            ]);
    }

    protected function getAirLogoUrl($value): ?string
    {
        $width = $this->width;
        $height= $this->height;
        $url = $width === $height ? 'https://pics.avs.io/al_square' : 'https://pics.avs.io';
        return is_string($value) ? "$url/$width/$height/$value@2x.png" : null;
    }
}
