<?php
/*
Template Name: Single Pokémon Template
*/
get_header();
?>

<div class="container">
    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="pokemon-image">
                <?php the_post_thumbnail('large', ['class' => 'img-fluid w-100']); ?>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="pokemon-details">
                <?php
                $pokemon_name = ucfirst(get_the_title());
                $pokemon_description = get_post_meta(get_the_ID(), 'pokemon_description', true);
                $primary_type = get_post_meta(get_the_ID(), 'pokemon_primary_type', true);
                $secondary_type = get_post_meta(get_the_ID(), 'pokemon_secondary_type', true);
                $pokemon_weight = get_post_meta(get_the_ID(), 'pokemon_weight', true);
                $pokedex_number_old = get_post_meta(get_the_ID(), 'pokedex_number_old', true);
                $pokedex_number_recent = get_post_meta(get_the_ID(), 'pokedex_number_recent', true);
                $pokemon_attacks = get_post_meta(get_the_ID(), 'pokemon_attacks', true);
                ?>

                <h1 class='mb-3'><?php echo $pokemon_name; ?></h1>
                <p>Description: <?php echo $pokemon_description; ?></p>
                <p>Primary Type: <?php echo $primary_type; ?></p>
                <p>Secondary Type: <?php echo $secondary_type; ?></p>
                <p>Weight: <?php echo $pokemon_weight; ?> kg</p>
                <p>Pokedex Number (Old): <?php echo $pokedex_number_old; ?></p>
                <p>Pokedex Number (Recent): <?php echo $pokedex_number_recent; ?></p>
            </div>
            <?php if ($pokemon_attacks && is_array($pokemon_attacks)): ?>
                <div class="pokemon-attacks mt-5">
                    <h2>Attacks:</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Movement Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($pokemon_attacks as $attack) {
                                echo '<tr>';
                                echo '<td>' . $attack['name'] . '</td>';
                                echo '<td>' . $attack['description'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
