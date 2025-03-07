<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Pokémon</title>
    <style>
        .result-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .result-container img {
            width: 50px;
            height: 50px;
        }
    </style>
    <script>
        function searchPokemon() {
            const query = document.getElementById('search').value;
            if (query.length > 0) {
                fetch('search_pokemon.php?q=' + query)
                    .then(response => response.json())
                    .then(data => {
                        const results = document.getElementById('results');
                        results.innerHTML = ''; // Clear previous results

                        data.forEach(pokemon => {
                            const container = document.createElement('div');
                            container.className = 'result-container';

                            const img = document.createElement('img');
                            img.src = pokemon.sprites.mini;
                            img.alt = pokemon.name;

                            const info = document.createElement('div');
                            info.innerHTML = `<strong>Nom :</strong> ${pokemon.name}<br><strong>Types :</strong> ${pokemon.types.join(', ')}`;

                            container.appendChild(img);
                            container.appendChild(info);
                            results.appendChild(container);
                        });
                    });
            } else {
                document.getElementById('results').innerHTML = ''; // Clear results if query is empty
            }
        }
    </script>
</head>
<body>
    <h1>Recherche de Pokémon</h1>
    <input type="text" id="search" onkeyup="searchPokemon()" placeholder="Recherchez un Pokémon...">
    <div id="results"></div>
</body>
</html>
