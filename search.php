<?php
// Connexion à la base de données MongoDB
require 'config/db.php';
require 'vendor/autoload.php';

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

try {
    $client = new MongoDB\Client($uri);
    $db = $client->$dbName;
    $collection = $db->pokemons;

    // Vérifier les paramètres passés et définir des valeurs par défaut
    $query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; // Valeur par défaut : chaîne vide
    $type = isset($_GET['type']) ? $_GET['type'] : 'global'; // Par défaut : recherche globale

    $filter = []; // Filtre de base pour la requête MongoDB

    // Déterminer le champ de recherche en fonction du type
    switch ($type) {
        case 'id':
            $filter = [
                '$expr' => [
                    '$regexMatch' => [
                        'input' => ['$toString' => '$pokedexId'],
                        'regex' => $query,
                    ]
                ]
            ];
            break;

        case 'name':
            $filter = ['name' => new MongoDB\BSON\Regex($query, 'i')];
            break;

            case 'type':
                if (strpos($query, ',') !== false) {
                    $types = explode(',', $query); // Divise les types par la virgule
                    $filter = [
                        'types' => [
                            '$all' => [
                                new MongoDB\BSON\Regex($types[0], 'i'), 
                                new MongoDB\BSON\Regex($types[1], 'i') 
                            ]
                        ]
                    ];
                } else {
                    $filter = ['types' => new MongoDB\BSON\Regex($query, 'i')];
                }
                break;

                case 'weight':
                    $query = htmlspecialchars_decode($query);
                    if (preg_match("/^(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)$/", $query, $matches)) {
                        error_log("Matches : " . json_encode($matches));
                        $filter = [
                            'poids' => [
                                '$gte' => (float)$matches[1],
                                '$lte' => (float)$matches[2],
                            ],
                        ];
                    } elseif (preg_match("/^[<>]=?\d+(\.\d+)?$/", $query, $matches)) {
                        error_log("Matches : " . json_encode($matches));
                        $operator = strpos($query, '<') !== false ? '$lt' : '$gt';
                        $filter = [
                            'poids' => [
                                $operator => (float)str_replace(['<', '>'], '', $query),
                            ],
                        ];
                    } elseif (is_numeric($query)) {
                        error_log("Matches : " . json_encode($matches));
                        $filter = ['poids' => (float)$query];
                    } else {
                        echo json_encode([]);
                        exit;
                    }
                    break;
    
                case 'height':
                    $query = htmlspecialchars_decode($query);
                    if (preg_match("/^(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)$/", $query, $matches)) {
                        $filter = [
                            'taille' => [
                                '$gte' => (float)$matches[1],
                                '$lte' => (float)$matches[2],
                            ],
                        ];
                    } elseif (preg_match("/^[<>]=?\d+(\.\d+)?$/", $query, $matches)) {
                        error_log("Matches : " . json_encode($matches));
                        $operator = strpos($query, '<') !== false ? '$lt' : '$gt';
                        $filter = [
                            'taille' => [
                                $operator => (float)str_replace(['<', '>'], '', $query),
                            ],
                        ];
                    } elseif (is_numeric($query)) {
                        $filter = ['taille' => (float)$query];
                    
                    } else {
                        echo json_encode([]);
                        exit;
                    }
                    break;

            // Recherche globale
        case 'global':
            $filter = [
                '$or' => [
                    // Recherche par nom
                    ['name' => new MongoDB\BSON\Regex($query, 'i')],

                    // Recherche par types (prend en compte plusieurs types)
                    [
                        'types' => [
                            '$all' => array_map(function($type) {
                                return new MongoDB\BSON\Regex($type, 'i'); 
                            }, array_map('trim', explode(',', $query))) // Découpe la chaîne de recherche par virgule
                        ]
                    ],

                    // Recherche par ID
                    [
                        '$expr' => [
                            '$regexMatch' => [
                                'input' => ['$toString' => '$pokedexId'],
                                'regex' => $query,
                            ]
                        ]
                    ],
                ]
            ];
            break;

        default:
            echo json_encode([]);
            exit;
    }

    // affichage des logs serveur pour debboguer
    error_log("Filtre utilisé : " . json_encode($filter));

    // Effectuer la recherche dans MongoDB
    $results = $collection->find($filter);

    // Construire la liste des résultats
    $pokemonList = [];
    foreach ($results as $pokemon) {
        $pokemonList[] = [
            'name' => $pokemon['name'],
            'id' => $pokemon['pokedexId'],
            'types' => $pokemon['types'],
            'mini' => $pokemon['sprites']['mini'],
            'sprite' => $pokemon['sprites']['artwork'],
            'shiny' => $pokemon['sprites']['artwork-shiny'],
            'poids' => $pokemon['poids'] ?? null,
            'taille' => $pokemon['taille'] ?? null
        ];
    }

    // Retourner les résultats sous forme de JSON
    echo json_encode($pokemonList);
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
