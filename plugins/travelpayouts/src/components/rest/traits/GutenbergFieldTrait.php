<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\rest\traits;

use Travelpayouts\components\rest\fields\Autocomplete;
use Travelpayouts\components\rest\fields\Checkbox;
use Travelpayouts\components\rest\fields\DatePicker;
use Travelpayouts\components\rest\fields\Input;
use Travelpayouts\components\rest\fields\InputNumber;
use Travelpayouts\components\rest\fields\InputTag;
use Travelpayouts\components\rest\fields\InputWithVariablesInDescription;
use Travelpayouts\components\rest\fields\Line;
use Travelpayouts\components\rest\fields\RadioFields;
use Travelpayouts\components\rest\fields\Select;
use Travelpayouts\components\rest\fields\SelectAsync;
use Travelpayouts\components\rest\fields\Text;

trait GutenbergFieldTrait
{
    /**
     * @return Input
     */
    public function fieldInput(): Input
    {
        return new Input();
    }

    /**
     * @return InputWithVariablesInDescription
     */
    public function fieldInputWithVariablesInDescription(): InputWithVariablesInDescription
    {
        return new InputWithVariablesInDescription();
    }

    /**
     * @return Line
     */
    public function fieldHr(): Line
    {
        return new Line();
    }

    /**
     * @return Text
     */
    public function fieldText(): Text
    {
        return new Text();
    }

    /**
     * @return InputTag
     */
    public function fieldInputTag(): InputTag
    {
        return new InputTag();
    }

    /**
     * @return Autocomplete
     */
    public function fieldInputAutocomplete(): Autocomplete
    {
        return new Autocomplete();
    }

    /**
     * @return InputNumber
     */
    public function fieldInputNumber(): InputNumber
    {
        return new InputNumber();
    }

    /**
     * @return Select
     */
    public function fieldSelect(): Select
    {
        return new Select();
    }

    /**
     * @return SelectAsync
     */
    public function fieldSelectAsync(): SelectAsync
    {
        return new SelectAsync();
    }

    /**
     * @return Checkbox
     */
    public function fieldCheckbox(): Checkbox
    {
        return new Checkbox();
    }

    /**
     * @return DatePicker
     */
    public function fieldDatePicker(): DatePicker
    {
        return new DatePicker();
    }

    /**
     * @return RadioFields
     */
    public function fieldRadioFields(): RadioFields
    {
        return new RadioFields();
    }

    /**
     * @return Autocomplete
     */
    public function fieldDirectionAutocomplete(): Autocomplete
    {
        return $this->fieldInputAutocomplete()->setAsync([
            'url' => $this->prepareEndpoint('//autocomplete.travelpayouts.com/places2?term=${term}&locale=${locale}&types[]=city'),
            'itemProps' => [
                'value' => '${code}',
                'label' => '${name}, ${country_name}',
            ],
        ]);
    }

}