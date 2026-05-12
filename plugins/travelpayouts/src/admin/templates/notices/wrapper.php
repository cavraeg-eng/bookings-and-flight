<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 * @var Template $this
 * @var array $noticeList
 * @var Notices $noticesInstance
 */
use Travelpayouts\Vendor\League\Plates\Template\Template;
use Travelpayouts\components\notices\Notices;

Travelpayouts::getInstance()->assets->loader->registerAsset('admin-notice');
?>
<div class="travelpayouts-chunk" style="display: none">
    <div class="travelpayouts-notice-wrapper">
        <?php foreach ($noticeList as $notice) {
            if (is_array($notice) && isset($notice['name'])) {
                $this->insert('admin::notices/notice', array_merge($notice, [
                    'showAlertDialog' => $noticesInstance->isShowAlertDialog($notice['name']),
                ]));
            }
            if (is_string($notice)) {
                echo $notice;
            }
        }
        ?>
    </div>
    <?= $this->insert('admin::notices/_notificationCloseDialog') ?>
</div>
