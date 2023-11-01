<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

// Random functionality
function custom_random_pokemon_endpoint() {
    add_rewrite_rule('random/?$', 'index.php?random_pokemon=1', 'top');
    add_rewrite_tag('%random_pokemon%', '1');
}
add_action('init', 'custom_random_pokemon_endpoint');

function handle_random_pokemon_request() {
    if (get_query_var('random_pokemon')) {
        $random_pokemon_id = get_random_pokemon_id();

        if ($random_pokemon_id) {
            $permalink = get_permalink($random_pokemon_id);

            wp_redirect($permalink);
            exit;
        }
    }
}
add_action('template_redirect', 'handle_random_pokemon_request');

function get_random_pokemon_id() {
    $args = array(
        'post_type' => 'pokemon',
        'posts_per_page' => -1,
        'fields' => 'ids', // Retrieve only the IDs
    );

    $pokemon_ids = get_posts($args);

    if ($pokemon_ids) {
        // Generate a random index within the range of available IDs
        $random_index = array_rand($pokemon_ids);

        // Get the random Pokémon post ID
        $random_pokemon_id = $pokemon_ids[$random_index];

        return $random_pokemon_id;
    }

    return null;
}
// End Random Functionality

// Generate Functionality
function custom_generate_pokemon_endpoint() {
    add_rewrite_rule('generate/?$', 'index.php?generate_pokemon=1', 'top');
    add_rewrite_tag('%generate_pokemon%', '1');
}
add_action('init', 'custom_generate_pokemon_endpoint');

function handle_generate_pokemon_request() {
    if (get_query_var('generate_pokemon')) {
        // Check if the current user has the required permissions (e.g., post creation)
        if (current_user_can('publish_posts')) {
            $pokemon_plugin = new PokemonPlugin();
            $random_pokemon_data = $pokemon_plugin->generate_random_pokemon();

			if ($random_pokemon_data) {
                $pokemon = new Pokemon();
                $pokemon_id = $pokemon->create_or_update($random_pokemon_data->name, $random_pokemon_data);

                if ($pokemon_id) {
                    $permalink = get_permalink($pokemon_id);
                    wp_redirect($permalink);
                    exit;
                }
            }
        } else {
            wp_die('You do not have permission to generate Pokémon.');
        }
    }
}

add_action('template_redirect', 'handle_generate_pokemon_request');
// End Generate Functionality

// Enqueue script for pokemon archive
function enqueue_pokemon_fetch_js() {
    wp_enqueue_script('pokemon-fetch', get_stylesheet_directory_uri() . '/js/pokemon-fetch.js', array('jquery'), null, true);

    wp_localize_script('pokemon-fetch', 'pokemonData', array(
        'siteURL' => get_site_url(),
        'route' => PokemonREST::POKEMON_REST_EXPORT_ROUTE,
        'detailed' => PokemonREST::POKEMON_REST_EXPORT_ROUTE_DETAILED,
        'types' => PokemonREST::POKEMON_REST_EXPORT_ROUTE_TYPES,
    ));
}

add_action('wp_enqueue_scripts', 'enqueue_pokemon_fetch_js');
// End Enqueue script for pokemon archive

  