<?php

require '../vendor/autoload.php';
include_once '../config/db.php';

try {
    // Créer une instance de MongoDB Client
    $client = new MongoDB\Client($uri);

    // Accéder à la base de données et à la collection
    $db = $client->$dbName;
    $collection = $db->pokemons; // Remplace "pokemons" par le nom de ta collection

    // Exemple de requête pour récupérer tous les documents de la collection
    $cursor = $collection->find(); // `find()` récupère tous les documents

     // Parcourir et afficher les résultats
     echo "<h2>Liste des Pokémons :</h2>";

     foreach ($cursor as $document) {
        echo "<hr>"; // Séparateur pour chaque Pokémon

        // Utilisation d'un conteneur flex pour aligner l'image et le texte
        echo "<div style='display: flex; align-items: center; gap: 10px;'>";

        // Affichage de l'image
        echo "<img src='" . $document['sprites']['mini'] . "' alt='" . $document['name'] . "' style='width:50px;height:50px;'>";

        // Affichage des informations du Pokémon
        echo "<div>";
        echo "<strong>Nom :</strong> " . $document['name'] . "<br>";
        echo "<strong>Types :</strong> " . $document['types'][0] . " " . $document['types'][1] . "<br>";

        echo "</div>";

        echo "</div>";
        echo "<hr><br>"; // Séparateur après chaque Pokémon
    }

} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "\n";
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    echo "Erreur d'authentification : " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Une erreur s'est produite : " . $e->getMessage() . "\n";
}

?>
