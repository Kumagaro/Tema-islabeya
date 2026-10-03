<?php
/**
 * My Account page - Solo Login
 *
 * @package IslaBeya
 */

defined( 'ABSPATH' ) || exit;

get_header('fullheight');
?>


<!-- Plantilla para pagina de mi cuenta con el usuario ya logeado -->
<?php if ( is_user_logged_in() ) : ?>
    <?php wc_get_template( 'myaccount/content-mi-cuenta.php' ); ?>
<?php else : ?>
    <?php wc_get_template( 'myaccount/page-sesion.php' ); ?>
<?php endif; ?>

<?php get_footer('fullheight'); ?>