<?php 

add_action( 'wp_enqueue_scripts', 'my_enqueue_assets' ); 

function my_enqueue_assets() { 

    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' ); 

} 

// custom admin login logo




function custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url(/wp-content/uploads/logo-admin.png) !important;  background-size:150px!Important;height:95px!Important;width:150px!Important;}
	</style>';
}
add_action('login_head', 'custom_login_logo');


function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Argoweb';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );



// IMPOSTA BLANK PAGE NELLE PAGINE
function wpse196289_default_page_template() {
    global $post;

        $post->page_template = "page-template-blank.php";
 /*   if ( 'page' == $post->post_type 
        && 0 != count( get_page_templates( $post ) ) 
        && get_option( 'page_for_posts' ) != $post->ID // Not the page for listing posts
        && '' == $post->page_template // Only when page_template is not set
    ) {
        $post->page_template = "page-template-blank.php";
    }
*/
}

add_action('add_meta_boxes', 'wpse196289_default_page_template', 1);

// FINE
// 

// IMPOSTA DIVI BUILDER DI DEFAULT
add_action('load-post-new.php', 'dbc_load_post_new_php'); 

function dbc_load_post_new_php() { 
	add_filter('et_builder_always_enabled', '__return_true');
}; 

// FINE

// SOSTITUZIONE POST-TEMPLATE CON PAGE-TEMPLATE SUL BODY_CLASS

add_filter( 'body_class', 'alter_body_class', 20, 2 );
function alter_body_class( $classes ) {
    foreach( $classes as $key => $value ) {
        if ( $value === 'post-template-page-template-blank') {
            //unset( $classes[ $key ] );
	    $classes[$key] = 'page-template-page-template-blank';
        }

        if ( $value === 'post-template') {
            //unset( $classes[ $key ] );
	    $classes[$key] = 'page-template';
        }
         if ( $value === 'post-template-page-template-blank-php') {
            //unset( $classes[ $key ] );
	    $classes[$key] = 'page-template-page-template-blank-php';
        }
 
    }
 //   return array_merge( $classes, array( 'page' ) );
    return array_merge( $classes );
}

// fine


function circolari_taxonomy() {  
    register_taxonomy(  
        'circolai_categories',   
        'circolari_type',        
        array(  
            'hierarchical' => true,  
            'label' => 'Categorie Circolari', 
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'themes', 
                'with_front' => false 
            )
        )  
    );  
}  
add_action( 'init', 'circolari_taxonomy');


// Register Circolari Post Type
if ( ! function_exists('circolari_post_type') ) {

    // Register Custom Post Type
    function circolari_post_type() {
    
        $labels = array(
            'name'                  => _x( 'Circolari', 'Post Type General Name', 'edutheme' ),
            'singular_name'         => _x( 'Circolare', 'Post Type Singular Name', 'edutheme' ),
            'menu_name'             => __( 'Circolari', 'edutheme' ),
            'name_admin_bar'        => __( 'Circolari', 'edutheme' ),
            'archives'              => __( 'Archivi Circolari', 'edutheme' ),
            'attributes'            => __( 'Attributi Circolari', 'edutheme' ),
            'parent_item_colon'     => __( 'Parent Item:', 'edutheme' ),
            'all_items'             => __( 'Tutte le Circolari', 'edutheme' ),
            'add_new_item'          => __( 'Aggiungi nuova', 'edutheme' ),
            'add_new'               => __( 'Aggiungi nuova', 'edutheme' ),
            'new_item'              => __( 'Nuova', 'edutheme' ),
            'edit_item'             => __( 'Modifica', 'edutheme' ),
            'update_item'           => __( 'Aggiorna', 'edutheme' ),
            'view_item'             => __( 'Guarda', 'edutheme' ),
            'view_items'            => __( 'Guarda', 'edutheme' ),
            'search_items'          => __( 'Cerca', 'edutheme' ),
        );
        $args = array(
            'label'                 => __( 'Circolare', 'edutheme' ),
            'description'           => __( 'Gestione Circolari', 'edutheme' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array( 'circolai_categories' ),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'circolari_type', $args );
    
    }
    add_action( 'init', 'circolari_post_type', 0 );
    
}

// Create Cricolari PDF Custom Field
function circolari_pdf_field( $meta_boxes ) {
	$prefix = '';

	$meta_boxes[] = array(
		'id' => 'circolari',
		'title' => esc_html__( 'Circolari', 'metabox-online-generator' ),
		'post_types' => array('circolari_type' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => 'false',
		'fields' => array(
			array(
				'id' => 'pdf_input',
				'type' => 'file_input',
				'name' => esc_html__( 'PDF', 'metabox-online-generator' ),
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'circolari_pdf_field' );


// Query Circolari
function query_circolari() {
    
    query_posts(array( 
        'post_type' => 'circolari_type',
        'showposts' => 10 
    ) );  
    while (have_posts()) : the_post(); ?>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
        <p><?php echo get_the_excerpt(); ?></p>
        <?php
        // Echo the url of PDF Circolare
        rwmb_the_value( 'pdf_input' );

        endwhile;
}

add_shortcode('circolari', 'query_circolari');