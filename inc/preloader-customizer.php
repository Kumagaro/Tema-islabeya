<?php
/**
 * Customizer options for theme preloader.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Customizer: requires core Customizer classes.
add_action( 'customize_register', function( $wp_customize ) {

    $wp_customize->add_section( 'islabeya_preloader_section', [
        'title'       => __( 'Preloader', 'islabeya' ),
        'priority'    => 30,
        'description' => __( 'Configura el preloader que aparece al cargar el sitio.', 'islabeya' ),
    ] );

    $wp_customize->add_setting( 'islabeya_preloader_enable', [
        'default'           => true,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'wp_validate_boolean',
    ] );

    $wp_customize->add_control( 'islabeya_preloader_enable', [
        'label'   => __( 'Activar preloader', 'islabeya' ),
        'section' => 'islabeya_preloader_section',
        'type'    => 'checkbox',
    ] );

    $wp_customize->add_setting( 'islabeya_preloader_bg', [
        'default'           => '#ffffff',
        'type'              => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
    ] );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'islabeya_preloader_bg', [
        'label'   => __( 'Color de fondo', 'islabeya' ),
        'section' => 'islabeya_preloader_section',
    ] ) );

    $wp_customize->add_setting( 'islabeya_preloader_image_id', [
        'default'           => 0,
        'type'              => 'theme_mod',
        'sanitize_callback' => 'absint',
    ] );

    // Media control class provided by WP core? If not, we provide a fallback below.
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'islabeya_preloader_image_id', [
        'label'     => __( 'Imagen/Icono del preloader', 'islabeya' ),
        'section'   => 'islabeya_preloader_section',
        'mime_type' => 'image',
    ] ) );

} );

// Fallback Media control for the Customizer (lets admins pick an image from Media Library).
if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'WP_Customize_Media_Control' ) ) {
    class WP_Customize_Media_Control extends WP_Customize_Control {
        public $type = 'image';

        public function enqueue() {
            wp_enqueue_media();
        }

        public function render_content() {
            $value   = (int) $this->value();
            $image   = $value ? wp_get_attachment_image_src( $value, 'full' ) : false;
            $img_url = $image ? $image[0] : '';
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <div class="islabeya-media-control" style="margin-top:10px;">
                    <input class="customize-control-input" type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>" />

                    <div class="islabeya-media-preview" style="margin: 8px 0;">
                        <?php if ( $img_url ) : ?>
                            <img src="<?php echo esc_url( $img_url ); ?>" style="max-width: 160px; height: auto;" />
                        <?php endif; ?>
                    </div>

                    <button type="button" class="button islabeya-media-select">Seleccionar</button>
                    <button type="button" class="button islabeya-media-clear" <?php echo $value ? '' : 'disabled'; ?>>Quitar</button>
                </div>
            </label>

            <script>
                (function($){
                    wp.media = wp.media || {};
                    $(document).on('click', '.islabeya-media-select', function(e){
                        e.preventDefault();
                        var button = $(this);
                        var container = button.closest('.islabeya-media-control');
                        var input = container.find('input.customize-control-input');
                        var preview = container.find('.islabeya-media-preview');
                        var frame = wp.media({
                            title: 'Seleccionar imagen',
                            multiple: false,
                            library: { type: 'image' }
                        });
                        frame.on('select', function(){
                            var attachment = frame.state().get('selection').first().toJSON();
                            input.val(attachment.id).trigger('change');
                            preview.html('<img src="'+attachment.url+'" style="max-width:160px;height:auto;" />');
                            container.find('.islabeya-media-clear').prop('disabled', false);
                        });
                        frame.open();
                    });

                    $(document).on('click', '.islabeya-media-clear', function(e){
                        e.preventDefault();
                        var button = $(this);
                        var container = button.closest('.islabeya-media-control');
                        var input = container.find('input.customize-control-input');
                        var preview = container.find('.islabeya-media-preview');
                        input.val('0').trigger('change');
                        preview.html('');
                        button.prop('disabled', true);
                    });
                })(jQuery);
            </script>
            <?php
        }
    }
}


