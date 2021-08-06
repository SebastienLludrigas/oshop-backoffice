<?php if (!empty($errorList)) : ?>
    <?php foreach ($errorList as $errorText) : ?>

        <div class="alert alert-danger" role="alert">
            <?= $errorText; ?>
        </div>

    <?php endforeach; ?>
<?php endif; ?>