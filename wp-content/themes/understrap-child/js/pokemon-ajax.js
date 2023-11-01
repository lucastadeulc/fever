document.addEventListener('DOMContentLoaded', () => {
    const ajaxurl = pokemonData.ajax_url;
    const postID = pokemonData.post_id;
    const fetchButton = document.getElementById('fetch-pokedex-button');
    const pokedexOldResult = document.getElementById('pokedex-old-result');

    if (fetchButton) {
        fetchButton.addEventListener('click', () => {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: new URLSearchParams({
                    action: 'get_pokedex_number_old',
                    postId: postID
                }),
            })
            .then(response => response.json())
            .then(data => {
                pokedexOldResult.textContent = "Pokedex Number(Old): " + data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
