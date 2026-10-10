<?php
/**
 * Template Name: Página de Inicio
 * 
 * @package IslaBeya
 */

get_header();
?>

<main id="primary" class="site-main">
    <!-- Hero Section version Desktop -->
    <section class="hero">
        <div class="carrusel-imagenes" id="slider">
            <a href="<?php echo esc_url( get_term_link( 'kits-solares', 'product_cat' ) ); ?>" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 1.webp' ) ); ?>" alt="Energía solar para ganar independencia, hasta 25% de descuento">
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'cemento-chagres-425-kg', OBJECT, 'product' ) ) ); ?>" class="slider oculto">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 2.webp' ) ); ?>" alt="Materiales de construcción de calidad para tus proyectos">
            </a>
            <a href="<?php echo esc_url( get_term_link( 'electrodomesticos', 'product_cat' ) ); ?>" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 3.webp' ) ); ?>" alt="Electrodomésticos que resuelven tu día">
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'harina-de-trigo-bonoli-25kg', OBJECT, 'product' ) ) ); ?>" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 4.webp' ) ); ?>" alt="Harina Bonoli de alta calidad para su dieta diaria">
            </a>
            <a href="<?php echo esc_url( get_term_link( 'torvan', 'product_brand' ) ); ?>" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 5.webp' ) ); ?>" alt="Motos Torvan, marca exclusiva en Islabeya">
            </a>
            <a href="https://wa.link/q8fpmb" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 6.webp' ) ); ?>" alt="Envio de paqueteria mediante SD Express">
            </a>
            <a href="<?php echo esc_url( get_term_link( 'despensa', 'product_cat' ) ); ?>" class="slider">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/carrusel-hero/Banner 7.webp' ) ); ?>" alt="Productos para comprar en Villa Clara. Selecciona tu despensa con seguridad">
            </a>
        </div>
        <div class="dots-container"></div>
        <span class="btn-left">
            <span class="icon-arrow-left"></span>
        </span>
        <span class="btn-right">
            <span class="icon-arrow-right"></span>
        </span>
    </section>
    <!-- Hero Section version Movil -->
    <section class="hero-movil">
        <a href="<?php echo esc_url( get_term_link( 'electrodomesticos', 'product_cat' ) ); ?>" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/Banner Movil 1.webp', true); ?> alt="Electrodomésticos que resuelven tu día a día">
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'cemento-chagres-425-kg', OBJECT, 'product' ) ) ); ?>" class="slider-movil oculto">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 2.webp'); ?> alt="Materiales de contruccion de calidad">
        </a>
        <a href="<?php echo esc_url( get_term_link( 'kits-solares', 'product_cat' ) ); ?>" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 3.webp'); ?> alt="Energia solara para ganar independencia. Kit solares con hasta 25% de descuento">
        </a>
        <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'harina-de-trigo-bonoli-25kg', OBJECT, 'product' ) ) ); ?>" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 4 (1).webp'); ?> alt="Harina Bonoli, de alta calidad para su dieta diaria">
        </a>
        <a href="<?php echo esc_url( get_term_link( 'torvan', 'product_brand' ) ); ?>" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 5.webp'); ?> alt="Motos Torvan, marca exlcusiva en Islabeya">
        </a>
        <a href="https://wa.link/q8fpmb" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 6.webp'); ?> alt="Envios seguros con SD Express">
        </a>
        <a href="<?php echo esc_url( get_term_link( 'despensa', 'product_cat' ) ); ?>" class="slider-movil">
            <img <?php echo islabeya_banner_movil_srcset('carrusel-hero/Banner movil/BaNNer movil 7.webp'); ?> alt="Compra tus productos en Villa Clara, selecciona tu despensa con seguridad">
        </a>
    </section>
    <!-- Categorías Populares version movil -->
    <section class="categorias-populares">
        <?php
        $categorias = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => 0,        // Solo categorías padres
            'number'     => 20,
        ) );
        
        if ( ! empty( $categorias ) ) :
            foreach ( $categorias as $categoria ) :
                $thumbnail_id = get_term_meta( $categoria->term_id, 'thumbnail_id', true );
                $imagen = wp_get_attachment_url( $thumbnail_id );
                ?>
                <a href="<?php echo get_term_link( $categoria ); ?>" class="categoria-item">
                    <?php if ( $imagen ) : ?>
                        <img width="70" height="70" src="<?php echo esc_url( $imagen ); ?>" <?php echo islabeya_categoria_icon_srcset( $thumbnail_id, 70 ); ?> alt="<?php echo esc_attr( $categoria->name ); ?>">
                    <?php endif; ?>
                    <h3 class="categoria-text"><?php echo esc_html( $categoria->name ); ?></h3>
                </a>
            <?php endforeach;
            echo '</div>';
        endif;
        ?>
    </section>
    <!-- Features -->
    <section class="features"> 
        <div class="features-content">
            <div class="feature">
                <span class="icon-feature-history"></span>
                <h3>Envíos Rápidos</h3>
                <p>Recibe tus productos en tiempo récord con nuestro servicio de entrega eficiente.</p>
            </div>
            <div class="feature">
                <span class="icon-feature-shield-check"></span>
                <h3>Servicio al cliente</h3>
                <p>Nuestro equipo está siempre listo para ayudarte en cualquier problema.</p>
            </div>
            <div class="feature">
                <span class="icon-feature-medal-ribbons-star"></span>
                <h3>Pagos Seguros</h3>
                <p>Compra con confianza gracias a nuestras transacciones seguras.</p>
            </div>
            <div class="feature">
                <span class="icon-feature-box"></span>
                <h3>Variedad de Productos</h3>
                <p>Explora variedades de productos para satisfacer todas tus necesidades.</p>
            </div>
            <div class="feature">
                <span class="icon-feature-call-chat-rounded"></span>
                <h3>Vende tus productos</h3>
                <p>Publica y vende tus propios productos en nuestra plataforma en línea.</p>
            </div>
        </div>
    </section>
    <!-- Features Movil-->
    <section class="features-movil"> 
        <div class="features-content">
            <div class="feature" style="padding-left: 5%;">
                <div class="feature-contain">
                    <span class="icon-feature-history"></span>
                    <h3>Envíos Rápidos</h3>
                    <p>Recibe tus productos en tiempo récord con nuestro servicio de entrega eficiente.</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-contain">
                    <span class="icon-feature-shield-check"></span>
                    <h3>Servicio al cliente</h3>
                    <p>Nuestro equipo está siempre listo para ayudarte en cualquier problema.</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-contain">
                    <span class="icon-feature-medal-ribbons-star"></span>
                    <h3>Pagos Seguros</h3>
                    <p>Compra con confianza gracias a nuestras transacciones seguras.</p>
                </div>
            </div>
            <div class="feature">
                <div class="feature-contain">
                    <span class="icon-feature-box"></span>
                    <h3>Variedad de Productos</h3>
                    <p>Explora variedades de productos para satisfacer todas tus necesidades.</p>
                </div>
            </div>
            <div class="feature" style="padding-right: 5%;">
                <div class="feature-contain">
                    <span class="icon-feature-call-chat-rounded"></span>
                    <h3>Vende tus productos</h3>
                    <p>Publica y vende tus propios productos en nuestra plataforma en línea.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Minibanners 1 -->
    <section class="minibanners">
        <div class="contenedor-minibaner">
            <a href="<?php echo esc_url( get_term_link( 'motos', 'product_cat' ) ); ?>" class="minibanner">
                <div class="banner-texto">
                    <h2>COMPRA TU MOTO</h2>
                    <p>Motos exclusivas solo para ti</p>
                    <p class="link">Ver ofertas</p>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/minibaner1.webp' ) ); ?>" alt="Moto negra deportiva. Venta de motos exclusivas">
            </a>
            <a href="<?php echo esc_url( get_term_link( 'contenedor', 'product_cat' ) ); ?>" class="minibanner">
                <div class="banner-texto">
                    <h2>IMPORTA LO QUE DESEES</h2>
                    <p>Importa lo que necesites para tu negocio</p>
                    <p class="link">Ver ofertas</p>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/minibaner2.webp' ) ); ?>" alt="Contenedores Blancos, Importacion de contenedores a Cuba ">
            </a>
        </div>
    </section>
    <!-- Video Promocional -->
    <section class="section-video">
        <div class="contenedor-video">
            <div class="contenedor-video-1">
                <div class="logos-h2">
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL 2.svg' ) ); ?>" 
                    alt="Logo IslaBeya, Identificador Visual Islabeya" />
                    +
                    <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Logotipo - Positivo.svg' ) ); ?>" 
                    alt="Logo SD Express, Identificador Visual SD Express" />
                </div>
                <p>
                    Ofrecemos
                    productos y servicios de calidad que satisfagan las
                    necesidades y expectativas de nuestros clientes.
                </p>
                <a href="<?php echo esc_url( home_url( '/tienda/sdexpress/' ) ); ?>" class="btn font-medium">
                    <span class="icon-shop"></span>
                    <span class="boton-text">Tienda SD Express</span>
                    <span class="icon-arrow-right"></span>
                </a>
            </div>
            <div class="hero-video-container" id="video-container">
                <!-- SPINNER DE CARGA -->
                <div class="video-loader" id="video-loader">
                    <div class="loader-spinner"></div>
                    <span>Cargando video...</span>
                </div>
                
                <video 
                    id="video-promocional"
                    autoplay 
                    muted 
                    loop 
                    playsinline 
                    preload="auto"
                    <?php echo islabeya_video_poster_srcset('Poster-video.webp'); ?>
                >   
                    <source src="<?php echo get_template_directory_uri(); ?>/assets/video/PROMOCIONAL-WEB-480.webm" type="video/webm">
                    <source src="<?php echo get_template_directory_uri(); ?>/assets/video/PROMOCIONAL-WEB-480.mp4" type="video/mp4">
                    <p>Tu navegador no soporta videos HTML5.</p>
                </video>
                
                <!-- Botón de control de sonido -->
                <button id="video-sound-toggle" class="video-sound-toggle" aria-label="Activar sonido">
                    <svg class="icon-muted" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M20.5145 6.3164C20.892 6.14605 21.3362 6.31403 21.5065 6.6916C21.9736 7.72674 22.5 9.45958 22.5 12C22.5 14.1916 22.1082 15.7829 21.7 16.8442C21.4962 17.374 21.2894 17.7692 21.1275 18.039C21.0466 18.1738 20.977 18.2772 20.9248 18.3504C20.8986 18.3869 20.8769 18.4159 20.8602 18.4375C20.8518 18.4483 20.8448 18.4572 20.8391 18.4643L20.8316 18.4736L20.8286 18.4772L20.8273 18.4788C20.8273 18.4788 20.8262 18.4801 20.25 18L20.8262 18.4801C20.561 18.7983 20.0881 18.8413 19.7699 18.5762C19.4532 18.3123 19.4091 17.8426 19.67 17.5245C19.67 17.5245 19.6718 17.5222 19.6735 17.52C19.6788 17.5132 19.6893 17.4994 19.7042 17.4785C19.7339 17.4368 19.7815 17.3668 19.8413 17.2673C19.9606 17.0683 20.1288 16.751 20.3 16.3058C20.6418 15.4171 21 14.0084 21 12C21 9.67366 20.5194 8.15099 20.1393 7.30849C19.9689 6.93093 20.1369 6.48676 20.5145 6.3164Z
                        M18.414 9.26566C18.8196 9.18146 19.2166 9.44198 19.3008 9.84754C19.4109 10.378 19.5 11.0889 19.5 12C19.5 13.1101 19.3678 13.9228 19.2265 14.4738C19.1559 14.749 19.0833 14.9579 19.0245 15.1051C18.9951 15.1787 18.9691 15.2367 18.9486 15.2797C18.9384 15.3012 18.9295 15.3189 18.9223 15.333L18.9126 15.3514L18.9088 15.3585L18.9071 15.3615L18.9063 15.3629C18.9063 15.3629 18.9056 15.3642 18.25 15L18.9056 15.3642C18.7045 15.7263 18.2479 15.8568 17.8858 15.6556C17.5268 15.4562 17.3955 15.0056 17.5893 14.645L17.5943 14.6348C17.6012 14.6204 17.6143 14.5917 17.6318 14.548C17.6667 14.4608 17.7191 14.3135 17.7735 14.1012C17.8822 13.6772 18 12.9899 18 12C18 11.1873 17.9206 10.5787 17.8321 10.1525C17.7479 9.74689 18.0084 9.34986 18.414 9.26566Z
                        M21.7803 3.53033C22.0732 3.23744 22.0732 2.76256 21.7803 2.46967C21.4874 2.17678 21.0126 2.17678 20.7197 2.46967L16.2705 6.91886C16.2246 6.39532 16.1646 5.93197 16.077 5.52977C15.9052 4.74135 15.6003 4.05581 14.9609 3.60646C14.7259 3.44128 14.4642 3.30809 14.1923 3.21531C13.3741 2.9361 12.5608 3.15928 11.7348 3.56055C10.9212 3.95576 9.93412 4.60663 8.70324 5.41822L8.43647 5.59411C7.98856 5.88944 7.83448 5.98815 7.67513 6.05848C7.50452 6.13378 7.3252 6.18757 7.14132 6.21862C6.96956 6.24762 6.7866 6.25003 6.25008 6.25003L6.08906 6.24998C4.87215 6.24933 4.02659 6.24889 3.27496 6.59664C2.58016 6.9181 1.91141 7.54732 1.54828 8.22128C1.15566 8.94996 1.10959 9.712 1.04409 10.7955L1.03618 10.926C1.01373 11.2943 1 11.6585 1 12C1 12.3416 1.01373 12.7058 1.03618 13.0741L1.04409 13.2045C1.10959 14.2881 1.15566 15.0501 1.54828 15.7788C1.91141 16.4527 2.58016 17.082 3.27496 17.4034C3.88551 17.6859 4.55803 17.7386 5.44121 17.7481L2.71967 20.4697C2.42678 20.7626 2.42678 21.2374 2.71967 21.5303C3.01256 21.8232 3.48744 21.8232 3.78033 21.5303L21.7803 3.53033Z
                        M16.5 12C16.5 11.5858 16.1642 11.25 15.75 11.25C15.5554 11.25 15.3781 11.3241 15.2448 11.4457L15.1735 11.5203L9.17494 17.7941C8.82947 18.1554 8.90952 18.7441 9.33893 19.0001C10.3777 19.6808 11.2375 20.2247 11.9704 20.549C12.7127 20.8773 13.4503 21.0379 14.1923 20.7847C14.4642 20.6919 14.7259 20.5588 14.9609 20.3936C15.667 19.8974 15.9659 19.1134 16.1278 18.2139C16.287 17.3296 16.3414 16.1576 16.4092 14.6977L16.4119 14.6402C16.4637 13.5252 16.5 12.552 16.5 12Z" />
                    </svg>
                    <svg class="icon-unmuted" style="display:none" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M2.00299 11.7155C2.04033 9.87326 2.059 8.95215 2.67093 8.16363C2.78262 8.0197 2.9465 7.8487 3.08385 7.73274C3.83639 7.09741 4.82995 7.09741 6.81706 7.09741C7.527 7.09741 7.88197 7.09741 8.22035 7.00452C8.29067 6.98522 8.36024 6.96296 8.4289 6.93781C8.75936 6.81674 9.05574 6.60837 9.64851 6.19161C11.9872 4.54738 13.1565 3.72527 14.138 4.08241C14.3261 4.15088 14.5083 4.24972 14.671 4.37162C15.5194 5.00744 15.5839 6.48675 15.7128 9.44537C15.7606 10.5409 15.7931 11.4785 15.7931 12C15.7931 12.5215 15.7606 13.4591 15.7128 14.5546C15.5839 17.5132 15.5194 18.9926 14.671 19.6284C14.5083 19.7503 14.3261 19.8491 14.138 19.9176C13.1565 20.2747 11.9872 19.4526 9.64851 17.8084C9.05574 17.3916 8.75936 17.1833 8.4289 17.0622C8.36024 17.037 8.29067 17.0148 8.22035 16.9955C7.88197 16.9026 7.527 16.9026 6.81706 16.9026C4.82995 16.9026 3.83639 16.9026 3.08385 16.2673C2.9465 16.1513 2.78262 15.9803 2.67093 15.8364C2.059 15.0478 2.04033 14.1267 2.00299 12.2845C2.00103 12.1878 2 12.0928 2 12C2 11.9072 2.00103 11.8122 2.00299 11.7155Z
                        M19.4895 5.55219C19.7821 5.29218 20.217 5.33434 20.4608 5.64635L19.931 6.11713C20.4608 5.64635 20.4606 5.64602 20.4608 5.64635L20.4619 5.6477L20.4631 5.64921L20.4658 5.65275L20.4727 5.66184C20.4779 5.6688 20.4844 5.67756 20.4921 5.68814C20.5075 5.70929 20.5275 5.73772 20.5515 5.77358C20.5995 5.84529 20.6635 5.94667 20.7379 6.07889C20.8868 6.34345 21.077 6.73092 21.2644 7.25038C21.6397 8.29107 22 9.85136 22 12.0002C22 14.1491 21.6397 15.7094 21.2644 16.7501C21.077 17.2695 20.8868 17.657 20.7379 17.9216C20.6635 18.0538 20.5995 18.1552 20.5515 18.2269C20.5275 18.2627 20.5075 18.2912 20.4921 18.3123C20.4844 18.3229 20.4779 18.3317 20.4727 18.3386L20.4658 18.3477L20.4631 18.3513L20.4619 18.3528C20.4616 18.3531 20.4608 18.3541 19.931 17.8833L20.4608 18.3541C20.217 18.6661 19.7821 18.7083 19.4895 18.4483C19.1983 18.1895 19.1578 17.729 19.3977 17.417C19.3983 17.4163 19.3994 17.4148 19.4009 17.4127C19.4058 17.406 19.4154 17.3925 19.4291 17.372C19.4565 17.3311 19.5003 17.2625 19.5552 17.1649C19.6649 16.9698 19.8195 16.6587 19.977 16.2221C20.2913 15.3508 20.6207 13.9695 20.6207 12.0002C20.6207 10.0309 20.2913 8.64968 19.977 7.77836C19.8195 7.34181 19.6649 7.03066 19.5552 6.8356C19.5003 6.73802 19.4565 6.66934 19.4291 6.62845C19.4154 6.608 19.4058 6.59449 19.4009 6.58778C19.3994 6.58561 19.3983 6.58416 19.3977 6.5834C19.3977 6.5834 19.3977 6.58341 19.3977 6.5834
                        M17.7571 8.41595C18.0901 8.21871 18.51 8.34663 18.6949 8.70166L18.0921 9.0588C18.6949 8.70166 18.6948 8.70134 18.6949 8.70166L18.6956 8.70295L18.6963 8.70432L18.6978 8.7073L18.7014 8.71428L18.7102 8.73227C18.7169 8.74607 18.7251 8.76348 18.7345 8.78457C18.7533 8.82676 18.7772 8.88363 18.8042 8.95574C18.8584 9.10004 18.9251 9.3049 18.99 9.57476C19.1199 10.115 19.2415 10.9119 19.2415 12.0003C19.2415 13.0888 19.1199 13.8857 18.99 14.4259C18.9251 14.6958 18.8584 14.9007 18.8042 15.045C18.7772 15.1171 18.7533 15.1739 18.7345 15.2161C18.7251 15.2372 18.7169 15.2546 18.7102 15.2684L18.7014 15.2864L18.6978 15.2934L18.6963 15.2964L18.6956 15.2978C18.6954 15.2981 18.6949 15.299 18.0921 14.9419L18.6949 15.299C18.51 15.6541 18.0901 15.782 17.7571 15.5847C17.427 15.3892 17.3063 14.9474 17.4846 14.5938L17.4892 14.5838C17.4955 14.5697 17.5075 14.5415 17.5236 14.4987C17.5557 14.4132 17.6039 14.2688 17.6539 14.0606C17.7539 13.6448 17.8622 12.9709 17.8622 12.0003C17.8622 11.0298 17.7539 10.3559 17.6539 9.94007C17.6039 9.73193 17.5557 9.58748 17.5236 9.50197C17.5075 9.45918 17.4955 9.43102 17.4892 9.41691L17.4846 9.40687C17.3063 9.05332 17.427 8.61152 17.7571 8.41595Z" />
                    </svg>
                </button>
            </div>
        </div>
    </section>
    <!-- Seccion motos-->
    <?php wc_get_template_part( 'carousel', 'moto' ); ?>
    <!-- Disfruta Islabeya -->
    <section class="feature-disfruta">
        <div class="contenedor-feature-disfruta">
            <div class="disfruta-top">
                <h2 class="principal">Disfruta</h2>
                <h2 class="secundario desktop">todo lo que tiene IslaBeya para ti</h2>
                <h2 class="secundario movil">de IslaBeya</h2>
                <!-- Para utiazar partes de plantillas -->
                <?php get_template_part( 'template parts/header/boton-tienda' ); ?>
                <!-- Fin -->
            </div>
            <div class="disfruta-bottom">
                <div class="solar">
                    <div class="contenido">
                        <span class="sako"></span>
                        <div class="text">
                            <h3>Autonomía para tu hogar</h3>
                            <p>Kit solares potentes para no preocuparte por la energía</p>
                        </div>
                    </div>
                </div>
                <div class="torvan">
                    <div class="contenido">
                        <span class="torvan-icon"></span>
                        <div class="text">
                            <h3>Elegancía y exclusividad</h3>
                            <p>Torvan lo tiene todo: estilo, calidad y durabilidad</p>
                        </div>
                    </div>
                </div>
                <div class="envio">
                    <div class="contenido">
                        <span class="sdexpres"></span>
                        <div class="text">
                            <h3>Entregas ultra rápidas</h3>
                            <p>Tendrás tus productos en tu hogar con la mayor velocidad del mercado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Minibanner -->
        <?php
        // Obtener la URL de la página de listado de tiendas de Dokan
        $store_listing_page_id = dokan_get_option( 'store_listing', 'dokan_pages', 0 );
        $store_listing_url = $store_listing_page_id ? get_permalink( $store_listing_page_id ) : home_url( '/store-listing/' );
        ?>
    <section class="minibanners-3">
        <div class="contenedor-minibaner-3">
            <div class="minibanner-3">
                <div class="banner-texto">
                    <h2>Todas las tiendas a tu alcance</h2>
                    <p>Mira todas las tiendas disponibles en Islabeya, busca la de tu preferencia y COMPRA SUS PRODUCTOS!</p>

                    <a href="<?php echo esc_url( $store_listing_url ); ?>" class="btn btn-second font-medium">
                        <span class="icon-shop icon-white"></span>
                        <span class="boton-text">Todas las tiendas</span>
                    </a>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Banner tiendas.webp' ) ); ?>" alt="Paqueteria con SD Express, Chico con abrigo amarillo con paqueteria de SD Express">
            </div>
        </div>
    </section>
    <!-- Minibanners 2 -->
    <section class="minibanners-2">
        <div class="contenedor-minibaner-2">
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'moto-torvan-raptor-180cc-2', OBJECT, 'product' ) ) ); ?>" class="minibanner-2">
                <div class="banner-texto">
                    <h2>Torvan Raptor Fibra de Carbono</h2>
                    <p>Moto exclusiva solo para ti disponible en nuestra tienda</p>
                    <p class="link">9% de descuento</p>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/minibaner5.webp' ) ); ?>" alt="Torvan Raptor Fibra de Carbono">
            </a>
            <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'moto-electrica-torvan-volt', OBJECT, 'product' ) ) ); ?>" class="minibanner-2">
                <div class="banner-texto">
                    <h2>Torvan Volt</h2>
                    <p>Moto exclusiva solo para ti disponible en nuestra tienda</p>
                    <p class="link">9% de descuento</p>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/minibaner6.webp' ) ); ?>" alt="Torvan Volt color rosa">
            </a>
        </div>
    </section>
    <!-- Productos electrodomesticos -->
    <?php wc_get_template_part( 'carousel', 'equipos' ); ?>
    <!-- Productos despensa -->
    <?php wc_get_template_part( 'carousel', 'despensa' ); ?>
    <!-- Productos combo -->
    <?php wc_get_template_part( 'carousel', 'combo' ); ?>
    <!-- Minibanners 3 -->
    <section class="minibanners-2">
        <div class="contenedor-minibaner-2">
            <a href="https://api.whatsapp.com/send?phone=34602483604&text=Hola%2C%20Estoy%20interesado%20en%20armar%20mi%20propio%20contenedor%20con%20ustedes.%20C%C3%B3mo%20es%20el%20proceder%3F" class="minibanner-2">
                <div class="banner-texto">
                    <h2>Arma tu contenedor</h2>
                    <p>Arma un contenedor a tu gusto con nuestros productos disponibles</p>
                    <span class="link">Armar contenedor</span>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Mini banner.webp' ) ); ?>" alt="Arma un contenedor a tu gusto con nuestros productos disponibles">
            </a>
            <a href="<?php echo esc_url( get_term_link( 'kits-solares', 'product_cat' ) ); ?>" class="minibanner-2">
                <div class="banner-texto">
                    <h2>Autonomía Eléctrica</h2>
                    <p>Montamos kits solares en tu hogar. Disfruta de libertad energética</p>
                    <span class="link">25% de descuento</span>
                </div>
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/minibaner3.webp' ) ); ?>" alt="Kits solares marca Sako">
            </a>
        </div>
    </section>
    <?php wc_get_template_part( 'carousel', 'contenedor' ); ?>
    <?php wc_get_template_part( 'carousel', 'solares' ); ?>
    <!-- Categrias grid -->
    <section class="grid-categorias">
    <div class="categorias-contenedor">
        <h2>categorias</h2>
        <div class="categorias">
            <?php
            $categorias = get_terms( array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => true,
                'parent'     => 0,        // Solo categorías padres
                'number'     => 20,
            ) );

            if ( ! empty( $categorias ) ) :
                foreach ( $categorias as $categoria ) :
                    $thumbnail_id = get_term_meta( $categoria->term_id, 'thumbnail_id', true );
                    $imagen = wp_get_attachment_url( $thumbnail_id );
                    ?>
                    <a href="<?php echo get_term_link( $categoria ); ?>" class="categoria-item">
                        <?php if ( $imagen ) : ?>
                            <img width="130" height="130" src="<?php echo esc_url( $imagen ); ?>" <?php echo islabeya_categoria_icon_srcset( $thumbnail_id, 130 ); ?> alt="<?php echo esc_attr( $categoria->name ); ?>">
                        <?php endif; ?>
                        <h3 class="categoria-text"><?php echo esc_html( $categoria->name ); ?></h3>
                        <?php if ( ! empty( $categoria->description ) ) : ?>
                            <p class="categoria-descripcion"><?php echo esc_html( $categoria->description ); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach;
                echo '</div>';
            endif;
            ?>
        </div>
    </div>
    </section>
    <!-- Colaboradores -->
    <section class="colaboraciones">
        <h2>
            Marcas Colaboradoras
        </h2>
        <div class="marcas">
            <a href="https://wa.link/q8fpmb" style="text-decoration: none;">
                <img width="140" height="75" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/sdexpress.svg' ) ); ?>"
                    alt="sdexpress">
            </a>
            <?php
            // Buscar vendedor por nombre de tienda
            $store_name = 'Nuevos Tiempos';
            $vendor_id = null;

            $users = get_users( array(
                'role' => 'seller',
                'meta_key' => 'dokan_store_name',
                'meta_value' => $store_name,
                'number' => 1
            ) );

            if ( ! empty( $users ) ) {
                $vendor_id = $users[0]->ID;
                $store_url = dokan_get_store_url( $vendor_id );
            } else {
                $store_url = '#'; // URL por defecto si no se encuentra
            }
            ?>
            <a href="<?php echo esc_url( $store_url ); ?>" style="text-decoration: none;">
                <img width="140" height="50" src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Imagen.png' ) ); ?>"
                    alt="nuevos tiempos">
            </a>    
        </div>
    </section>
</main>
<?php get_footer(); ?>