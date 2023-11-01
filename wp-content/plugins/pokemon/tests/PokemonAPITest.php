<?php

/**
 * Class PokemonAPITest
 *
 * @package Pokemon
 */

class PokemonAPITest extends WP_UnitTestCase
{
    public function testGetPokemonData()
    {
        // Create an instance of the PokemonAPI class
        $pokemonAPI = new PokemonAPI();

        // Mock the wp_safe_remote_get function to return a sample JSON response
        $response = json_encode([
            'sprites' => ['front_default' => 'sprite_url'],
            'species' => ['url' => 'species_url'],
        ]);

        $this->wp_remote_get_response = $response;

        // Call the method you want to test
        $pokemonData = $pokemonAPI->get_pokemon_data('pikachu');

        // Assertions
        $this->assertNotEmpty($pokemonData);
        $this->assertObjectHasAttribute('sprite_url', $pokemonData);
        $this->assertObjectHasAttribute('description', $pokemonData);
    }
}
