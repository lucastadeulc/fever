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

        // Call the method you want to test
        $pokemonData = $pokemonAPI->get_pokemon_data('pikachu');

        // Assertions
        $this->assertNotEmpty($pokemonData);
        $this->assertObjectHasAttribute('sprite_url', $pokemonData);
        $this->assertObjectHasAttribute('description', $pokemonData);
    }
}
