<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Travelpayouts\Vendor\Glook\YiiGrid\DataView;

use Exception;
use Travelpayouts\Vendor\Glook\YiiGrid\Base\Component;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Html;
use Travelpayouts\Vendor\Glook\YiiGrid\Helpers\Arrays\ArrayHelper;

/**
 * BaseListView is a base class for widgets displaying data from data provider
 * such as ListView and GridView.cd
 * It provides features like sorting, paging and also filtering the data.
 * For more details and usage information on BaseListView, see the [guide article on data widgets](guide:output-data-widgets).
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class BaseListView extends Component
{
    /**
     * @var array the HTML attributes for the container tag of the list view.
     *            The "tag" element specifies the tag name of the container element and defaults to "div".
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var \yii\data\DataProviderInterface the data provider for the view. This property is required.
     */
    public $dataProvider;

    /**
     * @var bool whether to show an empty list view if [[dataProvider]] returns no data.
     *           The default value is false which displays an element according to the [[emptyText]]
     *           and [[emptyTextOptions]] properties.
     */
    public $showOnEmpty = false;
    /**
     * @var string|false the HTML content to be displayed when [[dataProvider]] does not have any data.
     *                   When this is set to `false` no extra HTML content will be generated.
     *                   The default value is the text "No results found." which will be translated to the current application language.
     * @see showOnEmpty
     * @see emptyTextOptions
     */
    public $emptyText;
    /**
     * @var array the HTML attributes for the emptyText of the list view.
     *            The "tag" element specifies the tag name of the emptyText element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $emptyTextOptions = ['class' => 'empty'];
    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     *             The following tokens will be replaced with the corresponding section contents:
     * - `{items}`: the list items. See [[renderItems()]].
     */
    public $layout = '{items}';

    /**
     * Renders the data models.
     * @return string the rendering result.
     */
    abstract public function renderItems();

    /**
     * Initializes the view.
     */
    public function init()
    {
        parent::init();
        if ($this->dataProvider === null) {
            throw new Exception('The "dataProvider" property must be set.');
        }
        if ($this->emptyText === null) {
            $this->emptyText = 'No results found.';
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
            $content = preg_replace_callback('/{\\w+}/', function ($matches) {
                $content = $this->renderSection($matches[0]);

                return $content === false ? $matches[0] : $content;
            }, $this->layout);
        } else {
            $content = $this->renderEmpty();
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $content, $options);
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{items}`.
     * @return string|bool the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{items}':
                return $this->renderItems();
            default:
                return false;
        }
    }

    /**
     * Renders the HTML content indicating that the list view has no data.
     * @return string the rendering result
     * @see emptyText
     */
    public function renderEmpty()
    {
        if ($this->emptyText === false) {
            return '';
        }
        $options = $this->emptyTextOptions;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        return Html::tag($tag, $this->emptyText, $options);
    }
}
