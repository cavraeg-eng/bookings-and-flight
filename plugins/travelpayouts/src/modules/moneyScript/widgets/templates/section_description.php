<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 * @var MoneyScriptSectionDescriptionWidget $_widget
 * @var \Travelpayouts\Vendor\League\Plates\Template\Template $this
 */

use Travelpayouts\components\widgets\AlertWidget;
use Travelpayouts\modules\moneyScript\widgets\MoneyScriptSectionDescriptionWidget;

$this->start('anchorLink');
?>
<a href='<?= $_widget->getSupportUrl() ?>' class='tp-link' target='_blank'>
    <?= Travelpayouts::_x('Knowledge Base', 'moneyscript knowledge url title') ?>
</a>
<?php
$this->end();

AlertWidget::begin([
    'type' => Travelpayouts\components\widgets\AlertWidget::TYPE_INFO,
    'showRoundel' => true,
]);
?>
<div class='tp-stack-2'>
    <div><?= Travelpayouts::_x('With Money Script you can quickly replace links to travel resources such as Booking.com, Kiwitaxi and others with Travelpayouts affiliate links.',
            'moneyscript description text') ?>
    </div>
    <div>
        <?=Travelpayouts::_x('Find out more in our {link}.', 'moneyscript description text', [
            'link' => $this->section('anchorLink'),
        ])?>
    </div>
</div>
<?php AlertWidget::end(); ?>
