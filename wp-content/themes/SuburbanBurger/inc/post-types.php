<?php
add_action('init', 'all_post_types');

function all_post_types() {
    $labels = array(
        'name'               => __( 'Products'),
        'singular_name'      => __( 'Product'),
        'menu_name'          => __( 'Products'),
        'name_admin_bar'     => __( 'Product'),
        'add_new'            => __( 'Add New'),
        'add_new_item'       => __( 'Add New Product'),
        'new_item'           => __( 'New Product'),
        'edit_item'          => __( 'Edit Product'),
        'view_item'          => __( 'View Product'),
        'all_items'          => __( 'All Products'),
        'search_items'       => __( 'Search Products'),
        'parent_item_colon'  => __( 'Parent Products:' ),
        'not_found'          => __( 'No products found.'),
        'not_found_in_trash' => __( 'No products found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'product' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'      => get_template_directory_uri().'/images/cart.png',
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'product', $args );
    
    $labels = array(
        'name'              => __( 'Categories'),
        'singular_name'     => __( 'Category'),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Category' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'item-cat' ),
    );

    register_taxonomy( 'item-cat', array( 'product' ), $args );

    $labels = array(
        'name'                       => __( 'Tags'),
        'singular_name'              => __( 'Tag'),
        'search_items'               => __( 'Search Tags' ),
        'popular_items'              => __( 'Popular Tags' ),
        'all_items'                  => __( 'All Tags' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Tag' ),
        'update_item'                => __( 'Update Tag' ),
        'add_new_item'               => __( 'Add New Tag' ),
        'new_item_name'              => __( 'New Tag Name' ),
        'separate_items_with_commas' => __( 'Separate tags with commas' ),
        'add_or_remove_items'        => __( 'Add or remove tags' ),
        'choose_from_most_used'      => __( 'Choose from the most used tags' ),
        'not_found'                  => __( 'No tags found.' ),
        'menu_name'                  => __( 'Tags' ),
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'item-tag' ),
    );

    register_taxonomy( 'iten-tag', 'product', $args );
    
    $labels = array(
        'name'               => __( 'Menus'),
        'singular_name'      => __( 'Menu'),
        'menu_name'          => __( 'Menus'),
        'name_admin_bar'     => __( 'Menu'),
        'add_new'            => __( 'Add New'),
        'add_new_item'       => __( 'Add New Menu'),
        'new_item'           => __( 'New Menu'),
        'edit_item'          => __( 'Edit Menu'),
        'view_item'          => __( 'View Menu'),
        'all_items'          => __( 'All Menus'),
        'search_items'       => __( 'Search Menus'),
        'parent_item_colon'  => __( 'Parent Menus:' ),
        'not_found'          => __( 'No menus found.'),
        'not_found_in_trash' => __( 'No menus found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'menu' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'      => get_template_directory_uri().'/images/menu.png',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt')
    );

    register_post_type( 'menu', $args );
    
    $labels = array(
        'name'              => __( 'Categories'),
        'singular_name'     => __( 'Category'),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Category' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'menu-cat' ),
    );

    register_taxonomy( 'menu-cat', array( 'menu' ), $args );
    
    $labels = array(
        'name'               => __( 'Sliders'),
        'singular_name'      => __( 'Slider'),
        'menu_name'          => __( 'Sliders'),
        'name_admin_bar'     => __( 'Slider'),
        'add_new'            => __( 'Add New'),
        'add_new_item'       => __( 'Add New Slider'),
        'new_item'           => __( 'New Slider'),
        'edit_item'          => __( 'Edit Slider'),
        'view_item'          => __( 'View Slider'),
        'all_items'          => __( 'All Sliders'),
        'search_items'       => __( 'Search Sliders'),
        'parent_item_colon'  => __( 'Parent Sliders:' ),
        'not_found'          => __( 'No menus found.'),
        'not_found_in_trash' => __( 'No menus found in Trash.')
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'slider' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'      => get_template_directory_uri().'/images/slider.png',
        'supports'           => array( 'title', 'editor', 'thumbnail')
    );

    register_post_type( 'sliders', $args );
}