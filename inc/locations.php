<?php 
// Registrar Taxonomía Personalizada: Provincias
function registrar_taxonomia_provincias() {
    $labels = array(
        'name'              => _x( 'Provincias', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Provincia', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Buscar Provincias', 'textdomain' ),
        'all_items'         => __( 'Todas las Provincias', 'textdomain' ),
        'parent_item'       => __( 'Provincia Padre', 'textdomain' ),
        'parent_item_colon' => __( 'Provincia Padre:', 'textdomain' ),
        'edit_item'         => __( 'Editar Provincia', 'textdomain' ),
        'update_item'       => __( 'Actualizar Provincia', 'textdomain' ),
        'add_new_item'      => __( 'Añadir Nueva Provincia', 'textdomain' ),
        'new_item_name'     => __( 'Nombre de la Nueva Provincia', 'textdomain' ),
        'menu_name'         => __( 'Provincias', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true, // Mantenido en 'true' para que se comporte como categorías (con jerarquía)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'provincia' ),
        'show_in_rest'      => true, // Fundamental para que funcione con el editor de bloques (Gutenberg) y la API REST
    );

    // Registrada para el post type 'product'. Puedes cambiarlo o añadir más, ej: array('post', 'product')
    register_taxonomy( 'provincia', array( 'product' ), $args );
}
add_action( 'init', 'registrar_taxonomia_provincias', 0 );