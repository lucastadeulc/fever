<?php

class PokemonREST
{
    const POKEMON_REST_NOT_FOUND_SINGLE = 'Pokémon not found';
    const POKEMON_REST_NOT_FOUND_MANY = 'No Pokémon data found';
    const POKEMON_REST_DEFAULT_ROUTE = 'custom/v1';
    const POKEMON_REST_EXPORT_ROUTE = '/wp-json/custom/v1/';
    const POKEMON_REST_EXPORT_ROUTE_SINGLE = 'pokemon';
    const POKEMON_REST_EXPORT_ROUTE_MANY = 'pokemons';
    const POKEMON_REST_EXPORT_ROUTE_DETAILED = 'pokemons-detailed';
    const POKEMON_REST_EXPORT_ROUTE_TYPES = 'pokemons-types';

    public function __construct()
    {
        // Register REST API routes here
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        // Single pokemon endpoint
        // wp-json/custom/v1/pokemon/{ID}
        register_rest_route($this::POKEMON_REST_DEFAULT_ROUTE, '/'.$this::POKEMON_REST_EXPORT_ROUTE_SINGLE.'/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_pokemon_data'),
        ));

        // All pokemon endpoint
        // wp-json/custom/v1/pokemons
        register_rest_route($this::POKEMON_REST_DEFAULT_ROUTE, '/'.$this::POKEMON_REST_EXPORT_ROUTE_MANY, array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_pokemon_list'),
        ));

        // All pokemon described endpoint
        // wp-json/custom/v1/pokemons-detailed/grass
        register_rest_route($this::POKEMON_REST_DEFAULT_ROUTE, '/'.$this::POKEMON_REST_EXPORT_ROUTE_DETAILED.'(/(?P<type>[\w-]+))?', array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_pokemon_list_with_details'),
        ));

        // 5 types endpoint
        // wp-json/custom/v1/pokemons-types
        register_rest_route($this::POKEMON_REST_DEFAULT_ROUTE, '/'.$this::POKEMON_REST_EXPORT_ROUTE_TYPES, array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_first_5_pokemon_types'),
        ));
    }

    public function get_pokemon_list($request)
    {
        $args = array(
            'post_type' => PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME,
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $pokemon_ids = get_posts($args);
        $pokemon_list = array();

        if (!$pokemon_ids) {
            return rest_ensure_response(array('message' => $this::POKEMON_REST_NOT_FOUND_MANY), 404);
        }

        foreach ($pokemon_ids as $pokemon_id) {
            $pokedex_number_recent = get_post_meta($pokemon_id, 'pokedex_number_recent', true);

            if ($pokedex_number_recent) {
                $pokemon_list[] = array(
                    'id' => $pokedex_number_recent,
                    'name' => get_the_title($pokemon_id),
                );
            }
        }

        return rest_ensure_response($pokemon_list);
    }

    public function get_pokemon_data($request)
    {
        $pokemon_id = $request['id'];

        // Check if the Pokémon post with the given ID exists
        $post = get_post($pokemon_id);

        if (!$post || $post->post_type !== PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME) {
            return new WP_Error('not_found', $this::POKEMON_REST_NOT_FOUND_SINGLE, array('status' => 404));
        }

        $pokemon_name = get_the_title($pokemon_id);
        $pokemon_type = get_post_meta($pokemon_id, 'pokemon_primary_type');
        $pokemon_secondary_type = get_post_meta($pokemon_id, 'pokemon_secondary_type');
        $pokemon_weight = get_post_meta($pokemon_id, 'pokemon_weight', true);
        $pokemon_pokedex_number_old = get_post_meta($pokemon_id, 'pokedex_number_old', true);
        $pokemon_pokedex_number_recent = get_post_meta($pokemon_id, 'pokedex_number_recent', true);
        $pokemon_description = get_post_meta($pokemon_id, 'pokemon_description', true);
        $pokemon_attacks = get_post_meta($pokemon_id, 'pokemon_attacks', true);
        $pokemon_photo = get_the_post_thumbnail_url($pokemon_id, 'full');

        $full_pokemon_data = array(
            'id' => $pokemon_id,
            'name' => $pokemon_name,
            'primary_type' => $pokemon_type,
            'secondary_type' => $pokemon_secondary_type,
            'weight' => $pokemon_weight,
            'pokedex_number_old' => $pokemon_pokedex_number_old,
            'pokedex_number_recent' => $pokemon_pokedex_number_recent,
            'description' => $pokemon_description,
            'attacks' => $pokemon_attacks,
            'photo' => $pokemon_photo,
        );

        return rest_ensure_response($full_pokemon_data);
    }

    public function get_pokemon_list_with_details($request)
    {
        $type = $request->get_param('type');

        $args = array(
            'post_type' => PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME,
            'posts_per_page' => -1,
        );

        // If a type is specified, add the type filter to the query
        if (!empty($type)) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'pokemon_primary_type',
                    'value' => $type,
                    'compare' => '=',
                ),
                array(
                    'key' => 'pokemon_secondary_type',
                    'value' => $type,
                    'compare' => '=',
                ),
            );
        }

        $pokemon_query = new WP_Query($args);
        $pokemon_list = array();

        if ($pokemon_query->have_posts()) {
            while ($pokemon_query->have_posts()) {
                $pokemon_query->the_post();
                $pokemon_id = get_the_ID();

                $pokemon_name = get_the_title();
                $pokemon_primary_type = get_post_meta($pokemon_id, 'pokemon_primary_type', true);
                $pokemon_secondary_type = get_post_meta($pokemon_id, 'pokemon_secondary_type', true);
                $pokemon_weight = get_post_meta($pokemon_id, 'pokemon_weight', true);
                $pokedex_number_old = get_post_meta($pokemon_id, 'pokedex_number_old', true);
                $pokedex_number_recent = get_post_meta($pokemon_id, 'pokedex_number_recent', true);
                $pokemon_description = get_post_meta($pokemon_id, 'pokemon_description', true);
                //$pokemon_attacks = get_post_meta($pokemon_id, 'pokemon_attacks', true);
                $pokemon_photo = get_the_post_thumbnail_url($pokemon_id, 'full');
                $pokemon_link = get_the_permalink($pokemon_id);

                if ($pokedex_number_recent && $pokemon_name && $pokemon_weight && $pokemon_description && $pokemon_photo) {
                    $pokemon_list[] = array(
                        'id' => $pokemon_id,
                        'name' => $pokemon_name,
                        'primary_type' => $pokemon_primary_type,
                        'secondary_type' => $pokemon_secondary_type,
                        'weight' => $pokemon_weight,
                        'pokedex_number_old' => $pokedex_number_old,
                        'pokedex_number_recent' => $pokedex_number_recent,
                        'description' => $pokemon_description,
                        //'attacks' => $pokemon_attacks,
                        'photo' => $pokemon_photo,
                        'link' => $pokemon_link,
                    );
                }
            }

            wp_reset_postdata();

            // Send the formatted data as a JSON response
            return rest_ensure_response($pokemon_list);
        } else {
            // Handle the case when no Pokémon data is found
            return rest_ensure_response(array('message' => $this::POKEMON_REST_NOT_FOUND_MANY), 404);
        }
    }

    public function get_first_5_pokemon_types($request)
    {
        $poke_api = new PokemonAPI();
        $types = $poke_api->get_first_5_pokemon_types();

        if ($types) {
            return rest_ensure_response($types);
        } else {
            return rest_ensure_response(array(), 404);
        }
    }
}
