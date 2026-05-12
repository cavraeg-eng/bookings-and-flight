<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components;
use Travelpayouts\Vendor\DI\Container as DiContainer;
use Travelpayouts\Vendor\DI\ContainerBuilder;
use Travelpayouts\Vendor\DI\DependencyException;
use Exception;
use Travelpayouts\traits\SingletonTrait;

/**
 * Class Container
 * @package Travelpayouts\components
 * @property-read DiContainer $container
 */
class Container extends BaseObject
{
	use SingletonTrait;

	/**
	 * @var DiContainer
	 */
	protected $_container;

	/**
	 * @return string[]
	 */
	protected function definitions()
	{
		return [
			dirname(__DIR__) . '/config/definitions.php',
			dirname(__DIR__) . '/config/definitions.translations.php',
			dirname(__DIR__) . '/config/definitions.components.php',
			dirname(__DIR__) . '/config/definitions.modules.php',
		];
	}

	public function init()
	{
		$builder = new ContainerBuilder();
		$builder->useAnnotations(true);
		foreach ($this->definitions() as $definition) {
			$builder->addDefinitions($definition);
		}
		$container = $builder->build();
		$this->_container = $container;
	}

	/**
	 * @return DiContainer
	 */
	public function getContainer()
	{
		return $this->_container;
	}

	/**
	 * @param object $instance
	 * @throws DependencyException
	 */
	public function inject($instance)
	{
		$container = $this->container;
		if ($container instanceof DiContainer) {
			$container->injectOn($instance);
		} else {
			throw new Exception('Cant find container');
		}
	}
}
