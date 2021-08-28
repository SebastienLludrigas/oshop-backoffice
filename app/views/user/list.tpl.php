<div class="container my-4">
    <a href="<?= $router->generate('user-add'); ?>" class="btn btn-success float-right">Ajouter</a>
    <h2>Liste des utilisateurs</h2>
    <table class="table table-hover mt-4">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $currentUser) : ?>
                <tr>
                    <th scope="row"><?= $currentUser->getId(); ?></th>
                    <td><?= $currentUser->getLastname(); ?></td>
                    <td><?= $currentUser->getFirstname(); ?></td>
                    <td><?= $currentUser->getEmail(); ?></td>
                    <td><?= $currentUser->getRole(); ?></td>
                    <td>
                        <?= $currentUser->getStatus() == 1 ? '<i class="fa fa-check-circle" aria-hidden="true" title="Actif" style="color: green"></i>' : '<i class="fa fa-ban" aria-hidden="true" title="Bloqué" style="color: red"></i>'; ?>
                    </td>
                    <td>
                        <a href="<?= $router->generate('user-update', ['user_id' => $currentUser->getId()]) ?>" class="btn btn-sm btn-warning">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </a>
                        <!-- Example single danger button -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $router->generate('user-delete', ['product_id' => $currentUser->getId()]) ?>?token=<?= $token ?>">Oui, je veux supprimer</a>
                                <a class="dropdown-item" href="#" data-toggle="dropdown">Oups !</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>