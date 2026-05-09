<?php
/**
 * @var string $buttonTitle
 * @var array $buttonParams
 * @var Travelpayouts\Vendor\League\Plates\Template\Template $this
 * @var string $formId
 */

Travelpayouts::getInstance()->assets->loader->registerAsset('admin-feedback-button');
?>

<div class="travelpayouts-chunk" style="display: none">
    <div class="travelpayouts-feedback-form__wrapper">
        <a class="btn tp-button tp-button--primary tp-button--lg"
           href="https://form.typeform.com/to/<?= $formId ?>#<?= http_build_query($buttonParams) ?>"
           target="_blank">🐞 <?= $this->e($buttonTitle) ?></a>
    </div>
</div>

