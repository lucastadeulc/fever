<?php

class Pokemon {
    public static function create_or_update($pokemon_name, $pokemon_data) {
        $args = array(
            'post_type' => PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME,
            'name' => $pokemon_name,
            'posts_per_page' => 1,
        );

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            // Create a new Pokémon post if it doesn't exist
            $post_id = wp_insert_post(array(
                'post_title' => $pokemon_name,
                'post_type' => PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME,
                'post_status' => 'publish',
            ));
        } else {
            $query->the_post();
            $post_id = get_the_ID();
        }

        if (!is_wp_error($post_id)) {
            // Update or set custom fields with Pokémon properties
            update_post_meta($post_id, 'pokemon_primary_type', $pokemon_data->types[0]->type->name);
            update_post_meta($post_id, 'pokemon_secondary_type', isset($pokemon_data->types[1]) ? $pokemon_data->types[1]->type->name : '');
            update_post_meta($post_id, 'pokemon_weight', $pokemon_data->weight);
            update_post_meta($post_id, 'pokedex_number_old', $pokemon_data->id);
            update_post_meta($post_id, 'pokedex_number_recent', $pokemon_data->id);
            update_post_meta($post_id, 'pokemon_description', $pokemon_data->description);

            // Set the featured image as the Pokémon's sprite
            if (!empty($pokemon_data->sprite_url)) {
                $image_data = file_get_contents($pokemon_data->sprite_url);
                $upload_dir = wp_upload_dir();
                $unique_file_name = wp_unique_filename($upload_dir['path'], 'pokemon-' . $pokemon_name . '.png');
                $filename = $upload_dir['path'] . '/' . $unique_file_name;

                // Save the sprite as a file
                file_put_contents($filename, $image_data);

                // Prepare the attachment data
                $filetype = wp_check_filetype(basename($filename), null);
                $attachment = array(
                    'post_mime_type' => $filetype['type'],
                    'post_title' => sanitize_file_name($unique_file_name),
                    'post_content' => '',
                    'post_status' => 'inherit',
                );

                // Insert the attachment
                $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                // Set the featured image
                set_post_thumbnail($post_id, $attach_id);
            }

            // Update or set attacks with short descriptions
            if (!empty($pokemon_data->attacks)) {
                self::update_pokemon_attacks($post_id, $pokemon_data->attacks);
            }
        }

        wp_reset_postdata(); // Reset the query to avoid interference with other queries.

        echo $post_id;
        return $post_id;
    }

    private static function update_pokemon_attacks($post_id, $attacks) {
        $existing_attacks = get_post_meta($post_id, 'pokemon_attacks', true);
        $existing_attacks = is_array($existing_attacks) ? $existing_attacks : array();

        // Combine existing attacks and new attacks
        $updated_attacks = array_merge($existing_attacks, $attacks);

        // Set the attacks field
        update_post_meta($post_id, 'pokemon_attacks', $updated_attacks);
    }
}
