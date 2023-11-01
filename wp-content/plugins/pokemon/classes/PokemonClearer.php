<?php
class PokemonClearer {
    public static function clear_all_pokemons() {
        global $wpdb;
        $post_table = $wpdb->prefix . 'posts';

        // Delete all Pokémon posts
        $wpdb->query("DELETE FROM $post_table WHERE post_type = 'pokemon'");
    }
}

