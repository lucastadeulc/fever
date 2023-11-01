<!-- archive-pokemon.php -->
<?php
get_header();
?>

<div class="container">
    <div class="row">
        <div class="col-24">
            <h1 class="my-3">Pokémon Archive</h1>

            <select id="pokemon-types" class="form-select mb-3">
                <option value="">All</option>
            </select>

            <div class="row">
                <div class="pokemon-container col-24"></div>
            </div>

            <?php echo paginate_links(); ?>
        </div>
    </div>
</div>

<?php
get_footer();
