<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;

use Exception;
use http\Exception\RuntimeException;
use Travelpayouts\Vendor\League\Plates\Engine;

abstract class BaseWidget extends BaseObject
{
    /**
     * @var Engine
     */
    protected $plates;

    /**
     * @var string ID виджета
     */
    public $id;

    /**
     * @var string Путь к директории с шаблонами
     */
    protected $templatePath;

    /**
     * @var array
     */
    protected static $stack = [];

    private static $_resolvedClasses = [];

    /**
     * Инициализация виджета
     */
    public function init()
    {
        if ($this->id === null) {
            $this->id = 'widget-' . uniqid();
        }
    }

    abstract public function run(): string;

    /**
     * @param array $config
     * @return string
     */
    public static function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            $config['class'] = get_called_class();
            $widget = new static($config);
            $out = '';
            if ($widget->beforeRun()) {
                $result = $widget->run();
                $out = $widget->afterRun($result);
            }
        } catch (\Exception $e) {
            // close the output buffer opened above if it has not been closed already
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        } catch (\Throwable $e) {
            // close the output buffer opened above if it has not been closed already
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean() . $out;
    }

    public function beforeRun(): bool
    {
        return true;
    }

    public function afterRun($result): string
    {

        return $result;
    }

    /**
     * Рендеринг шаблона
     * @param string $view Имя шаблона
     * @param array $data Данные для шаблона
     * @return string
     * @throws Exception
     */
    protected function render(string $view, array $data = []): string
    {
        $plates = $this->getPlatesEngine();
        $plates->addData(array_merge(\Closure::fromCallable("get_object_vars")->__invoke($this), [
            '_widget' => $this,
            'id' => $this->id,
        ]));
        return $plates->render($view, $data);
    }

    /**
     * @return Engine
     * @throws Exception
     */
    protected function getPlatesEngine(): Engine
    {
        if ($this->plates === null) {
            $this->plates = $this->createPlatesEngine();
        }

        return $this->plates;
    }

    /**
     * @return Engine
     * @throws Exception
     */
    protected function createPlatesEngine(): Engine
    {
        $templatePath = $this->getTemplatePath();
        if (!is_dir($templatePath)) {
            throw new Exception("Template path not found: {$templatePath}");
        }
        return new Engine($templatePath);
    }

    /**
     * Получение пути к шаблонам
     * @return string
     */
    protected function getTemplatePath(): string
    {
        if ($this->templatePath !== null) {
            return $this->templatePath;
        }
        $dir = dirname((new \ReflectionClass($this))->getFileName());
        return $dir . '/templates';
    }

    /**
     * @return static
     */
    public static function begin($config = []): self
    {
        $widget = new static($config);
        self::$stack[] = $widget;
        return $widget;
    }

    /**
     * Окончание захвата вывода
     * @return static
     * @throws Exception
     */
    public static function end(): self
    {
        if (!empty(self::$stack)) {
            $widget = array_pop(self::$stack);

            $calledClass = self::$_resolvedClasses[get_called_class()] ?? get_called_class();

            if (get_class($widget) === $calledClass) {
                /** @var static $widget */
                if ($widget->beforeRun()) {
                    $result = $widget->run();
                    $result = $widget->afterRun($result);
                    echo $result;
                }

                return $widget;
            }

            throw new Exception('Expecting end() of ' . get_class($widget) . ', found ' . get_called_class());
        }

        throw new Exception('Unexpected ' . get_called_class() . '::end() call. A matching begin() is not found.');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->run();
        } catch (Exception $e) {
            return '<!-- Widget Error: ' . htmlspecialchars($e->getMessage()) . ' -->';
        }
    }
}