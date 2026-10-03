<?php
/**
 * Permitir subida de SVG solo en Media Library para usuarios con rol administrador.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function islabeya_user_is_admin_role() {
    $user = wp_get_current_user();
    if ( ! $user || ! $user->exists() ) {
        return false;
    }
    return in_array( 'administrator', (array) $user->roles, true );
}

function islabeya_allow_svg_mime_types( $mimes ) {
    // Permitir solo a administradores.
    if ( ! islabeya_user_is_admin_role() ) {
        return $mimes;
    }

    // Agregar el mime para que WordPress/Media Library acepte el tipo.
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';

    return $mimes;
}


function islabeya_filter_uploaded_svg_content( $file ) {
    // Solo para administradores.
    if ( ! islabeya_user_is_admin_role() ) {
        return $file;
    }

    $ext = strtolower( pathinfo( isset( $file['name'] ) ? $file['name'] : '', PATHINFO_EXTENSION ) );
    if ( 'svg' !== $ext && 'svgz' !== $ext ) {
        return $file;
    }

    $filepath = isset( $file['tmp_name'] ) ? $file['tmp_name'] : '';
    if ( ! $filepath || ! file_exists( $filepath ) ) {
        return $file;
    }

    $contents = file_get_contents( $filepath );
    if ( $contents === false ) {
        return $file;
    }

    // Sanitización simple para evitar contenido activo.
    // 1) Quitar tags/atributos peligrosos
    // 2) Quitar cualquier cadena javascript: / data: url
    $contents = preg_replace('#<\s*(script|iframe|object|embed|link|meta|style)\b[^>]*>(.*?)<\s*/\s*\1\s*>#is', '', $contents);
    $contents = preg_replace('#<\s*(script|iframe|object|embed|link|meta|style)\b[^>]*>#is', '', $contents);

    // Eliminar event handlers y estilos peligrosos
    $contents = preg_replace('/\son\w+\s*=\s*(["\"]).*?\1/i', '', $contents);
    $contents = preg_replace('/javascript\s*:/i', '', $contents);
    $contents = preg_replace('/data\s*:/i', '', $contents);

    // Quitar referencias a xlink:href
    $contents = preg_replace('/xlink:href\s*=\s*(["\"]).*?\1/i', '', $contents);

    file_put_contents( $filepath, $contents );

    return $file;
}



// 1) MIME types
add_filter( 'upload_mimes', 'islabeya_allow_svg_mime_types' );

// 2) Sanitize al subir
add_filter( 'wp_handle_upload', function ( $file ) {
    if ( isset( $file['type'] ) && 'image/svg+xml' === $file['type'] ) {
        $file = islabeya_filter_uploaded_svg_content( $file );
    }
    return $file;
} );

// 3) Bloquear SVG a no-admins en el punto de validación de mime
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
    $ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
    if ( 'svg' === $ext || 'svgz' === $ext ) {
        if ( ! islabeya_user_is_admin_role() ) {
            $data['ext']  = false;
            $data['type'] = false;
        }
    }
    return $data;
}, 10, 4 );

