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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allUsers as $currentUser): ?>
                <tr>
                    <th scope="row"><?= $currentUser->getId(); ?></th>
                    <td><?= $currentUser->getFirstname(); ?></td>
                    <td><?= $currentUser->getLastname(); ?></td>
                    <td><?= $currentUser->getEmail(); ?></td>
                    <td><?= $currentUser->getRole(); ?></td>
                    <td><?= $currentUser->getStatus() == 1 ? '<i class="fa fa-check-circle" aria-hidden="true" title="Actif"></i>' : '<i class="fa fa-ban" aria-hidden="true" title="Bloqué"></i>'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>