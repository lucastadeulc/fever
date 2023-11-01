<?php

/**
 * Class PokemonRESTTest
 *
 * @package Pokemon
 */

class PokemonRESTTest extends WP_UnitTestCase
{
    public function test_get_pokemon_list_with_pokemon()
    {
        // Create a sample Pokémon data
        $pokemon_name = 'Pikachu';
        $pokemon_data = (object) array(
            'types' => array(
                (object) array('type' => (object) array('name' => 'Electric')),
            ),
            'weight' => 60,
            'pokedex_number_old' => '25',
            'id' => 25,
            'description' => 'An electric Pokémon.',
            'attacks' => [['name' => 'Thunderbolt', 'description' => 'Quick Attack']],
        );

        // Call the create_or_update method
        $post_id = Pokemon::create_or_update($pokemon_name, $pokemon_data);

        // Get an instance of your PokemonREST class
        $pokemon_rest = new PokemonREST();

        // Create a WP_REST_Request for the route you want to test
        $request = new WP_REST_Request('GET', '/' . $pokemon_rest::POKEMON_REST_EXPORT_ROUTE_MANY);

        // Call the method you want to test
        $response = $pokemon_rest->get_pokemon_list($request);

        // Check if the response is an instance of WP_REST_Response
        $this->assertInstanceOf(WP_REST_Response::class, $response);

        // Get the response data
        $data = $response->get_data();

        // Ensure the response contains the expected data
        $this->assertContains('Pikachu', $data);
    }

    public function test_get_pokemon_list_with_no_pokemon()
    {
        $clearer = new PokemonClearer();
        $clearer->clear_all_pokemons();

        // Ensure the response message when there are no Pokémon
        $pokemon_rest = new PokemonREST();
        $request = new WP_REST_Request('GET', '/' . $pokemon_rest::POKEMON_REST_EXPORT_ROUTE_MANY);
        $response = $pokemon_rest->get_pokemon_list($request);
        $data = $response->get_data();

        $this->assertCount(1, $data); // Expected 1 message
        $this->assertSame('No Pokémon data found', $data[0]['message']);
    }
}
