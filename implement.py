import pymongo
import requests
import json

config_db = json.load(open('config/db.json', 'r', encoding="utf-8")) # Ouvre le fichier contenant les identifiants de la base de données
connection_uri = f'mongodb://{config_db["user"]}:{config_db["password"]}@{config_db["host"]}:{config_db["port"]}/{config_db["dbName"]}'
database = pymongo.MongoClient(connection_uri)[config_db["dbName"]] # Se connecte à la base de données et récupère la base de données (cf. dbName)

headers = {
    "User-Agent": "RobotPokemon",
    'Content-type': 'application/json'
}

tyradex = requests.get("https://tyradex.vercel.app/api/v1/pokemon?offset=0&limit=2000", headers=headers) # Récupère les Pokémons

database.create_collection('pokemons') if 'pokemons' not in database.list_collection_names() else None # Crée la collection si elle n'existe pas
pokemons = database['pokemons'] # Récupère la collection des Pokémons
pokemons.delete_many({}) # Réinitialise la collection

if tyradex.status_code == 200:
    data_tyradex = tyradex.json()
else:
    print("Une erreur s'est produite lors de la récupération des données (Tyradex)")
    exit(-1)

for poke in data_tyradex[1:]: # Dématérialisation du JSON
    evo = {}
    if poke['evolution'] is not None:
        evo['pre'] = [t['pokedex_id'] for t in poke['evolution']['pre']] if poke['evolution']['pre'] is not None else None
        evo['next'] = [t['pokedex_id'] for t in poke['evolution']['next']] if poke['evolution']['next'] is not None else None
    else:
        evo = None
    pokemon = {
        "pokedexId": poke['pokedex_id'], # Identifiant du Pokémon dans le Pokédex national
        "generation": poke['generation'], # La génération du Pokémon (1=Kanto, 2=Johto, 3=Hoenn, 4=Sinnoh, 5=Unys, 6=Kalos, 7=Alola, 8=Galar, 9=Paldea)
        "name": poke['name']['fr'],
        "types": [t['name'] for t in poke['types']],
        "sprites": {
            "mini": f"https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{poke['pokedex_id']}.png",
            "artwork": f"https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{poke['pokedex_id']}.png",
            "artwork-shiny": f"https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/shiny/{poke['pokedex_id']}.png"
        },
        "evolutions": evo,
        "poids": float(poke['weight'].split(' ')[0].replace(',', '.')), # Poids du Pokémon
        "taille": float(poke['height'].split(' ')[0].replace(',', '.')), # Taille du Pokémon
    }

    pokemons.insert_one(pokemon) # Insertion des Pokémons