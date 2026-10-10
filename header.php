<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ============================================== -->
    <!-- PRECONNECT PARA OPTIMIZAR CARGA DE RECURSOS EXTERNOS
    <!-- ============================================== -->
    <link rel="preconnect" href="https://accounts.google.com">
    <link rel="preconnect" href="https://js.monei.com">

    <!-- ============================================== -->
    <!-- INFORMACIÓN BÁSICA DEL SITIO -->
    <!-- ============================================== -->
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    
    <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>">
    <meta name="keywords" content="marketplace, tiendas, productos, compras, Cuba, islabeya, ecommerce">
    <meta name="author" content="<?php bloginfo( 'name' ); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    <!-- ============================================== -->
    <!-- VERIFICACIÓN DE PROPIEDADES -->
    <!-- ============================================== -->
    
    <!-- ============================================== -->
    <!-- OPEN GRAPH (FACEBOOK, LINKEDIN, WHATSAPP, TELEGRAM) -->
    <!-- ============================================== -->
    <meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
    <meta property="og:image" content="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/Open graph.webp' ) ); ?>">
    <meta property="og:image:width" content="900">
    <meta property="og:image:height" content="900">
    <meta property="og:image:alt" content="<?php bloginfo( 'name' ); ?> - Marketplace de confianza">
    <meta property="og:locale" content="<?php echo get_locale(); ?>">
    <meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>">
    
    <!-- ============================================== -->
    <!-- FAVICONS (Múltiples dispositivos) -->
    <!-- ============================================== -->
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/favicon.svg' ) ); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/apple-touch-icon.png' ) ); ?>">
    <meta name="msapplication-TileColor" content="#15ad3c">
    <meta name="theme-color" content="#15ad3c">
    
    <!-- ============================================== -->
    <!-- PRELOAD DE RECURSOS CRÍTICOS -->
    <!-- ============================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php /* Preload solo assets reales del theme. Evita referencias a archivos inexistentes para no generar trabajo extra en la ruta crítica. */ ?>
    <link rel="preload" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/css/styles.css' ) ); ?>" as="style">
    <link rel="preload" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/js/preloader.js' ) ); ?>" as="script" />
    
    <!-- ============================================== -->
    <!-- FUENTES (Optimizadas) -->
    <!-- ============================================== -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </noscript>
    
    <!-- ============================================== -->
    <!-- CANONICAL URL (Evitar contenido duplicado) -->
    <!-- ============================================== -->
    <link rel="canonical" href="<?php echo esc_url( get_permalink() ); ?>">
    
    <!-- ============================================== -->
    <!-- BREADCRUMB LIST (Schema JSON-LD) -->
    <!-- ============================================== -->
    <?php if ( function_exists( 'bcn_display' ) || is_singular( 'product' ) ) : ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Inicio",
                "item": "<?php echo esc_url( home_url() ); ?>"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "<?php echo esc_js( wp_title( '', false ) ); ?>",
                "item": "<?php echo esc_url( get_permalink() ); ?>"
            }
        ]
    }
    </script>
    <?php endif; ?>
    
    <!-- ============================================== -->
    <!-- ORGANIZATION SCHEMA (SEO Local/General) -->
    <!-- ============================================== -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?php bloginfo( 'name' ); ?>",
        "url": "<?php echo esc_url( home_url() ); ?>",
        "logo": "<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL.svg' ) ); ?>",
        "sameAs": [
            "https://www.facebook.com/603289172865708/",
            "https://www.instagram.com/islabeya/"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+34 602 483 604",
            "contactType": "customer service",
            "areaServed": "CU",
            "availableLanguage": "Spanish"
        }
    }
    </script>
    
    <?php wp_head(); ?>
