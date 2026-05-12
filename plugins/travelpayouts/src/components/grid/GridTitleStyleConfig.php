<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\grid;

use Travelpayouts\components\BaseObject;
use Travelpayouts\components\HtmlHelper;

/**
 * Класс для получения
 * @property-write  bool $className
 */
class GridTitleStyleConfig extends BaseObject
{

    public $inlineCss = [];
    /**
     * @var string|null
     */
    protected $_titleClassName;

    public $useInlineCss = false;

    /**
     * @return string|null
     */
    protected function getClassName(): ?string
    {
        return $this->_titleClassName;
    }

    /**
     * @param mixed $titleClassName
     */
    public function setClassName($titleClassName): void
    {
        if (is_string($titleClassName) && !empty($titleClassName)) {
            $this->_titleClassName = $titleClassName;
        }
    }

    public function getHtmlOptions(): array
    {
        $htmlOptions = [];
        $className = $this->getClassName();

        if ($this->useInlineCss) {
            return array_merge($htmlOptions, [
                'style' => $this->getStyleHtmlOption(),
            ]);
        }

        if ($className) {
            return array_merge($htmlOptions, [
                'class' => $className,
            ]);
        }
        return [
            'class'=> 'tp-widget-table-title',
        ];
    }

    /**
     * @return string
     */
    protected function getStyleHtmlOption(): string
    {
        $skipProperties = [
            'google',
        ];
        $result = [];
        foreach (array_filter($this->inlineCss) as $key => $value) {
            if (!in_array($key, $skipProperties, true)) {
                $result[$key] = "$value !important";
            }
        }
        return HtmlHelper::cssStyleFromArray($result);
    }

}
