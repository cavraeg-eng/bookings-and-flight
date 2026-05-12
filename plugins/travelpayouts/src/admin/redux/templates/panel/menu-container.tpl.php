<?php
/**
 * The template for the menu container of the panel.
 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
 * @author     Redux Framework
 * @package    TravelpayoutsSettingsFramework/Templates
 * @version:    4.0.0
 */

use Travelpayouts\admin\components\HeadWayWidget;
use Travelpayouts\helpers\FileHelper;

?>
<div class="redux-sidebar tp-admin-sidebar-wrapper">
    <div class="redux-sidebar-wrapper tp-admin-sidebar">
        <div class="travelpayouts-admin-logo tp-admin-logo">
            <div class="travelpayouts-admin-logo--full  tp-admin-logo--full">
                <a href="javascript:void(0);"
                   class="redux-group-tab-link-a tp-admin-sidebar-menu-item-link"
                   data-key="2"
                   data-rel="2">
                    <?= FileHelper::requireAssetByAlias('@images/admin/panel/logo-wide.svg') ?>
                </a>
            </div>
            <div class="travelpayouts-admin-logo--short">
                <a href="javascript:void(0);"
                   class="redux-group-tab-link-a tp-admin-sidebar-menu-item-link"
                   data-key="2"
                   data-rel="2">
                    <?= FileHelper::requireAssetByAlias('@images/admin/panel/logo-short.svg') ?>
                </a>
            </div>
        </div>
        <div class="redux-group-menu-wrapper tp-admin-sidebar-menu-wrapper">
            <ul class="redux-group-menu tp-admin-sidebar-menu">
                <?php
                foreach ($this->parent->sections as $k => $section) {
                    $the_title = isset($section['title'])
                        ? $section['title']
                        : '';
                    $skip_sec = false;
                    foreach ($this->parent->options_class->hidden_perm_sections as $num => $section_title) {
                        if ($section_title === $the_title) {
                            $skip_sec = true;
                        }
                    }

                    if (isset($section['customizer_only']) && true === $section['customizer_only']) {
                        continue;
                    }

                    if (false === $skip_sec) {
                        echo($this->parent->section_menu($k, $section)); // phpcs:ignore WordPress.Security.EscapeOutput
                        $skip_sec = false;
                    }
                }

                /**
                 * Action 'redux_travelpayouts/page/{opt_name}/menu/after'
                 * @param object $this TravelpayoutsSettingsFramework
                 */
                do_action("redux_travelpayouts/page/{$this->parent->args['opt_name']}/menu/after", $this); // phpcs:ignore WordPress.NamingConventions.ValidHookName
                ?>
            </ul>
            <div class="redux-sidebar-divider redux-sidebar--no-mobile"></div>
            <ul class="redux-group-menu redux-sidebar--no-mobile">
                <?=HeadWayWidget::render() ?>
            </ul>
            <?php if (!empty($this->parent->args['display_version'])) : ?>
                <div class="redux-sidebar-divider redux-sidebar--no-mobile"></div>
                <div class="redux-sidebar-version redux-sidebar--no-mobile">
                    v. <?php echo wp_kses_post($this->parent->args['display_version']); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