</head>
<body>
    <?php
    $preloader_enabled = get_theme_mod( 'islabeya_preloader_enable', true );
    $preloader_bg      = get_theme_mod( 'islabeya_preloader_bg', '#ffffff' );
    $preloader_image_id = (int) get_theme_mod( 'islabeya_preloader_image_id', 0 );
    $preloader_image_url = $preloader_image_id ? wp_get_attachment_image_url( $preloader_image_id, 'full' ) : '';
    ?>
    <div id="islabeya-preloader" class="<?php echo $preloader_enabled ? '' : 'is-hidden'; ?>" style="--islabeya-preloader-bg: <?php echo esc_attr( $preloader_bg ); ?>;">
        <div class="islabeya-preloader-content">
            <?php if ( $preloader_image_url ) : ?>
                <img src="<?php echo esc_url( $preloader_image_url ); ?>" alt="Cargando..." />
            <?php endif; ?>
        </div>
    </div>
    <header>

        <div class="header-top">
            <a href="<?php echo home_url() ?>">
                <img src="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/IDENTIFICADOR-VISUAL.svg' ) ); ?>" 
                alt="Logo IslaBeya, Identificador Visual Islabeya" />
            </a> 
            <div class="header-rigt">
                <nav class="nav-primary">
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( "order-tracking" ) ) ) ?>" class="btn font-medium">
                        <span class="icon-box"></span>
                        <span class="boton-text">Rastrear Envío</span>
                    </a>
                    <!-- Parte de plantilla: Categorias -->
                    <?php get_template_part( 'template parts/header/boton-categorias' ); ?>
                    <!-- Fin -->

                    <!-- Parte de plantilla: Vendedores -->
                    <?php get_template_part( 'template parts/header/boton-vendedores' ); ?>
                    <!-- Fin -->

                    <!-- Parte de plantilla: Favoritos -->
                    <?php get_template_part( 'template parts/header/boton-favoritos' ); ?>
                    <!-- Fin -->

                    <!-- Secciones activas según rol del usuario -->
                    <?php if ( is_user_logged_in() ) : 
                        $current_user = wp_get_current_user();
                        $user_roles = $current_user->roles;
                        
                        // Verificar si el usuario es vendedor (Dokan)
                        if ( in_array( 'seller', $user_roles ) && function_exists( 'dokan_get_navigation_url' ) ) :
                            // Usuario VENDEDOR → Dashboard de Dokan
                            ?>
                            <a href="<?php echo esc_url( dokan_get_navigation_url( 'dashboard' ) ); ?>" class="btn btn-second font-medium boton-sesion">
                                <span class="icon-shop icon-white"></span>
                                <span class="boton-text">Mi Tienda</span>
                            </a>
                        <?php else : 
                            // Usuario CLIENTE → Mi Cuenta
                            ?>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-second font-medium boton-sesion">
                                <span class="icon-user icon-white"></span>
                                <span class="boton-text">Mi Cuenta</span>
                            </a>
                        <?php endif; ?>
                        
                    <?php else : 
                        // Usuario NO LOGUEADO → Iniciar Sesión
                        ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-second font-medium boton-sesion">
                            <span class="icon-login icon-white"></span>
                            <span class="boton-text">Iniciar Sesión</span> 
                        </a>   
                    <?php endif; ?>
                    <!-- Fin -->
                </nav>
                <nav>
                    <input type="checkbox" id="sidebar-active">
                    <label for="sidebar-active" class="open-sidebar-button">
                        <span width="20" height="20" class="icon-hamburger"></span>
                    </label>
                    <label id="overlay" for="sidebar-active"></label>
                    <div class="links-container">
                        <label for="sidebar-active" class="close-sidebar-button">
                            <span style="font-size: 25px; cursor: pointer; color: #000; z-index: 10;">&times;</span>
                        </label>
                        
                        <a href="<?php echo esc_url( get_permalink( get_page_by_path( "order-tracking" ) ) ) ?>" class="btn font-medium">
                            <span class="icon-box"></span>
                            <span class="boton-text">Rastrear Envío</span>
                        </a>
                        
                        <!-- Botón Categorías MÓVIL (acordeón) -->
                        <div class="menu-item-categorias-movil">
                            <div class="categorias-movil-header">
                                <span class="icon-box"></span>
                                <span class="boton-text">Categorías</span>
                                <span class="icon-arrow-down-movil"></span>
                            </div>
                            <div class="categorias-movil-submenu">
                                <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="categoria-movil-item">
                                    Todos los productos
                                </a>
                                <?php
                                $categorias = get_terms( array(
                                    'taxonomy'   => 'product_cat',
                                    'hide_empty' => true,
                                    'parent'     => 0,
                                    'number'     => 20,
                                ) );
                                
                                if ( ! empty( $categorias ) ) :
                                    foreach ( $categorias as $categoria ) :
                                        ?>
                                        <a href="<?php echo get_term_link( $categoria ); ?>" class="categoria-movil-item">
                                            <?php echo esc_html( $categoria->name ); ?>
                                        </a>
                                    <?php endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                        
                        <!-- Botón Tiendas MÓVIL (acordeón) -->
                        <div class="menu-item-tiendas-movil">
                            <div class="tiendas-movil-header">
                                <span class="icon-shop"></span>
                                <span class="boton-text">Tiendas</span>
                                <span class="icon-arrow-down-movil"></span>
                            </div>
                            <div class="tiendas-movil-submenu">
                                <?php
                                global $wpdb;
                                
                                // Consulta directa: vendedores con al menos un producto publicado
                                $tiendas_activas = $wpdb->get_results(
                                    "SELECT DISTINCT u.ID, u.display_name 
                                    FROM {$wpdb->users} u
                                    INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                                    INNER JOIN {$wpdb->posts} p ON u.ID = p.post_author
                                    WHERE um.meta_key = 'wp_capabilities' 
                                    AND um.meta_value LIKE '%seller%'
                                    AND p.post_type = 'product'
                                    AND p.post_status = 'publish'
                                    GROUP BY u.ID
                                    ORDER BY u.display_name ASC
                                    LIMIT 50"
                                );
                                
                                if ( ! empty( $tiendas_activas ) ) :
                                    foreach ( $tiendas_activas as $tienda ) :
                                        $store_url = dokan_get_store_url( $tienda->ID );
                                        $store_name = ! empty( $tienda->display_name ) ? $tienda->display_name : get_user_meta( $tienda->ID, 'dokan_store_name', true );
                                        ?>
                                        <a href="<?php echo esc_url( $store_url ); ?>" class="tienda-movil-item">
                                            <?php echo esc_html( $store_name ); ?>
                                        </a>
                                    <?php endforeach;
                                else : ?>
                                    <div class="tienda-movil-item no-stores">No hay tiendas con productos</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Parte de plantilla: Favoritos -->
                        <?php get_template_part( 'template parts/header/boton-favoritos' ); ?>
                        <!-- Fin -->
                    </div>
                </nav>
            </div>
        </div>
        <div class="header-bottom">
            <?php echo do_shortcode('[ubicacion_selector]') ?>
            <?php echo do_shortcode('[fk_cart_menu]'); ?>
        </div>
    </header>