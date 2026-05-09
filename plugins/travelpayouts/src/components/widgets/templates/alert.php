<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 * @var \Travelpayouts\Vendor\League\Plates\Template\Template $this
 * @var \Travelpayouts\components\widgets\AlertWidget $_widget
 * @var string $content
 * @var string $type
 * @var bool $showRoundel
 * @var string|null $title
 */

$alertType = $_widget->getAlertType();
?>

<div class="tp-alert tp-alert tp-alert--<?= $alertType ?>">
    <?php if ($showRoundel): ?>
        <div class="tp-roundel tp-roundel--<?= $alertType ?> tp-roundel--sm">
            <i class="tp-i-tabler:exclamation-mark"></i>
        </div>
    <?php endif; ?>
    <div class="tp-alert-content">
        <?= $content ?>
    </div>
</div>

