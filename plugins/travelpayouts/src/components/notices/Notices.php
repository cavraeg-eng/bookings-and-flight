<?php

namespace Travelpayouts\components\notices;
use Travelpayouts\Vendor\League\Plates\Engine;
use Travelpayouts\components\BaseInjectedObject;

/**
 * Class Notices
 * @package Travelpayouts\src\components
 */
class Notices extends BaseInjectedObject
{
    /**
     * @Inject
     * @var Engine
     */
    protected $template;

    /**
     * @var array
     */
    protected $_visibleNotices;

    protected $cookiePrefix = 'tp-notice-';

    /**
     * @return mixed|void
     */
    protected function getNotices()
    {
        return get_option($this->optionName(), []);
    }

    /**
     * Отображает уведомления и очищает их сразу после отображения
     */
    public function render(): void
    {
        $visibleNotices = $this->getVisibleNotices();
        if (!empty($visibleNotices)) {
            echo $this->template->render(
                'admin::notices/wrapper',
                [
                    'noticeList' => $visibleNotices,
                    'noticesInstance' => $this,
                ]
            );
            $this->clearAll();
        }
    }

    public function isShowAlertDialog(string $noticeName): string
    {
        return in_array($noticeName, $this->getRequestedAlerts(), true) ? '1' : '0';
    }

    /**
     * Список уведомлений которые не были скрыты пользователем
     * @return array
     */
    protected function getVisibleNotices(): array
    {
        if($this->_visibleNotices === null) {
            $noticeList = $this->getNotices();
            $disabledName = $this->getDisabledNames();
            $result = [];
            foreach ($noticeList as $noticeName => $noticeContent) {
                $str = $this->cookiePrefix . $noticeName;
                if (!isset($_COOKIE[$str]) && !in_array($noticeName, $disabledName, true)) {
                    $result[] = $noticeContent;
                }
            }
            $this->_visibleNotices = $result;
        }
      return $this->_visibleNotices;
    }

    /**
     * @param $key
     * @return bool
     */
    public function clearByKey($key): bool
    {
        $notices = $this->getNotices();
        unset($notices[$key]);

        return $this->updateOption($notices);
    }

    /**
     * @return bool
     */
    protected function clearAll(): bool
    {
        return $this->updateOption([]);
    }

    /**
     * @param $data
     * @return bool
     */
    protected function updateOption($data): bool
    {
        return update_option($this->optionName(), $data);
    }

    /**
     * @return string
     */
    protected function optionName()
    {
        return TRAVELPAYOUTS_PLUGIN_NAME . '_notices';
    }

    protected function disabledOptionName()
    {
        return TRAVELPAYOUTS_PLUGIN_NAME . '_disabled_notices';
    }

    protected function alertsOptionName()
    {
        return TRAVELPAYOUTS_PLUGIN_NAME . '_requested_alerts';
    }

	public function add(Notice $notice)
	{
        $this->updateOption(array_merge($this->getNotices(), [$notice->name => $notice->getResult()]));
	}

    /**
     * @param $name
     * @return void
     */
    public function disable($name): void
    {
        $this->addOption($name, $this->disabledOptionName());
    }

    /**
     * @param $name
     * @return void
     */
    public function requestAlert(string $name): void
    {
        setcookie($this->cookiePrefix . $name, '1', strtotime( '+30 days' ) );
        $this->addOption($name, $this->alertsOptionName());
    }

    /**
     * Массив с именами отключенными уведомлениями
     * @return string[]
     */
    public function getDisabledNames(): array
    {
        return $this->getOption($this->disabledOptionName());
    }

    /**
     * Массив уведомлений которым пора показать диалоговое окно
     * @return array
     */
    public function getRequestedAlerts(): array
    {
        return $this->getOption($this->alertsOptionName());
    }

    /**
     * @param $option
     * @param $name
     * @return bool
     */
    private function addOption($option, $name): bool
    {
        if (!empty($option) && is_string($option)) {
            $data = get_option($name, []);
            $data[$this->getUserKey()][] = $option;
            return update_option($name, $data);
        }
        return false;
    }

    /**
     * @param $name
     * @return array
     */
    private function getOption($name): array
    {
        $option = get_option($name, []);

        if (isset($option[$this->getUserKey()])) {
            return $option[$this->getUserKey()];
        }

        return [];
    }

    /**
     * @return string
     */
    private function getUserKey(): string
    {
        return 'user_' . get_current_user_id();
    }
}
