interface Pokemon {
    id: number;
    name: string;
    primary_type: string;
    secondary_type: string;
    link: string;
    photo: string;
}

interface PokemonType {
    name: string;
}

function createPokemonCard(pokemon: Pokemon): string {
    return `
      <a href="${pokemon.link}" class="pokemon-card col-24 col-md-4 mb-3 text-decoration-none">
        <div class="card h-100">
            <img src="${pokemon.photo}" class="card-img-top" alt="${pokemon.name}">
            <div class="card-body">
            <h2 class="card-title text-capitalize">${pokemon.name}</h2>
            <p class="card-text">Primary Type: ${pokemon.primary_type}</p>
            ${pokemon.secondary_type ? `<p class="card-text">Secondary Type: ${pokemon.secondary_type}</p>` : ''}
            </div>
        </div>
      </a>`;
}

function updatePokemonContainer(apiUrl: string, container: Element) {
    fetch(apiUrl)
        .then((response) => response.json())
        .then((data: Pokemon[]) => {
            if (data.length > 0) {
                let html = '';
                data.forEach((pokemon, index) => {
                    if (index % 3 === 0) {
                        html += '<div class="row">';
                    }
                    html += createPokemonCard(pokemon);

                    if (index % 3 === 2 || index === data.length - 1) {
                        html += '</div>';
                    }
                });

                container.innerHTML = html;
            } else {
                container.innerHTML = 'No Pokémon data found.';
            }
        })
        .catch((error) => {
            console.error('Error fetching data:', error);
        });
}

// Select populate
declare const pokemonData: any;
document.addEventListener('DOMContentLoaded', () => {
    const siteURL = pokemonData.siteURL; 
    const route = pokemonData.route; 
    const detailed = pokemonData.detailed; 
    const types = pokemonData.types; 
    const typeFilter = document.getElementById('pokemon-types') as HTMLSelectElement;
    const pokemonContainer = document.querySelector('.pokemon-container');
    const apiUrl = `${siteURL}${route}${detailed}`;
    const typesApiUrl = `${siteURL}${route}${types}`;

    if (typeFilter && pokemonContainer) {
        // Start the page populated
        updatePokemonContainer(apiUrl, pokemonContainer);

        // Type filter listener with update content
        typeFilter.addEventListener('change', () => {
            const selectedType = typeFilter.value;
            updatePokemonContainer(apiUrl + (selectedType ? '/' + selectedType : ''), pokemonContainer);
        });

        // Populate the 'types' select
        fetch(typesApiUrl)
            .then((response) => response.json())
            .then((data: PokemonType[]) => {
                if (data.length > 0) {
                    data.forEach((type) => {
                        const option = document.createElement('option');
                        option.value = type.name;
                        option.textContent = type.name;
                        typeFilter.appendChild(option);
                    });
                } else {
                    typeFilter.innerHTML = 'No Pokémon types found.';
                }
            })
            .catch((error) => {
                console.error('Error fetching Pokémon types:', error);
            });
    }
});
