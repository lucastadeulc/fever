Step by step:  
Created new site using local by flywheel
Created plugin using wpcli scaffold
Cloned git@github.com:understrap/understrap-child.git
Started plugin coding: pokemonplugin, pokemonapi, pokemon classes
Created single pokemon template
Created archive pokemon, blank for now
Created random url in functions
Created generate url in functions
Created rest api endpoint in functions
Migrated rest api to PokemonREST
Activation and deactivation functions for plugin
Overall end 2 end tests part 1
Created pokemonApi types request
Created pokemonRest types endpoint
Created pokemonRest get_pokemon_list_with_details to be used by typescript
Added type param to get_pokemon_list_with_details, for filtering
Started typescript filter by loading pokemons to page using get_pokemon_list_with_details
Added typescript populate on types select of archive
Added event listener for when select changes, rest request to filter by type
Refactor to add some functions for updatePokemonContainer and createPokemonCard in Javascript
Refactor on archive to enqueue script and in typescript to load site_url from ajax
Fixed row inside rows problems, micro changes on archive and single styles
Overall end 2 end tests part 2
Refactor on consts and routes
Enhanced the filter in the REST API to also filter by secondary type
Added ajax button in single pokemon
Fixed problem with old pokedex number, retrieved correctly now
Tried to improve the attacks insertion, by using multiple requests, but failed to


Problems and decisions by topic:
2- I created every pokemon property as post_meta to facilitate usage and retrieve,
I do know that primary and secondary type could have been added as a taxonomy, 
since they are group properties, but I didn't judge this as necessary for this challenge.
2- When I started to populate the attacks, I noticed the very high quantity of attacks per pokemon and reusability problem.
Perhaps changing to a taxonomy or CPT could solve the reusability problem, 
but I think the problem of time comsuption on insertion of a pokemon would still occur, 
since you would need to check/find in the database each attack. 
2- Update/insert attacks via wp-admin: there is no easy way to insert/edit those in "vanilla" custom fields since they are an array.
I did add some code to turn those into a table so user could see it in wp-admin, but I removed because I found it unecessary, 
since Pokemons get inserted via the plugin.
I still retrieve the attacks from the PokeAPI and insert in database, they are showing in single pokemon template too.
But if you are trying to manually insert/edit those, I believe Wordpress doesn't provide a way yet.
9- About DAPI: First I would need to better organize names/api/fields/+ in a way we can create a layer of abstraction above it
Lets call that layer as Trainer(abstraction), Trainer would have a interface injection of Monster(implementation),
Monster would have the concrete implementations: Pokémon and Digimon plugin classes, 
with that I believe its easier to add more Monsters.
10- About heavy traffic: Implement attacks reusability by relationship of pokemon x attack(1>N) to see if reduces db queries,
Database optimization: indexes, maybe create a field to contain all pokemon_data, facilitating retrieve
Caching
WP Object Cache API to cache query results
2- Tried to improve the attack insertion, which is making 1 request to the API per attack, to grab the description.
Unfortunately no success to make several requests per once.
For now I left a POKEMON_PLUGIN_MAX_ATTACKS_PER_POKEMON with value of 5.


Missing:
pagination thinking about scalability, posts_per_page = 6
tests

Tests:
Have wget and svn installed
Create a new database(wp_test)
cd into plugin folder using shell
bin/install-wp-tests.sh  wp_test  root root localhost:10066 latest true


Reproduce:
Initialize your Wordpress site as you prefer, using PHP 8+
Clone into your Wordpress folder
Activate plugin and theme
- Plugin on activation will automatically add 3 pokemons
Ready to test!
