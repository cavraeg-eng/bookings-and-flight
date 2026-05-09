let elementor_components = {};

window.getElComponents = () => {
    return elementor_components;
};

jQuery(window).on('elementor/frontend/init', () => {
    elementor.hooks.addAction(
        'panel/open_editor/widget/travelpayouts_shortcode_widget',
        function (panel, model, view) {},
    );
});
