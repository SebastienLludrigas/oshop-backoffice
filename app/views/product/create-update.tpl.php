<div class="container my-4">
    <a href="<?=$router->generate('product-list')?>" class="btn btn-success float-right">Retour</a>
    <h2><?= isset($mode) && $mode === 'create' ? 'Ajouter' : 'Modifier'; ?> un produit</h2>

    <?php include __DIR__ . '/../partials/errorlist.tpl.php'; ?>

    <form action="" method="POST" class="mt-5">
        <div class="form-group">
            <label for="name">Nom</label>
            <input
                type="text"
                class="form-control"
                id="name"
                placeholder="Nom du produit"
                name="name"
                value="<?= isset($product) ? $product->getName() : ''; ?>"
                >
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input
                type="text"
                class="form-control"
                id="description"
                placeholder="Description"
                aria-describedby="subtitleHelpBlock"
                name="description"
                value="<?= isset($product) ? $product->getDescription() : ''; ?>"
                >
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Sera affiché sur la page d'accueil comme bouton devant l'image
            </small>
        </div>
        <div class="form-group">
            <label for="price">Prix</label>
            <input
                type="text"
                class="form-control"
                id="price"
                placeholder="Prix du produit"
                aria-describedby="subtitleHelpBlock"
                name="price"
                value="<?= isset($product) ? $product->getPrice() : ''; ?>"
                >
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Prix de l'article
            </small>
        </div>
        <div class="form-group">
            <label for="picture">Image</label>
            <input
                type="text"
                class="form-control"
                id="picture"
                placeholder="image jpg, gif, svg, png"
                aria-describedby="pictureHelpBlock"
                name="picture"
                value="<?= isset($product) ? $product->getPicture() : ''; ?>"
                >
            <small id="pictureHelpBlock" class="form-text text-muted">
                URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
            </small>
        </div>

        <div class="form-group">
            <label for="brandid">Marque</label>
            <select class="form-control" id="brandid" name="brandid">
                <option value="1">oCirage</option>
                <option value="2">BOOTstrap</option>
                <option value="3">Talonette</option>
                <option value="4">Shossures</option>
                <option value="5">O'shoes</option>
                <option value="6">Pattes d'eph</option>
                <option value="7">PHPieds</option>
                <option value="8">oPompes</option>
            </select>
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Marque du produit
            </small>
        </div>

        <div class="form-group">
            <label for="categoryid">Catégorie</label>
            <select class="form-control" id="categoryid" name="categoryid">
                <option value="1">Détente</option>
                <option value="2">Au travail</option>
                <option value="3">Cérémonie</option>
                <option value="4">Sortir</option>
                <option value="5">Vintage</option>
                <option value="6">Piscine et bains</option>
                <option value="7">Sport</option>
                <option value="8">Courir</option>
            </select>
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Catégorie du produit
            </small>
        </div>

        <div class="form-group">
            <label for="typeid">Type</label>
            <select class="form-control" id="typeid" name="typeid">
                <option value="1">Chaussures de ville</option>
                <option value="2">Chaussures de sport</option>
                <option value="3">Tongs</option>
                <option value="4">Chaussures ouvertes </option>
                <option value="5">Talons éguilles</option>
                <option value="6">Talons</option>
                <option value="7">Pantoufles</option>
                <option value="8">Chaussons</option>
            </select>
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Type du produit
            </small>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="1">Disponible</option>
                <option value="2">Indisponible</option>
            </select>
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Statut du produit
            </small>
        </div>

        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>