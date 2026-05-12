<?php

namespace Travelpayouts\modules\tables\components\settings;

use Travelpayouts;
use Travelpayouts\components\tables\TableShortcode;
use Travelpayouts\helpers\ArrayHelper;
use Travelpayouts\helpers\StringHelper;
use Travelpayouts\modules\searchForms\models\SearchFormModel;

/**
 * Class FlightsSettingsSection
 * @package Travelpayouts\modules\tables\components\settings
 */
class FlightsSettingsSection extends Fields
{
    /**
     * @var string
     */
    public $title_inline_css;
    /**
     * @var array
     */
    public $typography;
    /**
     * @var string
     */
    public $title_custom_class;
    /**
     * @var string
     */
    public $theme = 'default-theme';
    /**
     * @var string
     */
    public $message_error_switch;
    /**
     * @var string
     */
    public $table_message_error;
    /**
     * @var string
     */
    public $empty_flights_table_search_form;

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $searchFormOptions = (new SearchFormModel())->getFormsSelect();
        return [
            'title_inline_css' => $this->fieldInlineCheckbox()
                ->setTitle(Travelpayouts::__('Disable plugin title styles')),
            'typography' => $this->fieldTypography()
                ->setTitle(Travelpayouts::__('Header styles'))
                ->setRequired($this->requiredRule('title_inline_css', 'equals', false)),
            'title_custom_class' => $this->fieldInput()
                ->setTitle(Travelpayouts::__('Title css class'))
                ->setRequired($this->requiredRule('title_inline_css', 'equals', true)),
            'theme' => (new ThemeSelectField())
                ->setOptions($this->themeList()),
            'message_error_switch' => $this->fieldSelect()
                ->setTitle(Travelpayouts::__('No results found scenario'))
                ->setOptions([
                    'hide' => Travelpayouts::__('Hide the table'),
                    'show_message' => Travelpayouts::__('Show error message'),
                    'show_search_form' => Travelpayouts::__('Show the search form'),
                ])
                ->setDefault('hide')
                ->setSubtitle(Travelpayouts::__('Choose what happens when there are no results available for the user\'s search query')),
            'table_message_error' => $this->fieldTextarea()
                ->setTitle(Travelpayouts::__('Error message'))
                ->setDefault(Travelpayouts::__('Unfortunately, we don\'t have actual data for flights from {origin} to {destination}. [link title="Find tickets from {origin} to {destination}"]'))
                ->setRequired($this->requiredRule('message_error_switch', 'equals', 'show_message')),
            'empty_flights_table_search_form' => $this->fieldSelect()
                ->setTitle(Travelpayouts::__('Select search form'))
                ->setOptions($searchFormOptions)
                ->setDefault(ArrayHelper::getFirstKey($searchFormOptions))
                ->setRequired($this->requiredRule('message_error_switch', 'equals', 'show_search_form')),
        ];
    }

    /**
     * @return array
     */
    public function themeList(): array
    {
        return TableShortcode::availableThemes();
    }

    /**
     * @inheritDoc
     */
    public function optionPath(): string
    {
        return 'flights';
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return Travelpayouts::__('Customize flights tables settings');
    }

    public function getUseInlineStyles(): bool
    {
        return !StringHelper::toBoolean($this->title_inline_css);
    }
}
