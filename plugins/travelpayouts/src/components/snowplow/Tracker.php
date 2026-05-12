<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\snowplow;
use Travelpayouts\Vendor\Snowplow\Tracker\Subject;
use Travelpayouts\components\BaseObject;

class Tracker extends \Travelpayouts\Vendor\Snowplow\Tracker\Tracker
{
	const CATEGORY_INSTALL = 'install';
	// Событие отправляется при установки плагина
	const ACTION_INSTALLED = 'plugin_installed';
	// событие отправляетс после того как пользователь ввел свой маркер и сохранил настройки
	const ACTION_ACTIVATED = 'plugin_activated';
	// обновление версии плагина с предыдущей
	const ACTION_UPDATED = 'plugin_updated';
	// Событие отправляется при удалении плагина (нажатии на "Deactivate" в списке плагинов)
	const ACTION_UNINSTALLED = 'plugin_uninstalled';
	/**
	 * @var string
	 */
	public $url;
	/**
	 * @var string|null
	 */
	public $protocol;
	/**
	 * @var string|null
	 */
	public $post_type;
	/**
	 * @var int|null
	 */
	public $buffer_size;
	/**
	 * @var boolean
	 */
	public $debug = false;
	/**
	 * @var string|null
	 */
	public $namespace;
	/**
	 * @var string|null
	 */
	public $app_id;
	/**
	 * @var SyncEmitter
	 */
	private $_emitter;

	public $encode_base64 = false;

	protected $_context = [];

	public function __construct($config = [])
	{
		BaseObject::configure($this, $config);
		parent::__construct($this->getEmitter(), new Subject(), $this->namespace, $this->app_id, $this->encode_base64);
	}

	/**
	 * @inheritdoc
	 */
	public function trackPageView($page_url, $page_title = null, $referrer = null, $context = null, $tstamp = null)
	{
		parent::trackPageView(
			$page_url,
			$page_title,
			$referrer,
			$this->mergeContext($context),
			$tstamp
		);
	}

	/**
	 * @inheritdoc
	 */
	public function trackStructEvent($category,
									 $action,
									 $label = null,
									 $property = null,
									 $value = null,
									 $context = [],
									 $tstamp = null)
	{
		parent::trackStructEvent($category,
			$action,
			$label,
			$property,
			$value,
			$this->mergeContext($context),
			$tstamp);
	}

	/**
	 * @inheritdoc
	 */
	public function trackUnstructEvent($event_json,
									   $context = [],
									   $tstamp = null)
	{
		parent::trackUnstructEvent($event_json,
			$this->mergeContext($context),
			$tstamp);
	}

	/**
	 * @inheritdoc
	 */
	public function trackEcommerceTransaction($order_id,
											  $total_value,
											  $currency = null,
											  $affiliation = null,
											  $tax_value = null,
											  $shipping = null,
											  $city = null,
											  $state = null,
											  $country = null,
											  $items = [],
											  $context = [],
											  $tstamp = null)
	{
		parent::trackEcommerceTransaction($order_id,
			$total_value,
			$currency,
			$affiliation,
			$tax_value,
			$shipping,
			$city,
			$state,
			$country,
			$items,
			$this->mergeContext($context),
			$tstamp);
	}

	public function setContext($context)
	{
		$this->_context = $context;
	}

	/**
	 * Объединяем базовый контекст с контекстом представленным в аргументе функции
	 * @param array $context
	 * @return array
	 */
	protected function mergeContext($context = [])
	{
		$context = array_merge(
			$this->_context,
			$context
		);

		return $this->formatContext($context);
	}

	/**
	 * Оборачиваем еще раз в архив потому что такой формат запланирован на другой стороне
	 * @param $context
	 * @return array[]
	 */
	protected function formatContext($context)
	{
		return [
			[
				'schema' => 'event',
				'data' => $context,
			],
		];
	}

	/**
	 * @return SyncEmitter
	 */
	protected function getEmitter()
	{
		if (!$this->_emitter) {
			$this->_emitter = new SyncEmitter(
				$this->url,
				$this->protocol,
				$this->post_type,
				$this->buffer_size,
				$this->debug
			);
		}
		return $this->_emitter;
	}
}
