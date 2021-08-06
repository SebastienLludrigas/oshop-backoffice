# Différents rappels...

## Créer une nouvelle "url"

1. Créer la route dans `index.php`
   1. `$router->map(...)`
2. Créer le controller si besoin
3. Créer la méthode dans le controller
4. Créer la vue qui reprend le nom du controller en dossier et le nom de la méthode en nom de fichier
   1. `controllername/methodname.tpl.php`

## Créer un modele

1. Créer une classe qui porte le nom de la table ciblée
2. Créer une propriété pour chaque champ de la table ciblée