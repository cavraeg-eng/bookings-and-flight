<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 * @var TravelpayoutsException $error
 * @var string $version
 * @var Template $this
 * @var boolean $isAdmin
 */
use Travelpayouts\Vendor\League\Plates\Template\Template;
use Travelpayouts\components\exceptions\TravelpayoutsException;

$backButtonText = function_exists('__') ? __('&laquo; Back') : '&laquo; Back';
?>
<style>
    :root {
        --disabled-bg-color: #f4f4f4;
        --brand-blue-color: #0085ff;
        --main-text-color: #262626;
        --steps-helper-color: #32343e;
    }

    body {
        color: var(--main-text-color)
    }

    .header {
        display: flex;
        align-items: center;
        color: var(--brand-blue-color);
        border-bottom: 1px solid #f4f4f4;
        padding: 0 0 10px 0;
        justify-content: center;
    }

    .header__title {
        margin: 0 0 0 10px;
        font-weight: 500;
        font-size: 32px;
    }

    .exception {
        margin: 10px 0 0 0;
    }

    .exception__title {
        border-width: 0;
        margin: 0 0 10px 0;
        padding: 0;
        font-weight: 500;
        color: var(--steps-helper-color);
    }

    .exception__description {
        font-weight: 400;
        font-size: 18px;
        margin: 10px 0;
    }

    .exception__source {
        margin: 15px 0 0 0;
    }

    pre {
        white-space: break-spaces;
        background: var(--disabled-bg-color);
        padding: 10px;
        word-break: break-all;
        border-radius: 4px;
    }

    .footer {
        border-top: 1px solid #f4f4f4;
        padding: 15px 0 0 0;
        margin: 15px 0 0 0;
    }
</style>
<div class="header">
    <div>
        <svg width="24" height="36" viewBox="0 0 24 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M23.46 25.926h-6.18a.96.96 0 01-.96-.96V20.16h5.634a.54.54 0 00.533-.45l.382-2.28a.54.54 0 00-.533-.63H12.96v8.166a4.32 4.32 0 004.32 4.32h3.36v2.499c-1.662.632-3.085.855-4.918.855-4.678 0-7.432-2.597-7.562-7.2V16.8H.922a.54.54 0 00-.533.45l-.381 2.28a.54.54 0 00.532.63H4.8v5.28c.074 2.88.821 5.524 2.906 7.625C9.61 34.986 12.382 36 15.722 36c2.813 0 4.945-.465 7.727-1.766a.958.958 0 00.551-.868v-6.9a.54.54 0 00-.54-.54zM1.665 13.44H8.16V6.23l4.8-1.7v8.91h10.118a.54.54 0 00.533-.45l.381-2.28a.54.54 0 00-.532-.63h-7.14V.54a.54.54 0 00-.72-.509L5.16 3.728a.54.54 0 00-.36.51v5.842H2.046a.54.54 0 00-.532.45l-.382 2.28a.54.54 0 00.533.63z"
                  fill="#0085FF"></path>
        </svg>
    </div>
    <h2 class="header__title">Travelpayouts plugin (<?= $this->e($version) ?>)</h2>
</div>
<div class="exception">
    <?php if ($isAdmin): ?>
        <h1 class="exception__title"><?= $this->e($error->getName()) ?></h1>
        <div class="exception__description"><?= $this->batch($error->getMessage(), 'nl2br|trim') ?></div>
        <pre class="exception__source">in <?= $this->e(plugin_basename($error->getFile())) ?> at line <?= $this->e($error->getLine()) ?></pre>
    <?php else: ?>
        <h1 class="exception__title"><?= Travelpayouts::__('Error') ?></h1>
        <div class="exception__description">
            <?= Travelpayouts::__('The above error occurred while the Web server was processing your request.') ?>
        </div>
    <?php endif ?>
    <div class="footer">
        <a href="javascript:history.back()" class="button button-large"> <?= $backButtonText ?></a>
    </div>
</div>

