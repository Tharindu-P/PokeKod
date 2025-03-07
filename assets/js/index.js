let searchType = 'global'; // Par défaut, recherche par nom
let selectedIndex = 0; // Index de la suggestion actuellement sélectionnée
let currentPokemon = null; // Variable pour stocker le Pokémon sélectionné


window.onload = function() {
    setSearchGlobal('global');
};


// Fonction pour définir le type de recherche (par ID, nom, type, etc.)
function setSearchType(type) {
    searchType = type;
    document.getElementById('search-type-input').value = type;
    document.getElementById('search-query').placeholder = `Rechercher par ${type}...`;
    document.getElementById('suggestions').innerHTML = ''; // Effacer les suggestions précédentes
}

function setSearchGlobal(type) {
    searchType = type; // Définir le type de recherche sur global
    document.getElementById('search-type-input').value = type; // Mettre à jour le champ caché
    document.getElementById('search-query').placeholder = 'Rechercher par tout...'; // Modifier le placeholder
    document.getElementById('suggestions').innerHTML = ''; // Effacer les suggestions précédentes
}

// Quand l'utilisateur clique sur le bouton "Recherche Global"
document.getElementById('search-global').addEventListener('click', function() {
    setSearchGlobal('global');
});


// Gestion de l'événement `input` sur la barre de saisie
document.getElementById('search-query').addEventListener('input', function() {
    const query = this.value.trim();
    if (query.length >= 1) {
        fetchSuggestions(query); // Lancer la recherche uniquement si la requête est non vide
    } else {
        // Si la recherche est vide, effacer les suggestions
        document.getElementById('suggestions').innerHTML = '';
    }
});

// Fonction pour récupérer les suggestions depuis `search.php`
async function fetchSuggestions(query) {
    const suggestionsList = document.getElementById('suggestions');
    suggestionsList.innerHTML = ''; // Effacer les suggestions précédentes

    try {
        // Appel à l'API de recherche pour obtenir les suggestions en fonction du type et de la requête
        const response = await fetch(`search.php?query=${encodeURIComponent(query)}&type=${searchType}`);
        const data = await response.json();

        if (data.length === 0) {
            suggestionsList.innerHTML = '<div>Aucun résultat trouvé</div>';
            return;
        }

        // Affichage des suggestions de Pokémon
        data.forEach(pokemon => {
            const suggestion = document.createElement('div');
            suggestion.classList.add('suggestion-item');
            suggestion.innerHTML = `
                <img src="${pokemon.mini}" alt="${pokemon.name}" style="width:30px; height:30px; margin-right: 10px;">
                <strong>${pokemon.name}</strong>
            `;

            // Action lors du clic sur une suggestion
            suggestion.onclick = () => selectPokemon(pokemon);

            suggestionsList.appendChild(suggestion);
        });

        // Sélectionner le premier élément par défaut
        setSelectedIndex(0);
    } catch (error) {
        console.error('Erreur lors de la recherche :', error);
        suggestionsList.innerHTML = '<div>Erreur lors de la recherche, veuillez réessayer.</div>';
    }
}

// Fonction pour afficher les informations du Pokémon sélectionné
function selectPokemon(pokemon) {
    currentPokemon = pokemon; // Stocker le Pokémon sélectionné

    // Mettre à jour le sprite au moment de la sélection (en fonction de l'état actuel)
    updateSprite(pokemon);

    // Mise à jour de l'affichage des informations à droite
    document.getElementById('right-rectangle').innerHTML = `
        <h2>${pokemon.name} (${pokemon.id})</h2>
        <p><strong>Types:</strong> ${pokemon.types.join(', ')}</p>
        <p><strong>Poids:</strong> ${pokemon.poids} kg</p>
        <p><strong>Taille:</strong> ${pokemon.taille} m</p>
    `;

    // Effacer les suggestions après la sélection
    document.getElementById('suggestions').innerHTML = '';

    // Effacer le champ de recherche
    const searchInput = document.getElementById('search-query');
    searchInput.value = '';  // Vider la barre de recherche

    // Re-focus sur le champ de saisie (optionnel, mais pratique pour une nouvelle recherche)
    searchInput.focus();
}

