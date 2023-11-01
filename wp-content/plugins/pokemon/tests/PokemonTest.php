<?php

/**
 * Class PokemonTest
 */
class PokemonTest extends WP_UnitTestCase
{
    /**
     * Test creating a new Pokémon and updating its properties.
     */
    function test_create_or_update_pokemon()
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

        // Check if the post was created or updated
        $this->assertGreaterThan(0, $post_id);

        // Verify the Pokémon properties
        $this->assertEquals('Electric', get_post_meta($post_id, 'pokemon_primary_type', true));
        $this->assertEquals('', get_post_meta($post_id, 'pokemon_secondary_type', true)); // No secondary type
        $this->assertEquals(60, get_post_meta($post_id, 'pokemon_weight', true));
        $this->assertEquals('25', get_post_meta($post_id, 'pokedex_number_old', true));
        $this->assertEquals(25, get_post_meta($post_id, 'pokedex_number_recent', true));
        $this->assertEquals('An electric Pokémon.', get_post_meta($post_id, 'pokemon_description', true));

        // Check if the Pokémon has an attack
        $attacks = get_post_meta($post_id, 'pokemon_attacks', true);
        $this->assertContains('Thunderbolt', $attacks);
    }
}
