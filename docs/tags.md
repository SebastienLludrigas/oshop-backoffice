# Ajout des tags dans notre model Product

## Etat des lieux

> Actuellement pour récuperer les informations d'un produit je fait:
 
```sql
## Récuperer les informations du produit
SELECT * FROM product WHERE id = 1;
```

Problème: le tag n'apparait pas dans la table produit: ceux-ci sont
géré dans une table de liaison (= table pivot)

Pour récupérer nos tags il va donc faloir réaliser deux requetes supplémentaire:

```sql
## Récuperer les id des tags associé à mon produit
SELECT tag_id FROM product_has_tag WHERE product_id = 1;
```
 
```sql
## Récuperer les tags dont l'id est 1 & 7 (issu du résultat de la requete précédente)
SELECT * FROM tag WHERE id in (1, 7);
```

## Optimisation de notre récupération

Il est possible en SQL, de réaliser des sous-requête. L'idée est "simple"
une partie de ma requete necessite l'execution d'une autre requete pour fonctionner:

```sql
# Mise en place de la même procédure avec moins de requetes
SELECT *
FROM tag
WHERE id in (SELECT tag_id FROM product_has_tag WHERE product_id = 1);
```

Ici, notre requête `SELECT tag_id FROM product_has_tag WHERE product_id = 1` est executée en premier, MySQL, stock le résultat pour ensuite executer la requête principale.

Défaut: il y a toujours 2 requêtes à executer.

## Amélioration avec une jointure

Il est possible en SQL d'expliciter une relation entre des tables via l'utilisation du mot clé `INNER JOIN`.

```sql
# Mise en place de la solution utilisant les jointures
SELECT tag.* FROM tag
INNER JOIN product_has_tag
ON product_has_tag.tag_id = tag.id
WHERE product_has_tag.product_id = 1;
```

Ici nous indiquons à MySQL qu'il est necessaire de faire correspondre à notre requête initiale sur `tag`, la table `product_has_tag`.  
Grâce au mot clé `ON`, nous venons détailler les champs à mettre en relation: `product_has_tag.tag_id = tag.id`  
Pour finir le `WHERE` me permet de filtrer le résultat: je ne souhaite pas TOUT les tags mais uniquement ceux qui sont relié au produit dont l'id est 1.
