<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\section;

use Travelpayouts\components\exceptions\InvalidConfigException;
use Travelpayouts\components\section\fields\Accordion;
use Travelpayouts\components\section\fields\Checkbox;
use Travelpayouts\components\section\fields\ClearCache;
use Travelpayouts\components\section\fields\Color;
use Travelpayouts\components\section\fields\Dimensions;
use Travelpayouts\components\section\fields\ImgSelect;
use Travelpayouts\components\section\fields\Import;
use Travelpayouts\components\section\fields\InlineCheckbox;
use Travelpayouts\components\section\fields\Input;
use Travelpayouts\components\section\fields\Radio;
use Travelpayouts\components\section\fields\Raw;
use Travelpayouts\components\section\fields\Section;
use Travelpayouts\components\section\fields\Select;
use Travelpayouts\components\section\fields\Slider;
use Travelpayouts\components\section\fields\Sorter;
use Travelpayouts\components\section\fields\Switcher;
use Travelpayouts\components\section\fields\Textarea;
use Travelpayouts\components\section\fields\TrafficSource;
use Travelpayouts\components\section\fields\Typography;

trait ReduxFieldsTrait
{

    /**
     * @return Checkbox
     */
    public function fieldCheckbox(): Checkbox
    {
        return new Checkbox();
    }

    /**
     * @return InlineCheckbox
     */
    public function fieldInlineCheckbox(): InlineCheckbox
    {
        return new InlineCheckbox();
    }

    /**
     * @return Input
     */
    public function fieldInput(): Input
    {
        return new Input();
    }

    /**
     * @return Select
     */
    public function fieldSelect(): Select
    {
        return new Select();
    }

    /**
     * @return Sorter
     */
    public function fieldSorter(): Sorter
    {
        return new Sorter();
    }

    /**
     * @return Slider
     */
    public function fieldSlider(): Slider
    {
        return new Slider();
    }

    /**
     * @return Textarea
     */
    public function fieldTextarea(): Textarea
    {
        return new Textarea();
    }

    /**
     * @return Typography
     */
    public function fieldTypography(): Typography
    {
        return new Typography();
    }

    /**
     * @return ImgSelect
     */
    public function fieldImgSelect(): ImgSelect
    {
        return new ImgSelect();
    }

    /**
     * @return Color
     */
    public function fieldColor(): Color
    {
        return new Color();
    }

    /**
     * @return TrafficSource
     */
    public function fieldTrafficSource(): TrafficSource
    {
        return new TrafficSource();
    }

    /**
     * @return Section
     */
    public function fieldSection(): Section
    {
        return new Section();
    }

    /**
     * @return Radio
     */
    public function fieldRadio(): Radio
    {
        return new Radio();
    }

    /**
     * @return Dimensions
     */
    public function fieldDimensions(): Dimensions
    {
        return new Dimensions();
    }

    /**
     * @return Import
     */
    public function fieldImport(): Import
    {
        return new Import();
    }

    /**
     * @return ClearCache
     */
    public function fieldClearCache(): ClearCache
    {
        return new ClearCache();
    }

    /**
     * @return Switcher
     */
    public function fieldSwitcher(): Switcher
    {
        return new Switcher();
    }

    /**
     * @return Raw
     */
    public function fieldRaw(): Raw
    {
        return new Raw();
    }

    public function fieldAccordion(): Accordion
    {
        return new Accordion();
    }

    public function requiredRule(string $fieldName, string $operation, $value): array
    {
        $availableOperationList = [
            '=',
            'equals',
            '!=',
            'not',
            '>',
            'greater',
            'is_larger',
            '>=',
            'greater_equal',
            'is_larger_equal',
            '<',
            'less',
            'is_smaller',
            '<=',
            'less_equal',
            'is_smaller_equal',
            'contains',
            'doesnt_contain',
            'not_contain',
            'is_empty_or',
            'not_empty_and',
        ];

        if (!in_array($operation, $availableOperationList)) {
            throw new InvalidConfigException(get_class($this) . ':requiredRule. Non available operation passed');
        }

        return [
            $this->optionPath . '_' . $fieldName,
            $operation,
            $value,
        ];
    }
}

