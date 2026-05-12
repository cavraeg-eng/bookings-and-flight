<?php
/**
 * @var boolean $allowClose
 * @var string $name
 * @var string $type
 * @var array $buttons
 * @var string $showAlertDialog
 */

use Travelpayouts\components\HtmlHelper;
use Travelpayouts\helpers\FileHelper;

$classNames = HtmlHelper::classNames(array_filter([
    'travelpayouts-notice',
    $type ? "travelpayouts-notice--{$type}" : null,
    $allowClose ? "travelpayouts-notice--closeable" : null,
]));

?>
<div class="<?= $classNames ?>" data-tp-notification="<?= $name ?>">
    <div class="wlcm__plug__content travelpayouts-notice__content">
        <div class="wlcm__plug__logo travelpayouts-notice__logo">
            <?= FileHelper::requireAssetByAlias('@images/admin/panel/logo-notice.svg') ?>
        </div>
        <div class="wlcm__plug__text travelpayouts-notice__text">
            <?php if (isset($title)): ?>
                <div class="travelpayouts-notice__text__title"><?= $title ?></div>
            <?php endif ?>
            <?php if (isset($description)): ?>
                <div class="travelpayouts-notice__text__description"><?= $description ?></div>
            <?php endif ?>
        </div>
    </div>
    <?php if (is_array($buttons) && !empty($buttons)): ?>
        <div class="wlcm__plug__control travelpayouts-notice__action-bar">
            <?php
            foreach ($buttons as $button) {
                echo $button;
            }
            ?>
        </div>
    <?php endif; ?>
    <?php if ($allowClose): ?>
        <div class="travelpayouts-notice__close">
            <a href="#" class="travelpayouts-notice__close__btn"
               data-tp-notification-close="<?= $name ?>"
               data-show-alert-dialog="<?= $showAlertDialog ?>"
            >
                <svg class="travelpayouts-notice__close__icon" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 511.995 511.995">
                    <path d="M437.126 74.939c-99.826-99.826-262.307-99.826-362.133 0C26.637 123.314 0 187.617 0 256.005s26.637 132.691 74.993 181.047c49.923 49.923 115.495 74.874 181.066 74.874s131.144-24.951 181.066-74.874c99.826-99.826 99.826-262.268.001-362.113zM409.08 409.006c-84.375 84.375-221.667 84.375-306.042 0-40.858-40.858-63.37-95.204-63.37-153.001s22.512-112.143 63.37-153.021c84.375-84.375 221.667-84.355 306.042 0 84.355 84.375 84.355 221.667 0 306.022z"/>
                    <path d="M341.525 310.827l-56.151-56.071 56.151-56.071c7.735-7.735 7.735-20.29.02-28.046-7.755-7.775-20.31-7.755-28.065-.02l-56.19 56.111-56.19-56.111c-7.755-7.735-20.31-7.755-28.065.02-7.735 7.755-7.735 20.31.02 28.046l56.151 56.071-56.151 56.071c-7.755 7.735-7.755 20.29-.02 28.046 3.868 3.887 8.965 5.811 14.043 5.811s10.155-1.944 14.023-5.792l56.19-56.111 56.19 56.111c3.868 3.868 8.945 5.792 14.023 5.792a19.828 19.828 0 0014.043-5.811c7.733-7.756 7.733-20.311-.022-28.046z"/>
                </svg>
            </a>
        </div>
    <?php endif; ?>
</div>
