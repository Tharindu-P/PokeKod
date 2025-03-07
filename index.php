<?php
include_once 'vendor/autoload.php';
$title = "PokeKoD National";
include_once 'config/db.php';
include_once 'includes/header.php';
?>

<div class="pokedex">
    <div class="left-panel">
        <div class="top">
            <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <path fill="none" stroke-width="0.5%" stroke="black" d="M 0,20 H 45 C 60,20 60,10 75,10 H 100" />
            </svg>
        </div>
        <div class="circle">
            <img src="assets/img/image.png" alt="Logo">
        </div>
        <div class="title">PokeKoD National</div>
        <div class="hint" onclick="openModal()">
            <p>?</p>
        </div>
        <!-- La fenêtre modale -->
        <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p><strong>Bienvenue dans PokeKoD National !</strong></p>
            <p>Voici un guide pour t'aider à explorer toutes les fonctionnalités de l'application.</p>
            <p><strong>PokeKoD National</strong> permet de rechercher des Pokémon en utilisant différents critères. Voici les fonctionnalités principales et des exemples pour chacune d'elles :</p>

            <ul>
                <li><strong>Affichage du Pokedex</strong>: Tu peux consulter un Pokémon en cliquant sur son nom ou en effectuant une recherche. L'application te permet de voir sa version normale ou shiny.
                    <ul>
                        <li><strong>Exemple :</strong> Si tu recherches <em>Pikachu</em>, tu verras l'image de Pikachu et tu pourras choisir entre la version normale ou shiny.</li>
                    </ul>
                </li>

                <li><strong>Recherche Avancée</strong>: Utilise différents critères pour trouver des Pokémon spécifiques.
                    <ul>
                        <li><strong>Recherche Globale</strong>: Recherche combinée par plusieurs critères comme le nom, le type, ou l'ID.</li>
                        <li><strong>Recherche par ID</strong>: Recherche un Pokémon par son numéro dans le Pokédex.</li>
                        <li><strong>Exemple :</strong> Si tu recherches l'ID <em>25</em>, tu trouveras <em>Pikachu</em> (Pokedex #25).</li>

                        <li><strong>Recherche par Nom</strong>: Recherche un Pokémon par son nom.</li>
                        <li><strong>Exemple :</strong> Si tu tapes <em>Bulbizarre</em>, tu trouveras <em>Bulbizarre</em> (Bulbasaur en anglais).</li>

                        <li><strong>Recherche par Type</strong>: Recherche un Pokémon par son type (ou plusieurs types).</li>
                        <li><strong>Exemple :</strong> Si tu recherches <em>Feu</em>, tu trouveras des Pokémon comme <em>Salamèche</em> ou <em>Dracaufeu</em>. Tu peux aussi chercher plusieurs types en les séparant par des virgules, comme <em>Feu, Vol</em> pour trouver des Pokémon de type feu et vol.</li>

                        <li><strong>Recherche par Poids</strong>: Recherche un Pokémon par son poids ou une plage de poids.</li>
                        <li><strong>Exemple :</strong> Si tu recherches <em>5-10</em> (5 kg à 10 kg), tu trouveras des Pokémon comme <em>Rattata</em> ou <em>Magmar</em>.</li>

                        <li><strong>Recherche par Taille</strong>: Recherche un Pokémon par sa taille ou une plage de tailles.</li>
                        <li><strong>Exemple :</strong> Si tu recherches <em>0.5-1.0</em> (entre 0,5 et 1,0 mètre), tu trouveras des Pokémon comme <em>Bulbizarre</em> ou <em>Leveinard</em>.</li>
                    </ul>
                </li>

                <li><strong>Version Shiny ou Normale</strong>: Tu peux basculer entre la version normale et shiny d'un Pokémon.
                    <ul>
                        <li><strong>Exemple :</strong> Si tu recherches <em>Pikachu</em>, tu peux choisir de voir son sprite normal ou son sprite shiny (version rare et colorée).</li>
                    </ul>
                </li>

                <li><strong>Suggestions de Recherche</strong>: Au fur et à mesure que tu tapes, des suggestions de Pokémon ou de types s'afficheront sous le champ de recherche pour t'aider à affiner ta requête.
                    <ul>
                        <li><strong>Exemple :</strong> Si tu commences à taper <em>Pi</em>, tu verras des suggestions comme <em>Papilusion</em>, <em>Pickachu</em>, etc.</li>
                    </ul>
                </li>
            </ul>

            <p>Amuse-toi bien à explorer l'univers des Pokémon avec <strong>PokeKoD National</strong> !</p>
        </div>
    </div>

        <div id="left-rectangle" class="main-display"></div>
        <div class="sprite-buttons">
            <button type="button" class="btn-normal" onclick="showNormal()">Version Normale</button>
            <button type="button" class="btn-shiny" onclick="showShiny()">Version Shiny</button>
        </div>
    </div>
    <div class="middle-part">
        <div class="first"></div>
        <div class="second"></div>
        <div class="third"></div>
        <div class="fourth"></div>
    </div>
    <div class="right-panel">
        <div id="right-rectangle" class="secondary-display">
            <p>Informations Pokémon</p>
        </div>

        <!-- Ajout des éléments déplacés ici -->
        <div class="text-box">
            <div class="search-buttons">
                <button type="button" id="search-global" onclick="setSearchGlobal('global')">Recherche Global</button>
            </div>
            <div class="search-buttons">
                <button type="button" id="search-id" onclick="setSearchType('id')">ID</button>
                <button type="button" id="search-name" onclick="setSearchType('name')">Nom</button>
                <button type="button" id="search-type" onclick="setSearchType('type')">Type</button>
                <button type="button" id="search-weight" onclick="setSearchType('weight')">Poids</button>
                <button type="button" id="search-height" onclick="setSearchType('height')">Taille</button>
            </div>
        </div>

        <!-- Formulaire de recherche -->
        <form method="GET" action="search.php" class="search-form" onsubmit="handleSubmit(event)">
            <input type="text" name="query" id="search-query" placeholder="Entrez votre recherche..." autocomplete="off">
            <input type="hidden" name="type" id="search-type-input" value="name">
            <div id="suggestions" class="suggestions-list"></div>
        </form>
    </div>
</div>

<script src="script.js?v=<?= time(); ?>"></script>

<?php include_once 'includes/footer.php';?>
