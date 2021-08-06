<div class="container my-4">

    <h2>Connection</h2>

    <?php include __DIR__ . '/../partials/errorlist.tpl.php'; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="exampleInputEmail1">Adresse email</label>
            <input
                type="email"
                class="form-control"
                id="exampleInputEmail1"
                aria-describedby="emailHelp"
                name="email"
                placeholder="Votre adresse email"
                value="<?= isset($errorAppUser) ? $errorAppUser->getEmail() : ''; ?>"
            >
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Mot de passe</label>
            <input
                type="password"
                class="form-control"
                id="exampleInputPassword1"
                name="password"
                placeholder="Votre mot de passe"
            >
        </div>
        <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>" />
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
</div>