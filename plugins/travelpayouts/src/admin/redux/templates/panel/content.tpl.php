<?php
/**
 * The template for the main content of the panel.
 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
 * @author      Redux Framework
 * @package     TravelpayoutsSettingsFramework/Templates
 * @version:    4.0.0
 * @var Redux_Travelpayouts_Panel $this
 */

use Travelpayouts\admin\redux\extensions\OscAccordionField;

?>
<!-- Header Block -->
<?php $this->get_template('header.tpl.php'); ?>
<!-- Intro Text -->
<?php if (isset($this->parent->args['intro_text'])) { ?>
    <div id="redux-intro-text"><?php echo wp_kses_post($this->parent->args['intro_text']); ?></div>
<?php } ?>

<?php $this->get_template('menu-container.tpl.php'); ?>

<div class="redux-main">
    <!-- Stickybar -->
    <?php $this->get_template('header-stickybar.tpl.php'); ?>
    <div id="Redux_Travelpayouts_ajax_overlay">&nbsp;</div>
    <div class="redux-sections"><?php foreach ($this->parent->sections as $k => $section) { ?>
            <?php if (isset($section['customizer_only']) && true === $section['customizer_only']) { ?>
                <?php continue; ?>
            <?php } // phpcs:ignore Squiz.PHP.NonExecutableCode.Unreachable ?>

            <?php $section['class'] = isset($section['class'])
                ? ' ' . $section['class']
                : ''; ?>

            <?php $disabled = ''; ?>
            <?php if (isset($section['disabled']) && $section['disabled']) { ?>
                <?php $disabled = 'disabled '; ?>
            <?php } ?>

            <div
                    id="<?php echo esc_attr($k); ?>_section_group"
                    class="redux-group-tab <?php echo esc_attr($disabled); ?><?php echo esc_attr($section['class']); ?>"
                    data-rel="<?php echo esc_attr($k); ?>">

                <?php $display = true; ?>

                <?php if (isset($_GET['page']) && $this->parent->args['page_slug'] === $_GET['page']) { // phpcs:ignore WordPress.Security.NonceVerification ?>
                    <?php if (isset($section['panel']) && false === $section['panel']) { ?>
                        <?php $display = false; ?>
                    <?php } ?>
                <?php } ?>

                <?php
                if ($display) {
                    /**
                     * Action 'redux_travelpayouts/page/{opt_name}/section/before'
                     *
                     * @param object $this TravelpayoutsSettingsFramework
                     */

                    // phpcs:ignore WordPress.NamingConventions.ValidHookName
                    do_action("redux_travelpayouts/page/{$this->parent->args['opt_name']}/section/before", $section);

                    $isAccordion = false;
                    foreach ($section['fields'] as $sectionField) {
                        if ($sectionField['type'] === OscAccordionField::TYPE) {
                            $isAccordion = true;
                            break;
                        }
                    }

                    $this->output_section($k);

                    /**
                     * Action 'redux_travelpayouts/page/{opt_name}/section/after'
                     *
                     * @param object $this TravelpayoutsSettingsFramework
                     */

                    // phpcs:ignore WordPress.NamingConventions.ValidHookName
                    do_action("redux_travelpayouts/page/{$this->parent->args['opt_name']}/section/after", $section);
                }
                ?>
            </div> <!-- section group -->
        <?php } ?>

        <?php
        /**
         * Action 'redux_travelpayouts/page/{opt_name}/sections/after'
         *
         * @param object $this TravelpayoutsSettingsFramework
         */

        // phpcs:ignore WordPress.NamingConventions.ValidHookName
        do_action("redux_travelpayouts/page/{$this->parent->args['opt_name']}/sections/after", $this);
        ?></div>
    <div class="clear"></div>
    <!-- Footer Block -->
    <?php $this->get_template('footer.tpl.php'); ?>
    <div id="redux-sticky-padder" style="display: none;">&nbsp;</div>
</div> <!-- redux main -->
<div class="clear"></div>
