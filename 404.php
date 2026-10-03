<?php
/**
 * Template Name: Página 404
 * 
 * @package IslaBeya
 */

get_header();
?>
    <div 
    style="
        height: 85vh;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    ">  
        <div>
            <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Imagen Cooming Soon.png' ) ); ?>" 
            style="
                width: 400px;
            "
            alt="Error 404" aria-hidden>
        </div>
        <h1 
        style="
            font-size: var(--tamano--principal);
        ">
            No se encuentra la página solicitada
        </h1>
        <a href="#" class="botones-menu btn-sesion font-medium">
            <span class="icon-home-2 icon-white"></span>
            <span class="boton-text">Regresar al Inicio</span>
        </a>
    </div>
<?php get_footer();?>