// Gestion de l'événement `keydown` sur la barre de saisie
document.getElementById('search-query').addEventListener('keydown', function(event) {
    const suggestions = document.querySelectorAll('.suggestion-item');

    // Si la touche flèche haut est pressée
    if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (selectedIndex > 0) {
            selectedIndex--;
        } else {
            selectedIndex = suggestions.length - 1; // Si on est en haut, on revient en bas
        }
        setSelectedIndex(selectedIndex);
    }

    // Si la touche flèche bas est pressée
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (selectedIndex < suggestions.length - 1) {
            selectedIndex++;
        } else {
            selectedIndex = 0; // Si on est en bas, on revient en haut
        }
        setSelectedIndex(selectedIndex);
    }

    // Si la touche "Enter" est pressée
    if (event.key === 'Enter') {
        event.preventDefault(); // Empêcher le comportement par défaut (soumettre le formulaire)
        const selectedSuggestion = suggestions[selectedIndex];
        if (selectedSuggestion) {
            selectedSuggestion.click(); // Simuler un clic sur l'élément sélectionné
        }
    }
});

// Fonction pour mettre à jour la classe CSS pour la suggestion sélectionnée
function setSelectedIndex(index) {
    const suggestions = document.querySelectorAll('.suggestion-item');
    suggestions.forEach(suggestion => suggestion.classList.remove('selected')); // Enlève la sélection de toutes les suggestions
    if (index >= 0 && index < suggestions.length) {
        suggestions[index].classList.add('selected'); // Ajoute la classe 'selected' à l'élément sélectionné
        suggestions[index].scrollIntoView({
            behavior: 'smooth', // Défilement fluide
            block: 'nearest' // Pour éviter que l'élément dépasse de l'écran
        });
    }
}

// Empêcher la soumission du formulaire lors de l'envoi
document.querySelector('.search-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêcher la soumission du formulaire
});

// Variable pour stocker l'état du sprite actuel
let currentSpriteType = 'normal'; // Par défaut, afficher le sprite normal

// Fonction pour afficher la version normale du sprite
function showNormal() {
    if (currentPokemon) {
        currentSpriteType = 'normal'; // Changer l'état à 'normal'
        updateSprite(currentPokemon);  // Mettre à jour l'image avec le sprite normal
    }
}

// Fonction pour afficher la version shiny du sprite
function showShiny() {
    if (currentPokemon) {
        currentSpriteType = 'shiny'; // Changer l'état à 'shiny'
        updateSprite(currentPokemon);  // Mettre à jour l'image avec le sprite shiny
    }
}

// Fonction pour mettre à jour l'affichage du sprite
function updateSprite(pokemon) {
    let spriteUrl = pokemon.sprite; // Par défaut, sprite normal

    // Si la version shiny est demandée, mettre à jour avec le sprite shiny (on suppose que 'shiny_sprite' est un attribut du Pokémon)
    if (currentSpriteType === 'shiny' && pokemon.shiny) {
        spriteUrl = pokemon.shiny; // Utiliser l'URL du sprite shiny si disponible
    }

    // Mise à jour de l'affichage du sprite
    document.getElementById('left-rectangle').innerHTML = `
        <img src="${spriteUrl}" alt="${pokemon.name}" style="width: 100%; height: auto;">
    `;
}

// Fonction pour ouvrir la popup
function openModal() {
    const modal = document.getElementById("myModal");
    modal.style.display = "block";
}

// Fonction pour fermer la popup
function closeModal() {
    const modal = document.getElementById("myModal");
    modal.style.display = "none";
}

// Fermer la fenêtre modale si l'utilisateur clique en dehors de la fenêtre modale
window.onclick = function(event) {
    const modal = document.getElementById("myModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
