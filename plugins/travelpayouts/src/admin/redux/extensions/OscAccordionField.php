<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\extensions;

use Travelpayouts\admin\redux\base\ConfigurableField;
use Travelpayouts\components\HtmlHelper;

class OscAccordionField extends ConfigurableField
{
    public const TYPE = 'osc_accordion';
    public const POSITION_START = 'start';
    public const POSITION_END = 'end';

    /**
     * @var string|null
     */
    public $title;
    /**
     * @var string|null
     */
    public $subtitle;
    /**
     * @var bool
     */
    public $open = false;
    /**
     * @var string
     */
    public $position = null;

    public function render(): void
    {
        switch ($this->position) {
            case self::POSITION_START:
                $this->renderStart();
                break;
            case self::POSITION_END:
                $this->renderEnd();
                break;
        }
    }

    protected function renderStart(): void
    {
        $isOpened = $this->open;
        $headerClassList = [
            'travelpayouts-accordion__header',
        ];
        if ($isOpened) {
            $headerClassList[] = 'travelpayouts-accordion__header--visible';
        }
        $startContent = [
            '</td></tr></table>',
            HtmlHelper::openTag('div', [
                'class' => 'travelpayouts-accordion',
            ]),
            HtmlHelper::openTag('div', [
                'class' => implode(' ', $headerClassList),
                'tabindex' => '0',
            ]),
            HtmlHelper::tag('div', [
                'class' => 'travelpayouts-accordion__title',
            ], $this->title),
            !empty($this->subtitle)
                ? HtmlHelper::tag('div', [
                'class' => 'travelpayouts-accordion__subtitle',
            ], $this->subtitle)
                : null,
            HtmlHelper::closeTag('div'),
            HtmlHelper::openTag('div', [
                'class' => 'travelpayouts-accordion__content',
                'style' => !$isOpened
                    ? 'display:none;'
                    : '',
            ]),
            '<table class="form-table tp-admin-section no-border" style="margin-top: 0;"><tbody class="tp-admin-table-body"><tr class="tp-admin-table-body-row" style="border-bottom:0; display:none;">',
            '<th class="tp-admin-table-body-head" style="padding-top:0;"></th><td class="tp-admin-table-body-cell" style="padding-top:0;">',
        ];
        echo implode('', $startContent);
    }

    protected function renderEnd(): void
    {
        $endContent = [
            '</td></tr></table></div>',
            '</div>',
            '<table class="form-table tp-admin-section no-border" style="margin-top: 0;"><tbody class="tp-admin-table-body"><tr class="tp-admin-table-body-row" style="border-bottom:0; display:none;">',
            '<th style="padding-top:0;"></th><td style="padding-top:0;">',
        ];
        echo implode('', $endContent);
    }
}