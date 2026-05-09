<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\admin\redux\base;
use Travelpayouts\Vendor\Adbar\Dot;
use Travelpayouts\components\section\fields\BaseField;

/**
 * Interface IModuleSectionFields
 * @package Travelpayouts\admin\redux\base
 * @property-read string $id
 * @property-read Dot $data
 */
interface IModuleSectionFields
{
    /**
     * @return array | BaseField[] | string[]
     */
    public function fields(): array;

    /**
     * Путь к опции текущего класса
     * @return string
     */
    public function optionPath(): string;
}
