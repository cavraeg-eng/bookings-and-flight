<?php

use Travelpayouts\admin\components\landingPage\LandingModel;
use Travelpayouts\admin\partials\LandingPage;
use Travelpayouts\helpers\FileHelper;

/**
 * @var boolean $isRU
 * @var string $createAccountUrl
 * @var string $apiTokenUrl
 * @var string $action
 * @var string $installPluginUrl
 * @var LandingModel $landingModel
 */

?>

<?= Travelpayouts::getInstance()->assets->loader->registerAsset('admin-landing-page')->renderPreloader() ?>
<div class="travelpayouts-chunk" style="display: none">

    <main class="wlcm">
        <div class="container">
            <section class="wlcm__top">
                <div class="wlcm__head">
                    <div class="wlcm__container wlcm__head__wrap">
                        <div class="wlcm__head__content">
                            <div class="wlcm__head__logo">
                                <?= FileHelper::requireAssetByAlias('@images/admin/panel/logo-wide.svg') ?>
                            </div>
                            <h2 class="wlcm__head__title">
                                <?php echo Travelpayouts::__('Earn by selling tourist services') ?>
                            </h2>
                            <p class="wlcm__head__text"><?php echo Travelpayouts::__('Install our convenient and useful tools on your website and help your visitors find the cheapest flights and hotels. Earn commission for each booking.'); ?></p>
                            <a href="<?php echo $createAccountUrl; ?>" target="_blank"
                               class="wlcm__head__btn">
                                <?php echo Travelpayouts::__('Create an account'); ?>
                                <small><?php echo Travelpayouts::__('go to travelpayouts.com'); ?></small>
                            </a>
                        </div>
                        <div class="wlcm__head__messages">
                            <div class="wlcm__head__bubble">
                                <strong class="wlcm__head__bubble__price"><?php echo Travelpayouts::__('+$50'); ?></strong>
                                <span class="wlcm__head__bubble__content"><?php echo Travelpayouts::__('Hotel, 2 people, <br>to Hong-Kong'); ?></span>
                            </div>
                            <div class="wlcm__head__bubble wlcm__head__bubble_right wlcm__head__bubble_second">
                                <strong class="wlcm__head__bubble__price"><?php echo Travelpayouts::__('+$27'); ?></strong>
                                <span class="wlcm__head__bubble__content"><?php echo Travelpayouts::__('Flight, 1 person,<br>to Prague'); ?></span>
                            </div>
                            <div class="wlcm__head__bubble wlcm__head__bubble_right wlcm__head__bubble_third">
                                <strong class="wlcm__head__bubble__price"><?php echo Travelpayouts::__('+$75'); ?></strong>
                                <span class="wlcm__head__bubble__content"><?php echo Travelpayouts::__('Flight, 1 person,<br>to San Francisco'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wlcm__tabs">
                    <div class="wlcm__container">
                        <div class="wlcm__tabs__head">
                            <a href="#tp_acc_exist" class="wlcm__toggler wlcm__toggler_active">
                <span class="wlcm__toggler__icon">
                  <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.251 4.5h2.377a2.25 2.25 0 014.244 0h6.879a.75.75 0 010 1.5h-6.879a2.251 2.251 0 01-4.244 0H5.251a.75.75 0 110-1.5zm4.499 0a.75.75 0 100 1.5.75.75 0 000-1.5zM5.251 10.5h6.877a2.251 2.251 0 014.244 0h2.379a.75.75 0 010 1.5h-2.379a2.251 2.251 0 01-4.244 0H5.251a.75.75 0 010-1.5zm8.999 0a.75.75 0 00-.75.723v.014l.001.013v.027a.75.75 0 10.749-.777z"/>
                  <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M.001 2.25A2.25 2.25 0 012.251 0h19.5a2.25 2.25 0 012.25 2.25v15a2.25 2.25 0 01-2.25 2.25h-7.422c.16 1.06.528 2.08 1.083 3h1.839a.75.75 0 010 1.5h-10.5a.75.75 0 010-1.5h1.837c.555-.92.922-1.94 1.083-3H2.25a2.25 2.25 0 01-2.25-2.25v-15zm1.5 0a.75.75 0 01.75-.75h19.5a.75.75 0 01.75.75V15h-21V2.25zM10.494 18H2.25a.75.75 0 01-.75-.75v-.75h21v.75a.75.75 0 01-.75.75H10.494zm.69 1.5a9.682 9.682 0 01-.891 3h3.414a9.682 9.682 0 01-.892-3h-1.63z"/>
                </svg>
                </span>
                                <span class="wlcm__toggler__text"><?php echo Travelpayouts::__('I have a Travelpayouts account'); ?></span>
                            </a>
                            <a href="#tp_acc_unexist" class="wlcm__toggler">
                <span class="wlcm__toggler__icon">
                  <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M7.5 0a4.875 4.875 0 100 9.75A4.875 4.875 0 007.5 0zM4.125 4.875a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0z"/>
                    <path d="M9.856 11.732A6.002 6.002 0 001.5 17.25a.75.75 0 01-1.5 0 7.503 7.503 0 0110.444-6.898.75.75 0 01-.588 1.38zM17.25 13.5a.75.75 0 01.75.75v2.25h2.25a.75.75 0 010 1.5H18v2.25a.75.75 0 01-1.5 0V18h-2.25a.75.75 0 010-1.5h2.25v-2.25a.75.75 0 01.75-.75z"/>
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M10.5 17.25a6.75 6.75 0 1113.5 0 6.75 6.75 0 01-13.5 0zM17.25 12a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5z"/>
                  </svg>
                </span>
                                <span class="wlcm__toggler__text"><?php echo Travelpayouts::__('New at Travelpayouts?'); ?></span>
                            </a>
                        </div>
                        <div class="wlcm__tabs__content wlcm__tabs__content_shown" id="tp_acc_exist">
                            <div class="wlcm__token">
                                <strong class="wlcm__token__title"><?php echo Travelpayouts::__('Enter your API token and Travelpayouts ID'); ?></strong>
                                <div class="wlcm__token__details">
                                    <img class="wlcm__token__details__image"
                                         src="<?php echo LandingPage::getLandingImage('notes.svg'); ?>"
                                         alt="">
                                    <span class="wlcm__token__details__text"><?php echo Travelpayouts::__('To identify you, we need your API token and affiliate ID'); ?></span>
                                    <a href="<?php echo $apiTokenUrl; ?>"
                                       target="_blank"
                                       class="wlcm__token__details__btn">
                                        <?php echo Travelpayouts::__('Get your API token or Partner ID'); ?>
                                    </a>
                                </div>
                                <form class="wlcm__token__form" method="POST"
                                      action="<?php echo admin_url('admin.php'); ?>">
                                    <?php wp_nonce_field($action); ?>
                                    <input type="hidden" name="action" value="<?php echo $action; ?>"/>
                                    <div class="wlcm__line">
                                        <div class="wlcm__col-6">
                                            <div class="wlcm__token__field">
                                                <label for="" class="wlcm__token__field__label">
                                                    <?php echo Travelpayouts::__('your API token'); ?>
                                                    <span class="wlcm__token__field__label__req">*</span>
                                                </label>
                                                <input name="token" type="text"
                                                       value="<?php echo $landingModel->getToken(); ?>"
                                                       class="wlcm__token__field__input" required>
                                            </div>
                                        </div>
                                        <div class="wlcm__col-6">
                                            <div class="wlcm__token__field">
                                                <label for="" class="wlcm__token__field__label">
                                                    <?php echo Travelpayouts::__('your affiliate ID'); ?>
                                                    <span class="wlcm__token__field__label__req">*</span>
                                                </label>
                                                <input name="marker" type="text"
                                                       value="<?php echo $landingModel->getMarker(); ?>"
                                                       class="wlcm__token__field__input" required>
                                            </div>
                                        </div>
                                    </div>
                                    <strong class="wlcm__token__title"><?php echo Travelpayouts::__('Select tables language and currency'); ?></strong>
                                    <div class="wlcm__line">
                                        <div class="wlcm__col-6">
                                            <div class="wlcm__token__field">
                                                <label for="" class="wlcm__token__field__label">
                                                    <?php echo Travelpayouts::__('widget and table language'); ?>
                                                </label>
                                                <select name="language"
                                                        class="wlcm__token__field__input wlcm__token__field__select">
                                                    <?php echo $landingModel->getLanguageOptions(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="wlcm__col-6">
                                            <div class="wlcm__token__field">
                                                <label for="" class="wlcm__token__field__label">
                                                    <?php echo Travelpayouts::__('currency'); ?>
                                                </label>
                                                <select name="currency"
                                                        class="wlcm__token__field__input wlcm__token__field__select">
                                                    <?php echo $landingModel->getCurrencyOptions(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wlcm__line wlcm__line_center">
                                        <div class="wlcm__col-6">
                                              <span class="wlcm__token__agree">
                                                <?php echo Travelpayouts::__('By pressing the <strong>Save changes</strong> button you agree to <br>send the plugin activation data to Travelpayouts.'); ?>
                                              </span>
                                        </div>
                                        <div class="wlcm__col-6">
                                            <button class="wlcm__token__field__btn"><?php echo Travelpayouts::__('Save changes'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="wlcm__tabs__content" id="tp_acc_unexist">
                            <div class="wlcm__action-box">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g opacity=".3" fill="#76777F">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M20 0C12.82 0 7 5.82 7 13s5.82 13 13 13 13-5.82 13-13S27.18 0 20 0zm-9 13a9 9 0 1118 0 9 9 0 01-18 0z"/>
                                        <path d="M26.283 31.285A16.007 16.007 0 004 46.001 2 2 0 010 46a20.008 20.008 0 0127.85-18.394 2 2 0 11-1.567 3.68zM46 36a2 2 0 012 2v6h6a2 2 0 110 4h-6v6a2 2 0 11-4 0v-6h-6a2 2 0 110-4h6v-6a2 2 0 012-2z"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M28 46c0-9.941 8.059-18 18-18s18 8.059 18 18-8.059 18-18 18-18-8.059-18-18zm18-14c-7.732 0-14 6.268-14 14s6.268 14 14 14 14-6.268 14-14-6.268-14-14-14z"/>
                                    </g>
                                </svg>
                                <div class="wlcm__action-box__text">
                                    <strong class="wlcm__action-box__text__title"><?php echo Travelpayouts::__('1. Create an account'); ?></strong>
                                    <span class="wlcm__action-box__text__desc"><?php echo Travelpayouts::__('Before you start, sign up and get your API token with affiliate ID'); ?></span>
                                </div>
                                <a href="<?php echo $createAccountUrl; ?>" target="_blank"
                                   class="wlcm__action-box__btn"><?php echo Travelpayouts::__('Create an account'); ?></a>
                            </div>
                            <div class="wlcm__action-box">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0)" fill-rule="evenodd" clip-rule="evenodd"
                                       fill="#76777F" opacity=".3">
                                        <path d="M14.002 12h6.339a6.002 6.002 0 0111.317 0h18.344a2 2 0 010 4H31.658a6.002 6.002 0 01-11.317 0h-6.339a2 2 0 110-4zm11.997 0a2 2 0 00-1.998 1.927 1.95 1.95 0 010 .146A2 2 0 1025.999 12zM14.002 28h18.339a6.002 6.002 0 0111.317 0h6.344a2 2 0 010 4h-6.344a6.002 6.002 0 01-11.317 0H14.002a2 2 0 110-4zm23.997 0a2 2 0 00-1.998 1.927v.039l.001.034-.001.073A2 2 0 1037.999 28z"/>
                                        <path d="M.002 6a6 6 0 016-6h52a6 6 0 016 6v40a6 6 0 01-6 6h-19.79a21.824 21.824 0 002.885 8h4.905a2 2 0 010 4h-28a2 2 0 110-4h4.9a21.824 21.824 0 002.886-8H6.002a6 6 0 01-6-6V6zm4 0a2 2 0 012-2h52a2 2 0 012 2v34h-56V6zm23.981 42H6.002a2 2 0 01-2-2v-2h56v2a2 2 0 01-2 2H27.983zm1.843 4a25.823 25.823 0 01-2.379 8h9.105a25.823 25.823 0 01-2.38-8h-4.346z"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0">
                                            <path fill="#fff" d="M0 0h64v64H0z"/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <div class="wlcm__action-box__text">
                                    <strong class="wlcm__action-box__text__title">
                                        <?=Travelpayouts::__('2. Setup the plugin')?>
                                    </strong>
                                    <span class="wlcm__action-box__text__desc">
                                            <?php echo Travelpayouts::__('Enter your information and start earning on tourist services'); ?>
                                        </span>
                                </div>
                                <a href="#tp_acc_exist"
                                   class="wlcm__toggle_acc_exist wlcm__action-box__btn wlcm__action-box__btn_green">
                                    <?php echo Travelpayouts::__('Start setup'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="wlcm__info">
                <div class="wlcm__info__how">
                    <div class="wlcm__container">
                        <h3 class="wlcm__info__title"><?php echo Travelpayouts::__('How it works?'); ?></h3>
                        <div class="wlcm__info__how__box">
                            <div class="wlcm__info__how__item">
                                <div class="wlcm__info__how__icon">
                                    <img src="<?php echo LandingPage::getLandingImage('hov_icon_1.svg'); ?>" alt="">
                                </div>
                                <span class="wlcm__info__how__desc"><?php echo Travelpayouts::__('User visits of your site'); ?></span>
                            </div>
                            <div class="wlcm__info__how__item">
                                <div class="wlcm__info__how__icon">
                                    <img src="<?php echo LandingPage::getLandingImage('hov_icon_2.svg'); ?>" alt="">
                                </div>
                                <span class="wlcm__info__how__desc"><?php echo Travelpayouts::__('Click on your affiliate link'); ?></span>
                            </div>
                            <div class="wlcm__info__how__item">
                                <div class="wlcm__info__how__icon">
                                    <img src="<?php echo LandingPage::getLandingImage('hov_icon_3.svg'); ?>" alt="">
                                </div>
                                <span class="wlcm__info__how__desc"><?php echo Travelpayouts::__('Performs an action, for example, buys a ticket'); ?></span>
                            </div>
                            <div class="wlcm__info__how__item">
                                <div class="wlcm__info__how__icon">
                                    <img src="<?php echo LandingPage::getLandingImage('hov_icon_4.svg'); ?>" alt="">
                                </div>
                                <span class="wlcm__info__how__desc"><?php echo Travelpayouts::__('You get money for action'); ?></span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="wlcm__info__tools">
                    <div class="wlcm__container">
                        <h3 class="wlcm__info__title"><?php echo Travelpayouts::__('Amazing tools'); ?></h3>
                        <div class="wlcm__line">
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title">
                                        <?php echo Travelpayouts::__('12 customizable tables with flights'); ?>
                                    </strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('640new.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('640new.png'); ?> 1x, <?php echo LandingPage::getLandingImage('640new@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title"><?php echo Travelpayouts::__('Search forms'); ?></strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('image_9.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('image_9.png'); ?> 1x, <?php echo LandingPage::getLandingImage('image_9@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title"><?php echo Travelpayouts::__('Low cost flights and hotels map'); ?></strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('image_10.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('image_10.png'); ?> 1x, <?php echo LandingPage::getLandingImage('image_10@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title"><?php echo Travelpayouts::__('Low prices calendar'); ?></strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('image_12.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('image_12.png'); ?> 1x, <?php echo LandingPage::getLandingImage('image_12@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title"><?php echo Travelpayouts::__('Hotel widget'); ?></strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('image_13.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('image_13.png'); ?> 1x, <?php echo LandingPage::getLandingImage('image_13@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="wlcm__col-4">
                                <div class="wlcm__info__tools__item">
                                    <strong class="wlcm__info__tools__title"><?php echo Travelpayouts::__('Popular flight destinations widget'); ?></strong>
                                    <div class="wlcm__info__tools__image">
                                        <img src="<?php echo LandingPage::getLandingImage('image_14.png'); ?>"
                                             srcset="<?php echo LandingPage::getLandingImage('image_14.png'); ?> 1x, <?php echo LandingPage::getLandingImage('image_14@2x.png'); ?> 2x"
                                             alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wlcm__info__tools__controls">
                        <a href="<?php echo $createAccountUrl; ?>" target="_blank"
                           class="wlcm__head__btn">
                            <?php echo Travelpayouts::__('Create an account'); ?>
                            <small><?php echo Travelpayouts::__('go to travelpayouts.com'); ?></small>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
