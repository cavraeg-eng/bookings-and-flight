<?php

/**
 * Register all actions and filters for the plugin
 * @link       http://www.travelpayouts.com/?locale=en
 * @since      1.0.0
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 */

namespace Travelpayouts\includes;

use Travelpayouts\components\HookableObject;
use Travelpayouts\helpers\ArrayHelper;

/**
 * Register all actions and filters for the plugin.
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 * @package    Travelpayouts
 * @subpackage Travelpayouts/includes
 * @author     travelpayouts < wpplugin@travelpayouts.com>
 */
class HooksLoader
{
    /**
     * @var string[]
     */
    protected $registeredClassList = [];

    /**
     * Add a new action to the collection to be registered with WordPress.
     * @param string $hook The name of the WordPress action that is being registered.
     * @param callable $callable The name of the function you wish to be called.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $acceptedArgsCount Optional. The number of arguments that should be passed to the $callback. Default
     *     is 1.
     * @return self
     */
    public function addAction($hook, $callable, $priority = 10, $acceptedArgsCount = 1)
    {
        add_action($hook,
            $callable,
            $priority,
            $acceptedArgsCount);

        return $this;
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     * @param string $hook The name of the WordPress action that is being registered.
     * @param callable $callable The name of the function you wish to be called.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $acceptedArgsCount Optional. The number of arguments that should be passed to the $callback. Default
     *     is 1.
     * @return self
     */
    public function addFilter($hook, $callable, $priority = 10, $acceptedArgsCount = 1)
    {

        add_filter($hook, $callable, $priority,$acceptedArgsCount);
        return $this;
    }

    /**
     * Add a new shortcode to the collection to be registered with WordPress.
     * @param string $name The name of shortcode
     * @param callable $callable The name of the function you wish to be called.
     */
    public function addShortcode($name, $callable)
    {
        add_shortcode($name,$callable);
        return $this;
    }

    /**
     * Fires authenticated Ajax actions for logged-in users.
     * @param string $hook The name of the WordPress action that is being registered.
     * @param callable $callable The name of the function you wish to be called.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $acceptedArgsCount Optional. The number of arguments that should be passed to the $callback. Default
     */
    public function addAdminAjaxEndpoint($hook, $callable, $priority = 10, $acceptedArgsCount = 1)
    {
        return $this->addAction("wp_ajax_$hook", $callable, $priority, $acceptedArgsCount);
    }

    /**
     * Fires non-authenticated Ajax actions for logged-out users.
     * @param string $hook The name of the WordPress action that is being registered.
     * @param callable $callable The name of the function you wish to be called.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $acceptedArgsCount Optional. The number of arguments that should be passed to the $callback. Default
     */
    public function addPublicAjaxEndpoint($hook, $callable, $priority = 10, $acceptedArgsCount = 1)
    {
        return $this->addAction("wp_ajax_nopriv_$hook", $callable, $priority, $acceptedArgsCount);
    }

    /**
     * Проверяем на наличие названия класса и заносим в список в случае отсутствия
     * @param HookableObject $object
     * @return bool
     * @see HookableObject::setUpHooks();
     */
    public function isRegistered(HookableObject $object)
    {
        $className = get_class($object);
        if (!in_array($className, $this->registeredClassList, true)) {
            $this->registeredClassList = ArrayHelper::addItem($this->registeredClassList, $className);
            return true;
        }
        return false;
    }

    /**
     * Регистрируем HookableObject
     * @param HookableObject $object
     * @return $this
     */
    public function registerHooksInstance(HookableObject $object)
    {
        $object->setUpHooks();
        return $this;
    }
}
