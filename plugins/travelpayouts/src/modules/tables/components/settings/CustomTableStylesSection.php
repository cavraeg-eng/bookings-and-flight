<?php

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts;
use Travelpayouts\helpers\StringHelper;

/**
 * Class CustomTableStylesSection
 * @package Travelpayouts\modules\tables\components\settings
 */
class CustomTableStylesSection extends Fields
{
    const CUSTOM_THEME = 'custom';

    /**
     * @var string
     */
    public $customize_header;
    /**
     * @var
     */
    public $customize_body;
    /**
     * @var string
     */
    public $customize_buttons;
    /**
     * @var string
     */
    public $bg_header;
    /**
     * @var string
     */
    public $bg_header_active;
    /**
     * @var string
     */
    public $text_header;
    /**
     * @var string
     */
    public $text_header_active;
    /**
     * @var string
     */
    public $bg_body_odd;
    /**
     * @var string
     */
    public $bg_body_even;
    /**
     * @var string
     */
    public $text_body;
    /**
     * @var string
     */
    public $border_color;
    /**
     * @var string
     */
    public $bg_body_hover;
    /**
     * @var string
     */
    public $bg_button;
    /**
     * @var string
     */
    public $bg_button_hover;
    /**
     * @var string
     */
    public $border_button;
    /**
     * @var string
     */
    public $text_button;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $requireForTableHeader = $this->requiredRule('customize_header', 'equals', true);
        $requireForTableBody = $this->requiredRule('customize_body', 'equals', true);
        $requireForTableButtons = $this->requiredRule('customize_buttons', 'equals', true);

        return [
            'customize_header' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table header')),
            'bg_header' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header background color'))
                ->setDefault('#0099cc')
                ->setRequired($requireForTableHeader),
            'bg_header_active' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header active background color'))
                ->setDefault('#099dc7')
                ->setRequired($requireForTableHeader),
            'text_header' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableHeader),
            'text_header_active' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table header active text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableHeader),
            'customize_body' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table body')),
            'text_body' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body text color'))
                ->setDefault('#6c7a87')
                ->setRequired($requireForTableBody),
            'border_color'=> $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body border color'))
                ->setDefault('#eaeaea')
                ->setRequired($requireForTableBody),
            'bg_body_odd' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body odd row background color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableBody),
            'bg_body_even' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table body even row background color'))
                ->setDefault('#f5f6f9')
                ->setRequired($requireForTableBody),
            'bg_body_hover' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table row hovered background color'))
                ->setDefault('#c1dfdd')
                ->setRequired($requireForTableBody),
            'customize_buttons' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Customize table buttons')),
            'bg_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button background color'))
                ->setDefault('#fcb942')
                ->setRequired($requireForTableButtons),
            'bg_button_hover' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button hovered background color'))
                ->setDefault('#fcb02d')
                ->setRequired($requireForTableButtons),
            'border_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button border color'))
                ->setDefault('#ce6408')
                ->setRequired($requireForTableButtons),
            'text_button' => $this->fieldColor()
                ->setTitle(Travelpayouts::__('Table button text color'))
                ->setDefault('#ffffff')
                ->setRequired($requireForTableButtons),
        ];
    }

    /**
     * @return bool
     */
    public function getCustomizeHeader(): bool
    {
        return StringHelper::toBoolean($this->customize_header);
    }

    /**
     * @return bool
     */
    public function getCustomizeBody(): bool
    {
        return StringHelper::toBoolean($this->customize_body);
    }

    /**
     * @return bool
     */
    public function getCustomizeButtons(): bool
    {
        return StringHelper::toBoolean($this->customize_buttons);
    }

    public function getCssVariables(): array
    {
        $result = [];

        if($this->getCustomizeHeader()){
            $result = array_merge($result, [
                'tp-table-custom-header-bg' => $this->bg_header,
                'tp-table-custom-header-color' => $this->text_header,
                'tp-table-custom-header-active-bg' => $this->bg_header_active,
                'tp-table-custom-header-active-color' => $this->text_header_active,
            ]);
        }

        if($this->getCustomizeBody()){
            $result = array_merge($result, [
                'tp-table-custom-body-bg-odd' => $this->bg_body_odd,
                'tp-table-custom-body-bg-even' => $this->bg_body_even,
                'tp-table-custom-body-color' => $this->text_body,
                'tp-table-custom-body-bg-hover' => $this->bg_body_hover,
                'tp-table-custom-body-border' => $this->border_color,
            ]);
        }

        if($this->getCustomizeButtons()){
            $result = array_merge($result, [
                'tp-table-custom-button-bg' => $this->bg_button,
                'tp-table-custom-button-bg-hover' => $this->bg_button_hover,
                'tp-table-custom-button-border' => $this->border_button,
                'tp-table-custom-button-color' => $this->text_button,
            ]);
        }





        return $result;
    }

    /**
     *
     * @return string|null
     */
    public function getStylesheets(): ?string
    {
        $variables = $this->getCssVariables();
        if (!empty($variables)) {
            $content = ":root{ \n";
            foreach ($variables as $key => $value) {
                $content .= "--$key: $value;\n";
            }
            $content .= "}\n";
            return $content;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'custom_styles';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Customize tables design');
    }
}
