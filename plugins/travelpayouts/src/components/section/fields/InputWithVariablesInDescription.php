<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\section\fields;

use Travelpayouts;
use Travelpayouts\components\HtmlHelper;
use Travelpayouts\helpers\ArrayHelper;

class InputWithVariablesInDescription extends Input
{
    /**
     * @var Array<string,string>
     */
    public $variables = [];

    public function setVariables(array $values): self
    {
        if (ArrayHelper::isAssociative($values)) {
            $this->variables = $values;
        }
        return $this;
    }

    protected function renderVariables(): array
    {
        $result = [];
        foreach ($this->variables as $variableName => $variableLabel) {
            $result[] = HtmlHelper::tagArrayContent('div', [
                'class' => HtmlHelper::classNames([
                    'tp-align-items-center',
                    'tp-flex',
                    'tp-mt-2',
                    'tp-gap-2'
                ]),
            ],
                [
                    HtmlHelper::tag('div', [
                        'class' =>
                            HtmlHelper::classNames([
                                'tp-badge',
                                'tp-badge--primary',
                                'tp-font-rubik',
                                'tp-text-xs',
                            ]),
                    ], '{' . $variableName . '}'),
                    HtmlHelper::tag('div', ['class' => HtmlHelper::classNames(['tp-text--black'])], $variableLabel),
                ]);
        }
        return $result;
    }

    public function renderVariablesList(): ?string
    {
        if (!empty($this->variables)) {
            return HtmlHelper::tagArrayContent('div',
                [
                    'class' => 'tp-input-with-tags__variables-list tp-stack-3',
                ], array_merge([
                    HtmlHelper::tag('div', [
                        'class' => HtmlHelper::classNames(['tp-font-rubik', 'tp-fs-em-7','tp-text--black']),
                    ], Travelpayouts::__('You can use the following variables in this field:')),
                ], $this->renderVariables()));
        }

        return null;
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'desc' => function () {
                return $this->renderVariablesList();
            },
        ]);
    }

}