<?php 
class PokemonAPI
{
    private $base_url = 'https://pokeapi.co/api/v2/';
    const POKEMON_API_NO_DESCRIPTION = 'No description available.';

    public function get_pokemon_data($pokemon_name)
    {
        $url = $this->base_url . PokemonPlugin::POKEMON_PLUGIN_SLUG_NAME. '/' . $pokemon_name;
        $response = wp_safe_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $pokemon_data = json_decode($body);

        // Fetch the sprite URL for the default front view of the Pokémon
        $sprite_url = $pokemon_data->sprites->front_default;

        // Fetch the description
        $description = $this->get_pokemon_description($pokemon_data->species->url);

        // Fetch the attacks with short descriptions
        $attacks = $this->get_pokemon_attacks(array_slice($pokemon_data->moves, 0, PokemonPlugin::POKEMON_PLUGIN_MAX_ATTACKS_PER_POKEMON));

        // Add the sprite URL, description, number_old, and attacks to the Pokémon data
        $pokemon_data->sprite_url = $sprite_url;
        $pokemon_data->description = $description;
        $pokemon_data->attacks = $attacks;
        $pokemon_data->pokedex_number_old = isset($pokemon_data->game_indices[0]->game_index) ? $pokemon_data->game_indices[0]->game_index : 'Not available';

        return $pokemon_data;
    }

    private function get_pokemon_description($species_url)
    {
        $response = wp_safe_remote_get($species_url);

        if (is_wp_error($response)) {
            return false;
        }

        $species_data = json_decode(wp_remote_retrieve_body($response));

        // Extract the English description (or use a default if not available)
        foreach ($species_data->flavor_text_entries as $entry) {
            if ($entry->language->name === 'en') {
                return $entry->flavor_text;
            }
        }

        return $this::POKEMON_API_NO_DESCRIPTION;
    }

    private function get_attack_description($attack_data)
    {
        if (isset($attack_data->effect_entries) && is_array($attack_data->effect_entries)) {
            foreach ($attack_data->effect_entries as $entry) {
                if ($entry->language->name === 'en' && !empty($entry->short_effect)) {
                    return $entry->short_effect;
                }
            }
        }
        return $this::POKEMON_API_NO_DESCRIPTION;
    }

    private function get_multiple_attack_data($move_urls)
    {
        // This could have been improved to do multiple requests per once
        $attack_data = [];
    
        foreach ($move_urls as $url) {
            $response = wp_safe_remote_request($url);
    
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body);
    
                if (isset($data->id)) {
                    $attack_data[$url] = $data;
                }
            }
        }
    
        return $attack_data;
    }    
    
    private function get_pokemon_attacks($moves)
    {
        if (!$moves) {
            return false;
        }
    
        $attacks = [];
    
        $move_urls = array_map(function($move) {
            return $move->move->url;
        }, $moves);
    
        // Retrieve attack data for all moves at once
        $attack_data = $this->get_multiple_attack_data($move_urls);
    
        foreach ($moves as $move) {
            $attack_name = $move->move->name;
    
            if (isset($attack_data[$move->move->url])) {
                $attack_description = $this->get_attack_description($attack_data[$move->move->url]);
    
                if ($attack_description) {
                    $attacks[] = [
                        'name' => $attack_name,
                        'description' => $attack_description,
                    ];
                }
            }
        }
    
        return $attacks;
    }
    

    public function get_first_5_pokemon_types()
    {
        $url = $this->base_url . 'type?limit=5';
        $response = wp_safe_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $type_data = json_decode($body);

        $types = [];

        foreach ($type_data->results as $type) {
            $types[] = [
                'name' => $type->name
            ];
        }

        return $types;
    }
}
