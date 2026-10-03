<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- ============================================== -->
    <!-- INFORMACIÓN BÁSICA DEL SITIO -->
    <!-- ============================================== -->
    <title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
    
    <meta name="description" content="<?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>">
    <meta name="keywords" content="marketplace, tiendas, productos, compras, Cuba, islabeya, ecommerce">
    <meta name="author" content="<?php bloginfo( 'name' ); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    <!-- ============================================== -->
    <!-- VERIFICACIÓN DE PROPIEDADES -->
    <!-- ============================================== -->
    <meta name="google-site-verification" content="TU_CODIGO_GOOGLE">
    <meta name="facebook-domain-verification" content="TU_CODIGO_FACEBOOK">
    
    <!-- ============================================== -->
    <!-- OPEN GRAPH (FACEBOOK, LINKEDIN, WHATSAPP, TELEGRAM) -->
    <!-- ============================================== -->
    <meta property="og:title" content="<?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
    <meta property="og:image" content="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/og-image.jpg' ) ); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?php bloginfo( 'name' ); ?> - Marketplace de confianza">
    <meta property="og:locale" content="<?php echo get_locale(); ?>">
    <meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>">
    
    <!-- ============================================== -->
    <!-- TWITTER CARDS -->
    <!-- ============================================== -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( get_bloginfo( 'description', 'display' ) ); ?>">
    <meta name="twitter:image" content="<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/og-image.jpg' ) ); ?>">
    <meta name="twitter:site" content="@tu_usuario_twitter">
    
    <!-- ============================================== -->
    <!-- FAVICONS (Múltiples dispositivos) -->
    <!-- ============================================== -->
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/favicon.svg' ) ); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/favicon-32x32.png' ) ); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/favicon-16x16.png' ) ); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/apple-touch-icon.png' ) ); ?>">
    <link rel="manifest" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/favicon/site.webmanifest' ) ); ?>">
    <meta name="msapplication-TileColor" content="#15ad3c">
    <meta name="theme-color" content="#15ad3c">
    
    <!-- ============================================== -->
    <!-- PRELOAD DE RECURSOS CRÍTICOS -->
    <!-- ============================================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/css/styles.css' ) ); ?>" as="style">
    <link rel="preload" href="<?php echo esc_url( get_parent_theme_file_uri( '/assets/js/main.js' ) ); ?>" as="script">
    
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
    <!-- GOOGLE ANALYTICS / TAG MANAGER (Código diferido) -->
    <!-- ============================================== -->
    <?php if ( ! is_admin() && ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) : ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TU_ID_ANALYTICS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-TU_ID_ANALYTICS', { 'anonymize_ip': true });
    </script>
    <?php endif; ?>
    
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
        "logo": "<?php echo esc_url( get_parent_theme_file_uri( '/assets/img/logo.svg' ) ); ?>",
        "sameAs": [
            "https://www.facebook.com/IslaBeya/",
            "https://www.instagram.com/IslaBeya/",
            "https://x.com/IslaBeya"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+53 5XXX XXXX",
            "contactType": "customer service",
            "areaServed": "CU",
            "availableLanguage": "Spanish"
        }
    }
    </script>
    
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
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