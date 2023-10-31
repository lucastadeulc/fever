<?php

/**
 * Plugin Name:     Pokemon
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     pokemon
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pokemon
 */
require_once dirname(__FILE__) . '/classes/PokemonAPI.php';
require_once dirname(__FILE__) . '/classes/PokemonClearer.php';
require_once dirname(__FILE__) . '/classes/Pokemon.php';
require_once dirname(__FILE__) . '/classes/PokemonREST.php';
//PokemonClearer::clear_all_pokemons();

class PokemonPlugin
{
    const POKEMON_PLUGIN_SLUG_NAME = 'pokemon';

    public function __construct()
    {
        add_action('init', array($this, 'register_pokemon_post_type'));
    }

    public function register_pokemon_post_type()
    {
        $labels = array(
            'name' => 'Pokémon',
            'singular_name' => 'Pokémon',
            'menu_name' => 'Pokémon',
            'all_items' => 'All Pokémon',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Pokémon',
            'edit_item' => 'Edit Pokémon',
            'new_item' => 'New Pokémon',
            'view_item' => 'View Pokémon',
            'search_items' => 'Search Pokémon',
            'not_found' => 'No Pokémon found',
            'not_found_in_trash' => 'No Pokémon found in Trash',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => $this::POKEMON_PLUGIN_SLUG_NAME),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        );

        register_post_type($this::POKEMON_PLUGIN_SLUG_NAME, $args);
    }

    public function generate_example_pokemon_from_api()
    {
        $api = new PokemonAPI();
        $pokemon_names = array('pikachu', 'bulbasaur', 'charizard');

        foreach ($pokemon_names as $pokemon_name) {
            $pokemon_data = $api->get_pokemon_data($pokemon_name);

            if ($pokemon_data) {
                Pokemon::create_or_update($pokemon_name, $pokemon_data);
            }
        }
    }

    public function generate_random_pokemon() {
        $poke_api = new PokemonAPI();

        // Get a random Pokémon from the API
        $random_pokemon_id = rand(1, 898); 

        // Fetch data for the random Pokémon
        $pokemon_data = $poke_api->get_pokemon_data($random_pokemon_id);

        return $pokemon_data;
    }
}

register_activation_hook(__FILE__, 'activate_pokemon_plugin');

function activate_pokemon_plugin() {
    $pokemon_plugin = new PokemonPlugin();
    $pokemon_plugin->generate_example_pokemon_from_api();
}

register_deactivation_hook(__FILE__, 'clear_pokemon_data_on_deactivation');

function clear_pokemon_data_on_deactivation() {
    PokemonClearer::clear_all_pokemons();
}

// Initialize the plugin
$pokemon_plugin = new PokemonPlugin();
$pokemon_rest = new PokemonREST();

