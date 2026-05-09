<?php
/**
 * The template for the header sticky bar.
 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
 *
 * @author        Redux Framework
 * @package       TravelpayoutsSettingsFramework/Templates
 * @version:      4.0.0
 * @var Redux_Panel $this
 */
$optionName = $this->parent->args['opt_name'];
?>
<div id="redux-sticky" class="tp-admin-header">
    <div id="info_bar">
        <a href="javascript:void(0);"
           class="expand_options<?php echo esc_attr(($this->parent->args['open_expanded'])
               ? ' expanded'
               : ''); ?>"<?php echo(true === $this->parent->args['hide_expand']
            ? ' style="display: none;"'
            : ''); ?>>
            <?php esc_attr_e('Expand', 'redux-framework'); ?>
        </a>
        <div class="redux-action_bar">
            <span class="spinner tp-mx-3 tp-my-0"></span>
            <?php
            if (false === $this->parent->args['hide_reset']) :?>
                <input type="submit" name="<?= $optionName ?>[defaults-section]" id="redux-defaults-section-top"
                       class="button button-secondary" value="<?= Travelpayouts::esc_attr__('Reset Section') ?>">
                <input type="submit"
                       name="<?= $optionName ?>[defaults]"
                       id="redux-defaults-top" class="button button-secondary"
                       value="<?= Travelpayouts::esc_attr__('Reset All') ?>">

            <?php endif; ?>
            <?php
            if (false === $this->parent->args['hide_save']) {
                submit_button(Travelpayouts::esc_attr__('Save changes'), 'tp-button tp-button--primary tp-button--lg', 'redux_save', false, [
                    'id' => 'redux_top_save',
                ]);
            }
            ?>
        </div>
        <div class="redux-ajax-loading" alt="<?php Travelpayouts::esc_attr_e('Working...'); ?>">
            &nbsp;
        </div>
        <div class="clear"></div>
    </div>

    <!-- Notification bar -->
    <div id="Redux_Travelpayouts_notification_bar">
        <?php $this->notification_bar(); ?>
    </div>
</div>
