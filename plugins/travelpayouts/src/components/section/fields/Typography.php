<?php

namespace Travelpayouts\components\section\fields;

class Typography extends Select2
{
    public $type = 'typography';
    public $output = ['h2.site-description'];
    public $units = 'px';
    public $subsets = false;
    public $default = [
        'color' => '#333',
        'font-weight' => '400',
        'font-family' => 'Arial, Helvetica, sans-serif',
        'font-size' => '22px',
        'line-height' => '24px',
        'text-align' => 'left'
    ];

    /**
     * @param $value
     * @return $this
     */
    public function setOutput($value): Typography
    {
        $this->output = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setUnits($value): Typography
    {
        $this->units = $value;

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setSubsets(bool $value): Typography
    {
        $this->subsets = $value;

        return $this;
    }
}