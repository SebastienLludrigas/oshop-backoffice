<div class="container my-4">
    <a href="<?=$router->generate('category-list')?>" class="btn btn-success float-right">Retour</a>
    <h2>Modifier une catégorie</h2>

    <?php include __DIR__ . '/../partials/errorlist.tpl.php'; ?>

    <form action="" method="POST" class="mt-5">
        <div class="form-group">
            <label for="name">Nom</label>
            <input
                type="text"
                class="form-control"
                id="name" name="catname"
                placeholder="Nom de la catégorie"
                value="<?= $category->getName(); ?>">
        </div>
        <div class="form-group">
            <label for="subtitle">Sous-titre</label>
            <input
                type="text"
                class="form-control"
                id="subtitle"
                name="subtitle"
                placeholder="Sous-titre"
                aria-describedby="subtitleHelpBlock"
                value="<?= $category->getSubtitle(); ?>">
            <small id="subtitleHelpBlock" class="form-text text-muted">
                Sera affiché sur la page d'accueil comme bouton devant l'image
            </small>
        </div>
        <div class="form-group">
            <label for="picture">Image</label>
            <input
                type="text"
                class="form-control"
                id="picture"
                name="picture"
                placeholder="image jpg, gif, svg, png"
                aria-describedby="pictureHelpBlock"
                value="<?= $category->getPicture(); ?>">
            <small id="pictureHelpBlock" class="form-text text-muted">
                URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
            </small>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
    </form>
</div>